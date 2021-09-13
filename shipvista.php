<?php

/**
 * Plugin Name: Shipvista live shipping rates
 * Plugin URI: https://github.com/Shipvista/shipvista-live-shipping-rates
 * Description: Display live shipping rates to customers on cart/checkout pages, print labels, and track orders with Shipvista's free live shipping rates plugin. Fully customizable to suit your every shipping needs. 
 * Author:  Shipvista
 * Author URI: http://www.shipvista.com
 * Version: 1.0.0
 * Tags: shipping, delivery, logistics, woocomemrce, free shipping, live rates, canada post, shipvista
 * Requires at least: 5.0
 * Tested up to: 5.8.1
 * Stable tag: 1.0.0
 * Requires PHP: 7.0
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * woo:
 */


use Shipvista\Forms\SLSR_WcShipvistaForms;
use Shipvista\Functions\SLSR_WcShipvistaFunctions;
use Shipvista\Rates\SLSR_WcShipvistaRates;

defined('ABSPATH') or die('WORDPRESS ERROR');
define('SHIPVISTA__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SHIPVISTA__PLUGIN_URL', plugin_dir_url(__FILE__));
define('SHIPVISTA__PLUGIN_SITE', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]");
define('SHIPVISTA__PLUGIN_FILE', __FILE__);
define('SHIPVISTA__PLUGIN_VERSION', '1.0.0');
define('SHIPVISTA__PLUGIN_SLUG', 'wc-settings'); 

if (!defined('WPINC')) {
  die('security by preventing any direct access to your plugin file');
}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {



  require_once SHIPVISTA__PLUGIN_DIR . 'inc/wc_shipvista_forms.php';
  require_once SHIPVISTA__PLUGIN_DIR . 'inc/wc_shipvista_shipping_rates.php';
  require_once SHIPVISTA__PLUGIN_DIR . 'inc/wc_shipvista_functions.php';

  function SLSR_Shipvista()
  {


    if (isset($_GET['wcs_action']) || isset($_POST['wcs_action'])) {
      require_once SHIPVISTA__PLUGIN_DIR . 'inc/wc_shipvista_actions.php';
    }

    if (!class_exists('SLSR_Shipvista')) {

      class SLSR_Shipvista extends WC_Shipping_Method
      {
        use SLSR_WcShipvistaFunctions;
        use SLSR_WcShipvistaForms;
        use  SLSR_WcShipvistaRates;
        public $logStatus = false;

        public function __construct()
        {
          global $woocommerce;
          //WC()->session = new WC_Session_Handler();
          //WC()->session->init();

          $this->id = 'shipvista';
          $this->method_title = __('Shipvista live shipping rates', 'shipvista');
          $this->method_description = __('Display live shipping rates to customers on cart/checkout pages, print labels and track order with Shipvista\'s free live shipping rates plugin. Fully customizable to suit your every shipping needs.', 'shipvista');
          // Contries availability
          $this->init();
          // $this->init_settings();
          $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'no';
          $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Shipvista live shipping rates', 'shipvista');
          $this->checkToken();

          // check if the user has enabled google api auto fill loation

        }


        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init()
        {
          // global $post;

          // Load the settings API
          $this->init_form_fields();
          $this->init_settings();
          // add sidebar widget to post
          $this->logStatus = get_option('shipvista_log_status') == 'yes' ? true : false;

          // Save settings in admin if you have any defined
          add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
        }



        /**
         * Control admin options
         */

        function admin_options()
        {
          require_once SHIPVISTA__PLUGIN_DIR . '/inc/wc_shipvista_bootstrap.php';
          $this->pluginLink =  menu_page_url(SHIPVISTA__PLUGIN_SLUG, false) . '&tab=shipping&section=shipvista';
          new SLSR_WcShipvistaBootstrap();
        }




        /**
         * This function is used to calculate the shipping cost. Within this function we can check for weights, dimensions and other parameters.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping($package = array())
        {

          // include get available shipping rates
          $rateList = $this->getShippingRates($package);

          $this->rateList = $rateList;
          // include get available shipping rates
          foreach ($rateList as $rateObject) {
            $rateObject['meta'] = [];
            unset($rateObject['rate']);
            unset($rateObject['free']);
            unset($rateObject['transit']);
            unset($rateObject['realRate']);
            $this->SLSR_pluginLogs('rateList_new', json_encode($rateObject));
            $this->add_rate($rateObject);
          }

          global $post;
          global $wp;
          // get the post type
          if (is_object($post) && $post->post_type == 'page') {
            if ($post->post_name == 'checkout' && !isset($wp->query_vars['order-pay'])) {
              if (count($rateList) < 2) {
                wc_add_notice('Enter a valid shipping postal code to proceed', 'error');
                return false;
              }
            }
          }
        }
      }
    }
  }

  add_action('woocommerce_shipping_init', 'SLSR_Shipvista');

  function add_Shipvista($methods)
  {
    $methods[] = 'SLSR_Shipvista';
    return $methods;
  }

  add_filter('woocommerce_shipping_methods', 'add_Shipvista');

  function shipvista_validate_order($posted)
  {

    $packages = WC()->shipping->get_packages();

    $chosen_methods = WC()->session->get('chosen_shipping_methods');
    $choosenMethod = strtolower($chosen_methods[0]);


    if (strtolower($choosenMethod) == 'shipvista') {
      wc_add_notice(__("What's your postal code? It'll help us estimate shipping and delivery. ", "woocommerce"), 'error');
      return false;
    } else {
      if (is_array($chosen_methods) && in_array('shipvista', $chosen_methods)) {

        foreach ($packages as $i => $package) {

          if ($chosen_methods[$i] != "shipvista") {

            continue;
          }

          $Shipvista = new SLSR_Shipvista();
          $rates = $Shipvista->calculate_shipping($package);
        }
      }
    }
  }


  function shipvista_update_order()
  {

    if (isset($_POST['shipvistaLabel_get_label']) && $_POST['shipvistaLabel_get_label'] == 1) {
      $orderId = @$_POST['shipvistaLabel_order_id'] ?: '';
      $code = @$_POST['shipvista_shipping_method'] ?: '';
      $carrierId =  (int) @$_POST['shipvista_shipping_carrier'] ?: '';

      $carrierOptions = (array) @json_decode(stripslashes($_POST['shipvista_shipping_options']), true) ?: [['code' => 'DC']];
      SLSR_Shipvista();
      $Shipvista = new SLSR_Shipvista();

      $response = ['status' => 0, 'message' => ''];
      if (strlen($code) > 2 && $orderId > 0) {
        $code = str_replace('.', '_', $code);
        // global $woocommerce;
        $label = (string) wc_get_order_item_meta($orderId, 'shipvista_shipment_label') ?: '';

        if (strlen($label) < 5) {

          $order = wc_get_order($orderId);
          $fromAddress = $Shipvista->getShipFromAddress();
          $fromAddress['stateCode'] = $fromAddress['state'];
          $fromAddress['phone'] = $Shipvista->get_option('shipvista_origin_phone_number') ?: '1111111111';
          $fromAddress['companyName'] = $Shipvista->get_option('shipvista_user_name') ?: 'Seller';
          unset($fromAddress['state']);
          unset($fromAddress['country']);

          $toAddress = $Shipvista->orderShippingAddress($order);
          $toAddress['stateCode'] = $toAddress['state'];
          $toAddress['companyName'] = $order->get_shipping_company() ?: 'Customer';
          $toAddress['name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
          $toAddress['phone'] = $order->get_billing_phone();
          unset($toAddress['state']);
          unset($toAddress['country']);
          $itemList = $order->get_items();
          $orderItems = $Shipvista->shippingItemList(['items' => $itemList], true);

          if (strtolower($fromAddress['countryCode']) == strtolower($toAddress['countryCode'])) {
            $requestobject = array(
              'rateRequest' =>
              array(
                // 'CarrierServiceList' =>
                // array(
                //   0 => $code,
                // ),
                'FromAddress' => $fromAddress,
                'ToAddress' => $toAddress,
                'lineItems' => $orderItems,
                'unitOfMeasurement' => 'METRIC',
                'serviceOptions' => $carrierOptions,

              ),
              'shippingCarrierAccountId' => $carrierId,
              'accountType' => 'ShipVistaAccount',
              'carrierServiceType' => $code,
              // 'shipDate' => '2020-12-28',
              'ShipmentLabelSize' => 'A4',
            );
          } else {

            $customItems = $Shipvista->customShippingItems($itemList);

            $requestobject = [
              'carrierServiceType' => $code,
              'rateRequest' => [
                'FromAddress' => $fromAddress,
                'ToAddress' => $toAddress,
                'lineItems' => $orderItems,
                'unitOfMeasurement' => 'METRIC',
              ],
              'shippingCarrierAccountId' => $carrierId,
              'accountType' => 'ShipVistaAccount',
              'shipOptions' => $carrierOptions,
              // 'shipDate' => '2020-12-28',
              'ShipmentLabelSize' => 'A4',
              'CustomDetails' => [
                'Currency' => ($Shipvista->countryCurrencies[$toAddress['country']] ?: 'USD'),
                'ReasonForExport' => 'SAM',
                'SKUList' => [
                  'Item' => $customItems,
                ],
              ],
            ];
          }

          $result = $Shipvista->shipvistaApi('/Shipments', $requestobject, 'POST');

          // send email to customer that their label has been printed successfully
          if ($result['status'] == true && isset($result['data'])) {
            $data = $result['data'];
            $trackingNumber = $data['trackingNumbers'][0];
            $label = $data['labelUrl'];
            $shipmentId = $data['shipmentId'];


            wc_add_order_item_meta($orderId, 'shipvista_shipment_label', $label);
            wc_add_order_item_meta($orderId, 'shipvista_tracking_number', $trackingNumber);
            wc_add_order_item_meta($orderId, 'shipvista_carrier_id', $carrierId);
            wc_add_order_item_meta($orderId, 'shipvista_shipment_id', $shipmentId);
            $Shipvista->SLSR_pluginLogs('get_labels', 'Label success: File: ' . $label . "\nTracking: " . $trackingNumber);
            // send email to customer of shippment sent
            $emailTemplate = include_once(SHIPVISTA__PLUGIN_DIR . "/assets/emails/order_label_print.php");
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
            $emailTemplate = str_replace(['{orderId}', '{days}', '{trackingnumber}', '{site}'], [$orderId, 'in a couple of days.', $trackingNumber, $url], $emailTemplate);
            // send email
            $Shipvista->SLSR_pluginLogs('get_label_', $order->get_billing_email() . "\n" . '[LABEL] Your parcel is on the way.' . "\n" . $emailTemplate);
            wp_mail($order->get_billing_email(), '[LABEL] Your parcel is on the way.', $emailTemplate);

            // send email to customer of shippment sent
            // add order note
            $note = __("$carrierId Label obtained successfully for order $orderId, with tracking number $trackingNumber");
            $order->add_order_note($note);
          } else {
            $Shipvista->SLSR_pluginLogs('get_labels', 'Error creating label');
            $Shipvista->SLSR_pluginLogs('get_labels', json_encode($result));
          }
          // send email to customer that their label has been printed successfully

        }
        remove_action('woocommerce_update_order', 'shipvista_update_order', 10);
        $response['status'] = 200;
        $response['status'] = 'Label gotten successfully';
      } else {
        $response['message'] = 'Could not get label. invalid Order Id or shipping option';
      }


      $message = sprintf(__($response['message'] . ' for %s', 'shipvista'), $Shipvista->title);
      $messageType = $response['status'] == 200 ? 'success' : "error";
      if (function_exists('wc_has_notice') && !wc_has_notice($message, $messageType)) {
        wc_add_notice($message, $messageType);
      }
    }
  }

  add_action('admin_enqueue_scripts',  'enqueue');
  function enqueue()
  {
    global $post;
    if (isset($_GET['section']) && $_GET['section'] == 'shipvista' || (isset($post->post_type) && $post->post_type == 'shop_order')) {
      wp_enqueue_style('shipvista_plugin_styles', plugins_url('assets/css/shipvista_admin_style.css', __FILE__));
      wp_enqueue_style('shipvista_plugin_stylesw', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
      wp_enqueue_style('shipvista_plugin_styles_full', plugins_url('assets/css/shipvista_style.css', __FILE__));
      wp_enqueue_script('shipvista_plugin_scripts2', plugins_url('assets/js/shipvista_admin_panel.js', __FILE__));
    }
    if (isset($post->post_type) && $post->post_type == 'shop_order') {
      wp_enqueue_style('shipvista_plugin_styles_front', plugins_url('assets/css/shipvista_style.css', __FILE__));
    }
  }

  function SLSR_include_front_end()
  {
    global $post;
    if (is_object($post) && $post->post_type == 'page') {
      if ($post->post_name == 'checkout' || $post->post_name == 'cart') {
        wp_enqueue_style('shipvista_plugin_styles_front', plugins_url('assets/css/shipvista_style.css', __FILE__));
        wp_register_script('shipvista_plugin_scripts_frontend', plugins_url('assets/js/shipvista_panel.js', __FILE__));
        wp_enqueue_script('shipvista_plugin_scripts_frontend');
        wp_localize_script('shipvista_plugin_scripts_fronend', 'my_ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
        ?>
        <script>
          var loaderContainer = `
            <div class="sv_LoaderWrap">
              <div class="sv_LoaderCont">
              <img src="<?php echo  esc_attr(SHIPVISTA__PLUGIN_URL); ?>assets/img/loader.gif" class="sv_loaderImg">
              </div>
            </div>
            `;
        </script>
        <?php
      }
    }
    // if (is_object($post) && $post->post_type == 'page') {
    // }
  }
  add_action('wp_enqueue_scripts', 'SLSR_include_front_end');
  add_action('wp_ajax_nopriv_shipvista_postcode', 'SLSR_Shipvista_postcode');

  add_action('wp_footer', 'SLSR_shipvista_custom_scripts');
  function SLSR_shipvista_custom_scripts()
  {
    global $post;
    if (is_object($post) && $post->post_type == 'page') {
      if ($post->post_name == 'checkout') {
        SLSR_Shipvista();
        $ship = new SLSR_Shipvista;
        if ($ship->get_option('shipvista_google_places_api') == 'yes' && strlen($ship->get_option('shipvista_google_places_api_key')) > 10) { ?>



          <script async src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr($ship->get_option('shipvista_google_places_api_key')); ?>&libraries=places&callback=initMap">
          </script>

          <script>
            // script for auto complete address
            let autocomplete;
            let shippingForm = 'billing';

            function initMap() {


              const center = {
                lat: 50.064192,
                lng: -130.605469
              };
              // Create a bounding box with sides ~10km away from the center point
              const defaultBounds = {
                north: center.lat + 0.1,
                south: center.lat - 0.1,
                east: center.lng + 0.1,
                west: center.lng - 0.1,
              };
              const input = document.getElementById(shippingForm + "_address_1");
              var country = document.getElementById(shippingForm + "_country").value;

              const options = {
                //bounds: defaultBounds,
                componentRestrictions: {
                  country: [country]
                },
                fields: ["address_components", "place_id", "geometry", "icon", "name"],
                //origin: center,
                //strictBounds: false,
                types: ["address"],
              };
              autocomplete = new google.maps.places.Autocomplete(input, options);
              autocomplete.addListener("place_changed", onPlaceChanged);
              // add event listener on country change


            }

            jQuery(function($) {
              $(document.body).on('change', 'select[name=billing_country]', function() {
                // Here run your function or code
                initMap();
              });

              $(document.body).on('change', 'select[name=shipping_country]', function() {
                // Here run your function or code
                initMap();
              });

              document.getElementById('ship-to-different-address-checkbox').addEventListener('change', function() {
                if (this.checked) {
                  shippingForm = 'shipping';
                } else {
                  shippingForm = 'billing';
                }
                initMap();
              });


            });

            function onPlaceChanged() {
              // Get the place details from the autocomplete object.
              const place = autocomplete.getPlace();
              let address1 = "";
              let postcode = "";
              let state = '';
              // Get each component of the address from the place details,
              // and then fill-in the corresponding field on the form.
              // place.address_components are google.maps.GeocoderAddressComponent objects
              for (const component of place.address_components) {
                const componentType = component.types[0];

                switch (componentType) {
                  case "street_number": {
                    address1 = `${component.long_name} ${address1}`;
                    break;
                  }

                  case "route": {
                    address1 += component.short_name;
                    break;
                  }

                  case "postal_code": {
                    postcode = `${component.long_name}${postcode}`;
                    document.getElementById(shippingForm + '_postcode').value = postcode.replace(" ", '');
                    break;
                  }

                  case "postal_code_suffix": {
                    postcode = `${postcode}`;
                    if (!postcode) {
                      postcode = `${component.long_name}`;
                    }
                    break;
                  }
                  case "locality":
                    var city = component.long_name;
                    document.getElementById(shippingForm + '_city').value = city;
                    jQuery('body').trigger('country_to_state_changed');
                    break;

                  case "administrative_area_level_1": {
                    state = component.short_name;
                    document.getElementById(shippingForm + "_state").value = state;
                    jQuery('body').trigger('country_to_state_changed');
                    break;
                  }
                  case "country":
                    break;
                }
              }
              //document.getElementById('billing_address_1').value = address1;
              document.getElementById(shippingForm + "_address_1").value = address1;
              document.getElementById(shippingForm + '_address_2').focus();
              jQuery('body').trigger('country_to_state_changed');
              jQuery('body').trigger('update_checkout');
              // After filling the form with address components from the Autocomplete
              // prediction, set cursor focus on the second address line to encourage
              // entry of subpremise information such as apartment, unit, or floor number.
              //address2Field.focus();
              //sv_supmitPostalCode();
            }
            // write code here
          </script>

    <?php }
      }
    }
  }

  function SLSR_Shipvista_postcode()
  {

    $code = strtoupper(@$_POST['shipvista_get_postal']);
    $country = @preg_replace('#[^a-zA-Z]#i', '', strtoupper($_POST['shipvista_get_country']));
    $response = ['status' => false, 'message' => 'invalid postal code.'];
    if (strlen($code) > 3 && strlen($country) == 2) {
      $destinationAddress = [
        'postcode' =>  $code,
        'country' => $country,
      ];

      SLSR_Shipvista();
      $s = new SLSR_Shipvista();

      $rates = $s->getShippingRates(['destination' => $destinationAddress]);

      $response['result'] = $rates;
      $response['og'] = $s->rex;
      if ($s->shippingRateSuccess == true || count($rates) > 0) {
        $response['html'] = str_replace('wide sv_border-top pt-3', ' d-none ',  $s->structOrderFieldList($rates, ''));
        $response['status'] = true;
        $response['message'] = 'Rates obtained successfully.';
      } else {
        $response['message'] = 'Could not find any shipping rate for the postal code entered.';
      }
    }

    echo json_encode($response);
    exit;
  }

  function SLSR_shipvista_meta_box_callback()
  {
    global $woocommerce;
    /* Get the user details to find user id for whom this order should be shown. Ideally, I believe it will be admin user. Make sure you change the email id*/
    $orderId = $_GET['post'];
    // include payment get_included_files
    // we need it to get any order detailes
    $order = wc_get_order($orderId);
    if ($order != false) {
      $user = $order->get_user();
      $shipping = $order->get_shipping_method();
      try {
        SLSR_Shipvista();
        $s = new SLSR_Shipvista();

        $package = [
          'destination' => $s->orderShippingAddress($order),
          'items' => $order->get_items()
        ];

        $rates = $s->getShippingRates($package, true);
        $s->orderId = $orderId;
        echo  esc_html($s->structOrderFieldList($rates, $shipping));

        update_user_option($user->ID, "meta-box-order_page", $order, true);
        update_user_option($user->ID, "meta-box-order_post", $order, true);
      } catch (Exception $e) {
        echo esc_html('Could not obtain order infomation.');
      }
    } else {
      echo  esc_html('Could not obtain order infomation.');
    }

    echo  esc_html('<p>You can log in to your Shipvista account to manage your orders, print labels, and track every step of the process.</p> <a href="https://shipvista.com/CreateShipment" target="_blank" class="sv_btn sv_btn-primary sv_btn-sm" >Create Label</a>');
  }
  function shipvista_add_meta_box()
  {

    $screens = array('shop_order');

    foreach ($screens as $screen) {
      add_meta_box(
        'shipvistaWidget',
        __('Print label - Shipvista.com', 'shipvista_textdomain'),
        'shipvista_meta_box_callback',
        $screen,
        'side'
      );
    }
  }
  // add_action('add_meta_boxes', 'SLSR_Shipvista_add_meta_box', 2); For subsequent release



  // Sendfeed back email
  add_action('updated_option', 'SLSR_Shipvista_feedback', 10, 3);
  function SLSR_Shipvista_feedback($fields)
  {
    if (isset($_POST['shipvista_feedback'])) {
      $feedback = preg_replace('#[^a-zA-Z0-9 \,\.\']#i', '', $_POST['shipvista_feedback']);
      // remove the post variable so we dont get duplicates
      unset($_POST['shipvista_feedback']);

      if (strlen($feedback) > 3) {
        //add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
        $subject = 'Shipvista Plugin feedback from ' . SHIPVISTA__PLUGIN_SITE;
        $email = 'developers@shipvista.com';
        // Get woocommerce mailer from instance
        $mailer = WC()->mailer();

        // Wrap message using woocommerce html email template
        $heading = '';
        $message = $feedback;
        $wrapped_message = $mailer->wrap_message($heading, $message);

        // Create new WC_Email instance
        $wc_email = new WC_Email;

        // Style the wrapped message with woocommerce inline styles
        $html_message = $wc_email->style_inline($wrapped_message);
        $headers = array('Content-Type: text/html; charset=UTF-8');
        // Send the email using wordpress mail function
        $mail = wp_mail($email, $subject, $html_message, $headers);

        if ($mail) {
          add_action('admin_notices',  'SLSR_feedback_success');
        } else {
          add_action('admin_notices',  'SLSR_feedback_fail');
        }
        return false;
      } else {
        add_action('admin_notices',  'SLSR_feedback_error');
        return false;
      }
    }
  }

  function SLSR_feedback_success()
  {
    ?>
    <div class="notice notice-success is-dismissible">
      <p><?php _e('Thanks for sending us your feedback', 'shipvista-feedback-success'); ?></p>
    </div>
  <?php
  }

  function SLSR_feedback_error()
  {
    $class = 'notice notice-error';
    $message = __('Please enter a valid feedback of at least 4 characters.', 'shipvista-feedback-success');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }


  function SLSR_feedback_fail()
  {
    $class = 'notice notice-error';
    $message = __('Could not send your feedback. Please check to make sure your emailing system is working properly.', 'shipvista-feedback-success');

    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
  }



  $wppstp1_version = '1.0.0';

  add_action('upgrader_process_complete', 'SLSR_Shipvista_update', 10, 2); // will work only this plugin activated.
  function SLSR_Shipvista_update(\WP_Upgrader $upgrader_object, $hook_extra)
  {
    global $wppstp1_version;

    if (is_array($hook_extra) && array_key_exists('action', $hook_extra) && array_key_exists('type', $hook_extra) && array_key_exists('plugins', $hook_extra)) {
      // check first that array contain required keys to prevent undefined index error.
      if ($hook_extra['action'] == 'update' && $hook_extra['type'] == 'plugin' && is_array($hook_extra['plugins']) && !empty($hook_extra['plugins'])) {
        // if this action is update plugin.
        $this_plugin = plugin_basename(__FILE__);

        foreach ($hook_extra['plugins'] as $each_plugin) {
          if ($each_plugin == $this_plugin) {
            // if this plugin is in the updated plugins.
            // don't process anything from new version of code here, because it will work on old version of the plugin.
            // set transient to let it run later.
            set_transient('shipvista_updated', 1);
          }
        } // endforeach;
        unset($each_plugin);
      } // endif update plugin and plugins not empty.
    } // endif; $hook_extra
  } // shipvista_update



  add_action('plugins_loaded', 'SLSR_Shipvista_runUpdatePlugin');
  function SLSR_Shipvista_runUpdatePlugin()
  {

    if (get_transient('shipvista_updated') && current_user_can('manage_options')) {
      // if plugin updated and current user is admin.
      // update code here.

      // delete transient.
      delete_transient('shipvista_updated');
    }
  } // shipvista_runUpdatePlugin

  //add_filter('plugin_action_links_nelio-content/nelio-content.php', 'nc_settings_link');

  /**
   * Set plugin links
   */
  add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'shipvista_action_links');
  function shipvista_action_links($links)
  {
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'admin.php?page=wc-settings&tab=shipping&section=shipvista')) . '">Settings</a>';
    return $links;
  }

  add_action('woocommerce_after_checkout_validation', 'shipvista_validate_order', 10);
  add_action('woocommerce_update_order', 'shipvista_update_order', 10);
}
