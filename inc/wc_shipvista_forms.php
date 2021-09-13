<?php

namespace Shipvista\Forms;

trait SLSR_WcShipvistaForms
{
    public $shipvistaFormMethods = ['connectForm', 'basicForm', 'apiForm', 'shipperForm', 'restrictionForm', 'dimensionForm', 'carriersForm'];
    public $requiredForms = [];

    function init_form_fields()
    {
        $form_list = [];
        foreach ($this->shipvistaFormMethods as $key => $method) {
            $method = 'sv_' . $method;
            if (method_exists($this, $method)) {
                $form = $this->$method() ?: [];
                $form_list = array_merge($form_list, $form);
            }
        }
        $this->form_fields = $form_list;
    }

    public function getRequiredForms(string $pageForm)
    {
        $form_list = [];
        $settings = ['dimension' => 'sv_dimensionForm', 'shipper' => 'sv_shipperForm', 'apis' => 'sv_apiForm', 'restrict' => 'sv_restrictionForm',  'basic' => 'sv_basicForm'];
        $settingTabs = isset($_GET['wcs_setting']) ? sanitize_text_field($_GET['wcs_setting']) : '';
        $skip = @$settings[$settingTabs] ?: 'sv_basicForm';
        foreach ($this->shipvistaFormMethods as $key => $method) {
            if ($pageForm == $method || $skip == 'sv_' . $method) {
                continue;
            }
            $method = 'sv_' . $method;
            if (method_exists($this, $method)) {
                $form = $this->$method() ?: [];
                $form_list = array_merge($form_list, $form);
            }
        }

        $this->requiredForms = $this->generate_settings_html($form_list, false);
    }

    public function sv_carriersForm()
    {
        return array(
            'carrier_canada_post' => array(
                'title' => __('Default Carrier', 'shipvista'),
                'type'        => 'hidden',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'carrier_canada_post_enabled' => array(
                'title' => __('Enable Canada Post', 'shipvista'),
                'type'        => 'hidden',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
        );
    }

    public function settingsForm()
    {
        $settings = ['dimension' => 'sv_dimensionForm', 'shipper' => 'sv_shipperForm', 'basic' => 'sv_basicForm'];
        $settingTabs = isset($_GET['wcs_setting']) ? sanitize_text_field($_GET['wcs_setting']) : 'basic';
        if (array_key_exists($settingTabs, $settings)) {
            unset($settings[$settingTabs]);
            $settingList = [];
            foreach ($settings as $method) {
                $settingList = array_merge($settings,   $this->$method());
            }
            return $settingList;
        }
    }


    function sv_restrictionForm()
    {
        return array(

            'shipvista_restricted_locations' => array(
                'title' => __('Restricted Postal Codes', 'shipvista'),
                'type'        => 'text',
                'description' => 'Restricted shipping location where discount applies.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
        );
    }


    public function sv_dimensionForm()
    {

        return array(

            'shipvista_dimension_length' => array(
                'title' => __('Length', 'shipvista'),
                'type'        => 'number',
                'description' => 'Default shipping length.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_dimension_width' => array(
                'title' => __('Width', 'shipvista'),
                'type'        => 'text',
                'description' => 'Default shipping .',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_dimension_height' => array(
                'title' => __('Height', 'shipvista'),
                'type'        => 'text',
                'description' => 'Default shipping height.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_dimension_weight' => array(
                'title' => __('Weight', 'shipvista'),
                'type'        => 'text',
                'description' => 'Default shipping weight.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_dimension_size_unit' => array(
                'title' => __('Size Unit', 'shipvista'),
                'type'        => 'text',
                'description' => 'Default shipping size unit.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_dimension_weight_unit' => array(
                'title' => __('Weight Unit', 'shipvista'),
                'type'        => 'text',
                'description' => 'Default shipping weight unit.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            )

        );
    }

    function sv_apiForm()
    {
        return array(
            'shipvista_google_places_api' => array(
                'title' => __('Enable google location prediction', 'shipvista'),
                'type'        => 'checkbox',
                'description' => 'Enable address suggestions with Google Places API.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_google_places_api_key' => array(
                'title' => __('Enter your google api key', 'shipvista'),
                'type'        => 'text',
                'description' => 'Enter your google places api key.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
        );
    }

    public function sv_shipperForm()
    {

        return array(
            'shipvista_user_email' => array(
                'type'        => 'hidden',
                'desc_tip'    => true,
            ),
            'shipvista_origin_address' => array(
                'title' => __('Origin Address', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping address (Shipping from address).',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
                'required' => true,
            ),
            'shipvista_origin_address_2' => array(
                'title' => __('Origin Address 2', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping address 2 (Shipping from address).',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
                'required' => true,
            ),
            'shipvista_origin_state' => array(
                'title' => __('Origin State', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping state (Shipping from state).',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
                'required' => true,
                'maxlength' => 2,
            ),
            'shipvista_origin_city' => array(
                'title' => __('Origin City', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping city (Shipping from city).',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
                'required' => true,
            ),
            'shipvista_origin_country' => array(
                'title' => __('Origin Country', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping country (Shipping from country).',
                'class' => 'form-control mb-3 form-control-sm',
                'required' => true,
                'desc_tip'    => true,
                'maxlength'    => 2,
            ),
            'shipvista_origin_postcode' => array(
                'title' => __('Origin Postcode', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping postcode (Shipping from postcode).',
                'class' => 'form-control mb-3 form-control-sm',
                'required' => true,
                'desc_tip'    => true,
            ),
            'shipvista_origin_phone_number' => array(
                'title' => __('Origin Phone Number', 'shipvista'),
                'type'        => 'text',
                'description' => 'Shipping phone (Shipping from phone).',
                'class' => 'form-control mb-3 form-control-sm',
                'required' => true,
                'desc_tip'    => true,
            ),
            'shipvista_user_currency' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),
            'shipvista_user_balance' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            )

        );
    }

    public function sv_reportForm()
    {
        return array(
            'shipvista_user_feedback' => array(
                'title' => __('Feedback', 'shipvista'),
                'type'        => 'text',
                'class' => 'form-control mb-3 form-control-sm',
                'description' => 'Something wrong with the plugin? Please let us know by submiting your feedback.'
            ),
        );
    }

    public function sv_basicForm()
    {
        return array(
            'shipvista_log_satus' => array(
                'title' => __('Enable plugin logs to use for debugging.', 'shipvista'),
                'type'        => 'checkbox',
                'class' => 'form-control mb-3 form-control-sm',
            ),
            'shipvista_tax_status' => array(
                'title' => __('Tax Status', 'shipvista'),
                'type'        => 'text',
                'class' => 'form-control mb-3 form-control-sm',
            ),
            'shipvista_auto_labels' => array(
                'title' => __('Auto print labels', 'shipvista'),
                'type'        => 'checkbox',
                'description' => 'Once customers order is received print label after 15 minutes.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_fallback_rate' => array(
                'title' => __('Fallback Rate', 'shipvista'),
                'type'        => 'number',
                'description' => 'This Cost will be added for every unit of product if no rule is applied on it',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_fallback_rate_on' => array(
                'title' => __('Fallback Rate On', 'shipvista'),
                'type' => 'select',
                'options' => ['per_unit_quantity' => 'Per Unit Quantity', 'per_cart_item' => 'Per Cart Item', 'total_order' => 'Total order'],
                'class' => 'form-control mb-3 form-control-sm',
                'label_class' => ' d-none ',
            ),
            'shipvista_rate_margin' => array(
                'title' => __('Shipping Margin', 'shipvista'),
                'type'        => 'text',
                'description' => 'Add a margin on shipping rates to be applied before displaying to customers.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_free_shipping' => array(
                'title' => __('Free Shipping', 'shipvista'),
                'type'        => 'checkbox',
                'description' => 'Enable free shipping option for customer base on calculation.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_free_max_amount' => array(
                'title' => __('Free Shipping cap Amount', 'shipvista'),
                'type'        => 'text',
                'description' => 'Offer free shipping for shipping cost up to this amount. Customers will be charge the balance.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_free_shipping_days' => array(
                'title' => __('Free Shipping duration', 'shipvista'),
                'type'        => 'text',
                'description' => 'Estimated number of days for items to arrive the customer. Handling + transit times',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_handling_time' => array(
                'title' => __('Free Shipping duration', 'shipvista'),
                'type'        => 'text',
                'description' => 'Estimated number of days for items to be shipped to user.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_pickup' => array(
                'title' => __('Enable Pickup', 'shipvista'),
                'type'        => 'checkbox',
                'description' => 'Enable customer in store pickup.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_pickup_note' => array(
                'title' => __('Pickup Note', 'shipvista'),
                'type'        => 'text',
                'description' => 'Enter a note to display to customers on pickup.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'shipvista_pickup_address' => array(
                'title' => __('Pickup address', 'shipvista'),
                'type'        => 'text',
                'description' => 'Enter address to display to customers for pickup.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            )
        );
    }

    function sv_connectForm($options = [])
    {

        return array(
            'shipvista_user_name' => array(
                'title' => __('Email Address', 'shipvista'),
                'type'        => 'text',
                'description' => 'Enter your shivista email address.',
                'desc_tip'    => true,
                'class' => 'form-control mb-3 form-control-sm',
            ),
            'shipvista_user_pass' => array(
                'title' => __('Password', 'shipvista'),
                'type'        => 'password',
                'description' => 'Enter your shipvista account password.',
                'class' => 'form-control mb-3 form-control-sm',
                'desc_tip'    => true,
            ),
            'enabled' => array(
                'title' => __('Enable shipping', 'shipvista'),
                'type' => 'checkbox',
                'default' => 'yes',
                'label_class' => ' d-none ',
                'custom_attributes' => ['checked' => ($this->get_option('enabled') ? true : false)]
            ),
            'shipvista_api_token' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),
            'shipvista_user_avatar' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),
            'shipvista_user_balance' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),
            'shipvista_refresh_token' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),
            'shipvista_token_expires' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),
            'shipvista_plugin_errors' => array(
                'label_class' => ' d-none ',
                'type' => 'hidden',
            ),

        );
    }
}
