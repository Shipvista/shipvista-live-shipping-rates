<?php

namespace Shipvista\Rates;

use Shipvista\Functions\SLSR_WcShipvistaFunctions;

trait SLSR_WcShipvistaRates
{
    public $shippingList = [];
    public $shippingRateSuccess = false;
    public $shippingToCountry = '';
    public $shippingToPostcode = '';

    public function getShippingRates($package = [], $isAdmin = false)
    {

        $destination = $package['destination'];
        $postcode = @$destination['postcode'];
        $address = [];
        if (isset($_COOKIE['shipvista_temp_address']) && empty($postcode)) {
            $address = json_decode(stripslashes(sanitize_text_field($_COOKIE['shipvista_temp_address'])), true);
        }
        if ($isAdmin == true) {
            $destinationAddress = $destination;
        } else {

            $destinationAddress = [
                'postalCode' =>   preg_replace('#[^a-zA-Z0-9]#i', '', (str_replace(' ', '', $destination['postcode']) ?: @$address['postcode'])),
                'countryCode' => strtoupper(@$destination['country'] ?? @$address['country'] ?? $this->get_option('shipvista_origin_country')),
                'state' => ($destination['state'] ?: ''),
                'city' => ($destination['city'] ?: ''),
                'streetAddress' => (substr($destination['address'], 0, 50) ?: ''),
                'streetAddress2' => (substr($destination['address_2'], 0, 50) ?: ''),
                'residential' => true
            ];
        }

        // set global variables to use for restrictions
        $this->shippingToCountry = $destinationAddress['countryCode'];
        $this->shippingToPostcode = $destinationAddress['postalCode'];


        $fromAddress = $this->getShipFromAddress();

        if ($fromAddress != false) {
            if (strlen($destinationAddress['postalCode']) > 0) {
                $itemList = $this->shippingItemList($package, $isAdmin);
                $apiObject = [
                    'FromAddress' => $fromAddress,
                    'ToAddress' => $destinationAddress,
                    "unitOfMeasurement" => "IMPERIAL",
                    "currency" => $this->get_option('shipvista_user_currency') ?? $this->get_option('woocommerce_currency') ?? 'USD',
                    "lineItems" => $itemList
                    //"carrierServiceTypeList" => []
                ];

                // do check to prevent throttling
                $session = $this->checkSessionThrottle($apiObject);

                $shippingList = [];
                $rateResults = [];

                if (!$session) {
                    $rateResults =  $this->shipvistaApi('/rate/', $apiObject);
                } else {
                    $rateResults = $session;
                }

                if (array_key_exists('status', $rateResults) && $rateResults['status'] == true && count($rateResults['data']) > 0) {
                    // set various session variables

                    if (!$session) {
                        try {
                            WC()->session->set("sv_session_request", json_encode($rateResults));
                            WC()->session->set("sv_session_time", time());
                            WC()->session->set("sv_session_response", json_encode($rateResults));
                        } catch (Exception $e) {
                            $this->pluginLogs('sessions', 'Could not create sessions :: ' . $e->getMessage());
                        }
                    }

                    $shippingList = $this->structureShippingRates($rateResults['data'], $isAdmin);
                    $this->shippingRateSuccess = true;
                } else {
                    // use fall back rate shipping rate
                    $shippingList = $this->getDefaultRates();
                    $this->rex = $rateResults;
                }
                return $shippingList;
            } else {
                return $this->getDefaultRates(true);
            }
        } else {
            return $this->getDefaultRates();
        }
    }

    /**
     * Check request to prevent multiple same request to be send over and over
     * Returns either false or an array
     */
    function checkSessionThrottle(array $request)
    {

        $reqeustString = json_encode($request);
        // cehck if session isset
        $return = false;
        $session = WC()->session->get("sv_session_request");
        if ($session) {
            $sessionTime = WC()->session->get("sv_session_time");
            $sessionTime = (int) $sessionTime;
            $timeNow = time();
            // check if its been a minute
            $minute = round(abs($timeNow - $sessionTime) / 60, 2);
            $this->timmer = $minute . ' == ' . date('h:i:sa', strtotime($sessionTime)) . ' -- ' . date('h:i:sa', $timeNow);
            if ($minute < 0.5) {
                // get the stored response
                $storeResponse = WC()->session->get("sv_session_response");
                if ($storeResponse) {
                    $return = json_decode($storeResponse, true) ?: false;
                }
            }
        }

        return $return;
    }

    /**
     * Get order shipping item list
     */
    public function shippingItemList($package, $isAdmin = false)
    {

        global $woocommerce;
        if ($isAdmin == false) {
            $items = $woocommerce->cart->get_cart();
        } else {
            $items = $package['items'];
        }
        $shippingList = [];
        $totalQuantity = 0;

        foreach ($items as $item => $values) {

            $quantity = @$values['quanity'] ?: 1;
            $_product =  wc_get_product(($isAdmin == true ? $values->get_product_id() : $values['data']->get_id()));
            if (!$_product) {
                continue;
            }
            $price = $_product->get_price(); // get_post_meta($values['product_id'] , '_price', true);
            $title = $_product->get_title();
            $length = $_product->get_length() ?: ($this->get_option('shipvista_dimension_length') > 0 ?  $this->get_option('shipvista_dimension_length') :  2);
            $width = $_product->get_width() ?: ($this->get_option('shipvista_dimension_width') > 0 ?  $this->get_option('shipvista_dimension_width') :  2);
            $height = $_product->get_height() ?: ($this->get_option('shipvista_dimension_height') > 0 ?  $this->get_option('shipvista_dimension_height') :  1);
            $weight = $_product->get_weight() ?: ($this->get_option('shipvista_dimension_weight') > 0 ?  $this->get_option('shipvista_dimension_weight') :  1);
            $weight *= $quantity;

            // set weight max to 30kg
            if ($weight > 30) {
                $weight = 30;
            }

            $shippingList[] = [
                'length' => (float) $length,
                'width' => (float) $width,
                'height' => (float) $height,
                'weight' => (float) $weight,
                'declaredValue' => [
                    'currency' => $this->get_option('shipvista_user_currency'),
                    'amount' => ($price * $quantity ?: 0)
                ],
                'description' => $title
            ];
            $totalQuantity += $quantity;
        }
        $this->shippingList['list'] = $shippingList;
        $this->shippingList['totalQuantity'] = $totalQuantity;

        return $shippingList;
    }


    function structCarriers(&$carries = [])
    {
        foreach ($carries as $key => $carrier) {
            $carries[$key] = str_replace(' ', '', strtolower($carrier));
        }
    }

    function structureRestrictions(string $country_code)
    {
        $restrictions = strtoupper(str_replace(' ', '', preg_Replace('#[^a-zA-Z0-9\,\:\|]#i', '', $this->get_option('shipvista_restricted_locations'))));
        if (strlen($restrictions) > 0) {
            $countryExp = explode('|', $restrictions);
            foreach ($countryExp as $countryEl) {
                $expCountry = explode(':', $countryEl);
                $country_code = strtoupper($country_code);
                if ($expCountry[0] == $country_code && count($expCountry) > 1) {
                    $postalcodes = explode(',', $expCountry[1]);
                    return $postalcodes;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * Structure shipping rate to get rates for display
     */
    public function structureShippingRates($rates = '', $isAdmin = false)
    {

        $shippingList = [];
        if (is_array($rates) || is_object($rates)) {
            // get added margin
            $shippingMargin = 1;
            $handlingTime =  $this->get_option('shipvista_handling_time') ?: 0;

            if ($isAdmin == false) {
                $shippingMargin = (float) $this->get_option('shipvista_rate_margin');
                if ($shippingMargin > 0) {
                    $shippingMargin = 1 + (round(($shippingMargin / 100), 2));
                } else {
                    $shippingMargin = 1;
                }

                // check to see if user has restrictions
                $restrictions = $this->structureRestrictions($this->shippingToCountry);
                $canApplyDiscount = true;
                if ($restrictions) {
                    $mainPostcode = strtoupper($this->shippingToPostcode);
                    foreach ($restrictions as $postcode) {
                        if (substr($mainPostcode, 0, strlen($postcode)) == $postcode) {
                            $canApplyDiscount = false;
                            break;
                        }
                    }
                }


                $isFreeShipping =  $this->get_option('shipvista_free_shipping');
                $freeShippingMax = (float) $this->get_option('shipvista_free_max_amount');
                $handlingTime = ((float) $this->get_option('shipvista_free_shipping_days') ?: 0) + $handlingTime;






                if ($isFreeShipping == 1 && $freeShippingMax == '' && $canApplyDiscount == true) {
                    $shippingList[] = [
                        'id' => 'shipvista_free',
                        'label' =>   'Free shipping' . ($handlingTime > 0 ? ': in ' . $handlingTime . ' day' . ($handlingTime > 1 ? 's' : '')  : ''),
                        'cost' => 0,
                        'transit' => $handlingTime,
                    ];
                }
            } else {
                $isFreeShipping = 'no';
            }

            if (count($rates) > 0) {


                // get a list of check methods allowed
                $activeShippingRates = $this->getActiveCarrierMethods();
                $activeCarriers = array_keys($activeShippingRates);

                $this->structCarriers($activeCarriers);

                $labelList = [];
                foreach ($rates as $rate) {
                    $rate = (array) $rate;
                    $carrierName = trim(strtolower($rate['shippingCarrierAccount']['carrier_code']));

                    if (in_array($carrierName, $activeCarriers)) {
                        $serviceName = trim(str_replace(['USA', 'CAD'], '', $rate['shippingService']['name'])); //  reset for usa and canda
                        $transit = (float) $rate['shippingService']['expectedTransitTime'];
                        $transit += $handlingTime;

                        foreach ($activeShippingRates as  $service) {
                            // count the service rates

                            if (in_array(strtolower($serviceName), array_map('strtolower', array_map('trim', $service)))) {
                                if (in_array($serviceName, $labelList)) {
                                    continue;
                                }
                                array_push($labelList, $serviceName);

                                $rateAmount = $rate['shipmentCharges']['totalCharge']['amount']  * $shippingMargin;
                                // free shipping computation
                                $freeRate = 0;
                                $originalRate = $rateAmount;

                                if ($isAdmin == false &&  $isFreeShipping == 'yes' && $freeShippingMax > 0 && $canApplyDiscount == true) {
                                    // free shipping 100% only work on regular parcels all other services 10% of discount
                                    $subtract = (float) round(($freeShippingMax * 0.1), 2);

                                    // check to make sure regular shipment has 100% 
                                    if (strpos('_ ' . strtolower($serviceName), 'regular')) {
                                        $subtract = $freeShippingMax;
                                    }

                                    $rateAmount -= $subtract;

                                    $freeRate = 100;
                                    if ($rateAmount < 1) {
                                        $rateAmount = 0;
                                    } else {
                                        $freeRate = (round($freeShippingMax / ($rateAmount + $freeShippingMax) * 100) ?: 1);
                                    }
                                }

                                // free shipping computation

                                $list = [
                                    'id' => 'shipvista_' . $rate['shippingService']['code'],
                                    'label' => $serviceName,
                                    'cost' => (float)$rateAmount,
                                    'transit' => $transit,
                                    'free' => $isFreeShipping,
                                    'rate' => $freeRate,
                                    'realRate' => $originalRate,

                                ];
                                //die(var_dump($list));
                                if ($isAdmin == true) {
                                    $list['code'] = $rate['shippingService']['code'];
                                    $list['carrierId'] = @$rate['shippingCarrierAccount']['id'];
                                    $list['options'] = @$rate['options'];
                                }

                                $shippingList[] = $list;
                            }
                        }
                    }
                }


                if (count($shippingList) > 0) {
                    $this->getRateRanking($shippingList);
                    return $shippingList;
                } else {
                    $this->pluginLogs('Rates', ' Rates gotten successfully but does not match specification in settings, fall back rate loaded');
                    return $this->getDefaultRates();
                }
            } else {
                $this->pluginLogs('Rates', ' Could not get rates, fall back rate loaded');
                return $this->getDefaultRates();
            }
        } else {
            $this->pluginLogs('Rates', ' Invalid rate object, fall back rate loaded');
            return $this->getDefaultRates();
        }
    }



    public function getRateRanking(array &$rates)
    {
        $winners = ['cheapest' => '', 'fastest' => '', 'recommended' => ''];
        $price = 0;
        $transit = 0;
        $recommend = 0;
        $newArr = [];
        foreach ($rates as $key => $rate) {
            if ($key == 0) {
                $winners = ['cheapest' => $key, 'fastest' => $key];
                $price = $rate['cost'];
                $transit = $rate['transit'];
            } else {
                $winnerList = array_values($winners);
                // get cheapest
                if ($rate['cost'] <= $price && !in_array($key, $winnerList)) {
                    $winners['cheapest'] = $key;
                    $price = $rate['cost'];
                }

                if ($rate['transit'] <= $transit  && !in_array($key, $winnerList)) {
                    $winners['fastest'] = $key;
                    $transit = $rate['transit'];
                }
            }
        }

        $price = 0;
        $transit = 0;
        if (count($rates) > 2) {
            foreach ($rates as $key => $rate) {
                if ($key == $winners['cheapest'] || $key == $winners['fastest']) {
                    continue;
                }


                if ($price == 0) {
                    $price = ($rate['cost']  + 1);
                }
                // get cheapest
                if ($rate['cost'] < $price) {
                    $winners['recommended'] = $key;
                    $price = $rate['cost'];
                    $transit = $rate['transit'];
                }
                // get cheapest
            }
        }


        $rates[$winners['fastest']]['label'] = 'Fastest:  ' .  $rates[$winners['fastest']]['label'] . ' - ' . $rates[$winners['fastest']]['transit'] . ' day' . ($rates[$winners['fastest']]['transit'] > 1 ? 's' : '');
        if ($winners['fastest'] != $winners['cheapest']) {
            $rates[$winners['cheapest']]['label'] = 'Cheapest: ' . $rates[$winners['cheapest']]['label'] . ' - '  . $rates[$winners['cheapest']]['transit'] . ' day' . ($rates[$winners['cheapest']]['transit'] > 1 ? 's' : '');
        }
        if (count($rates) > 2) {
            $rates[$winners['recommended']]['label'] = 'Recommended:  ' .  $rates[$winners['recommended']]['label'] . ' -  '  . $rates[$winners['recommended']]['transit'] . ' day' . ($rates[$winners['recommended']]['transit'] > 1 ? 's' : '');
            if ($winners['recommended'] !== '') {
                $newArr[] = $rates[$winners['recommended']];
            }
        }
        $newArr[] = $rates[$winners['cheapest']];
        $newArr[] = $rates[$winners['fastest']];


        foreach ($rates as $key => $rate) {
            if ($key == $winners['cheapest'] || $key == $winners['recommended'] || $key == $winners['fastest']) {
                continue;
            }
            $rate['label'] =  $rate['label'] . ' - '  . $rate['transit'] . ' day' . ($rate['transit'] > 1 ? 's' : '');
            $newArr[] =  $rate;
        }

        // handle pickup
        $isPickup = $this->get_option('shipvista_pickup');
        if ($isPickup == 'yes') {
            $pickupAddress = $this->get_option('shipvista_pickup_address');
            $pickupNote = $this->get_option('shipvista_pickup_note');
            $newArr[] = [
                'id' => 'shipvista_pickup',
                'label' =>   'Pickup : ' . $pickupNote . ' @Address ' . $pickupAddress,
                'cost' => 0,
                'transit' => 1,
            ];
        }

        $rates = $newArr;
    }
}
