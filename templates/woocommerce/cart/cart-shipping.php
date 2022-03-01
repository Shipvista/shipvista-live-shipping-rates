<?php

/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

$formatted_destination    = isset($formatted_destination) ? $formatted_destination : WC()->countries->get_formatted_address($package['destination'], ', ');
$has_calculated_shipping  = !empty($has_calculated_shipping);
$show_shipping_calculator = !empty($show_shipping_calculator);
$calculator_text          = '';
?>
<tr class="woocommerce-shippinEnter your address to view shipping options.g-totals shipping">
    <!-- <th colspan="2"><?php echo wp_kses_post($package_name); ?></th>
    <td colspan="2" data-title="<?php echo esc_attr($package_name); ?>"> -->
    <!-- <tr> -->
    <th colspan="2">
        <?php if ($available_methods) : ?>
            <?php $list = '';
            $in = 0;
            foreach ($available_methods as $method) : $in++;
                $hideListClass = '';
                if ($in > 3 && !checked($method->id, $chosen_method, false)) {
                    $hideListClass = ' sv_d-none shipvista_list_hide ';
                }

                $meta = $method->get_meta_data();
                $badge = '';
                $transit = '';
                if (isset($meta['attribute'])) {
                    switch ($meta['attribute']) {
                        case 'Fastest':
                            $badge = '<small class="sv_badge sv_badge-warning  sv_text-white">Fastest</small><br>';
                            break;
                        case 'Cheapest':
                            $badge = '<small class="sv_badge sv_badge-success  sv_text-white">Cheapest</small><br>';
                            break;
                        case 'Recommended':
                            $badge = '<small class="sv_badge sv_badge-primary  sv_text-white">Recommended</small><br>';
                            break;
                        default:
                            break;
                    }
                    if ($meta['transit'] > 0) {
                        $transit = ' - ' . $meta['transit'] . ' day' . ($meta['transit'] > 0 ? 's' : '');
                    }
                }

                $listPre = '
                <li class="sv_list-group-item sv_text-left sv_m-0 ' . $hideListClass . '">
                    <div class="sv_d-flex">
                        <div class="sv_px-2 sv_align-self-center">

                            ' .

                    ((1 < count($available_methods)) ?
                        ('<input type="radio" name="shipping_method[' . $index . ']" data-index="' . $index . '" id="shipping_method_' . $index . '_' . esc_attr(sanitize_title($method->id)) . '" value="' . esc_attr($method->id) . '" ' . checked($method->id, $chosen_method, false) . ' class="shipping_method sv_radio" ' . false . ' />') // WPCS: XSS ok.
                        : ('<input type="hidden" name="shipping_method[' . $index . ']" data-index="' . $index . '" id="shipping_method_' . $index . '_' . esc_attr(sanitize_title($method->id)) . '" value="' . esc_attr($method->id) . '" ' . checked($method->id, $chosen_method, false) . ' class="shipping_method sv_radio" />') // WPCS: XSS ok.
                    )
                    . '
                        </div>

                        <div class="sv_flex-fill sv_border-right sv_align-self-center">
                            ' .
                    ('<label for="shipping_method_' . $index . '_' . esc_attr(sanitize_title($method->id)) . '">' . $badge . $method->get_label() . $transit . '</label>') // WPCS: XSS ok.
                    . '

                        </div>
                        <div class="sv_align-self-center sv_pl-2">
                            <b>

                                ' .  ($method->get_cost() > 0 ? get_woocommerce_currency_symbol() . $method->get_cost() : 'Free') . '
                            </b>
                        </div>
                        
                        </div>
                        </li>';

                if (checked($method->id, $chosen_method, false)) {
                    $list = $listPre . $list;
                } else {
                    $list .= $listPre;
                }
                do_action('woocommerce_after_shipping_rate', $method, $index);
            endforeach; ?>

            <?php if (is_cart()) : ?>
                <h4 class="sv_shipping-title">Shipping</h4>

            <?php elseif (is_checkout()) : ?>
                <h4 class="sv_shipping-title">Shipping</h4>

            <?php endif; ?>

            <div id="_shipvistaListingInView"></div>
            <ul id="_shpvistaShippingList" class="sv_list-group sv_mb-3">
                <?php echo $list; ?>

                <?php if ($in > 3) { ?>
                    <li class="sv_list-group-item sv_m-0 sv_text-center sv_text-dark sv_bg-light" onclick="shipvistaToggleViewMoreList()">
                        <a href="javascript:void(0)" class="sv_text-dark "><small id="_shipvistaMoreList">MORE <i class="fa fa-chevron-down"></i></small></a>
                    </li>
                <?php } ?>


            </ul>

            <div class="sv_text-right sv_w-100 "><small style="color:#4B4B4B"><i>Live rates powered by <b>shipvista.com</b></i></small></div>


            <p class="woocommerce-shipping-destination">
                <?php
                if (is_cart()) {
                    if ($formatted_destination) {
                        // Translators: $s shipping destination.
                ?>
                        <a class="sv_float-right sv_btn sv_btn-text shipping-calculator-button sv_text-danger sv_m-0" style="margin-top:-8px !important" onclick="document.getElementsByClassName('woocommerce-shipping-calculator')[0].classList.remove('sv_d-none')"> <small>Update</small></a>
                <?php
                        printf(esc_html__('Shipping to %s.', 'woocommerce') . ' ', '<br><small><strong>' . esc_html($formatted_destination) . '</strong></small>');
                        // $calculator_text ='';// esc_html__('<span class="d-none">Change address</span>', 'woocommerce');
                    } else {
                        echo wp_kses_post(apply_filters('woocommerce_shipping_estimate_html', __('Shipping options will be updated during checkout.', 'woocommerce')));
                    }
                }
                ?>
            </p>

        <?php
        elseif (!$has_calculated_shipping || !$formatted_destination) : ?>
            <div class="sv_d-flex mb-2">
                <div class="sv_flex-fill align-self-center">
                    <h4 class="mb-0">Shipping</h4>
                </div>
                <?php if(is_cart()){ ?><div class=" pl-2"><a id="sv_calculateBtnToggle" onclick="toggleCartShippingFields()" class="sv_btn sv_btn-sm sv_btn-danger sv_text-white"><small>Calculate Now</small></a></div> <?php } ?>
            </div>

        <?php
            if (is_cart() && 'no' === get_option('woocommerce_enable_shipping_calc')) {
                echo wp_kses_post(apply_filters('woocommerce_shipping_not_enabled_on_cart_html', __('<span class="text-warning"><span class="dashicons dashicons-no"></span> Shipping costs are calculated during checkout.</span>', 'woocommerce')));
            } else {

                echo wp_kses_post(apply_filters('woocommerce_shipping_may_be_available_html', __('<span class="text-warning"><span class="dashicons dashicons-no"></span> Enter your address to view shipping options.</span>', 'woocommerce')));
            }
        elseif (!is_cart()) :
            echo wp_kses_post(apply_filters('woocommerce_no_shipping_available_html', __('<span class="text-warning"><span class="dashicons dashicons-no"></span> There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.</span>', 'woocommerce')));
        else :
            // Translators: $s shipping destination.
            echo wp_kses_post(apply_filters('woocommerce_cart_no_shipping_available_html', sprintf(esc_html__('No shipping options were found for %s.', 'woocommerce') . ' ', '<strong><span class="text-warning"><span class="dashicons dashicons-no"></span> ' . esc_html($formatted_destination) . '<span></strong>')));
            $calculator_text = esc_html__('Enter a different address', 'woocommerce');
        endif;
        ?>

        <?php if ($show_package_details) : ?>
            <?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html($package_details) . '</small></p>'; ?>
        <?php endif; ?>

        <?php if ($show_shipping_calculator && is_cart()) : ?>
            <?php woocommerce_shipping_calculator($calculator_text); ?>
        <?php endif; ?>
    </th>
</tr>