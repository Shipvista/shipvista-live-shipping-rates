<?php
require_once SHIPVISTA__PLUGIN_DIR . '/inc/wc_shipvista_render.php';

class SLSR_WcShipvistaBootstrap extends SLSR_Shipvista
{
    use SLSR_ShipvistaRenderPage;
    private $parentObject;
    public $content = ['Errors' => ''];
    public $activePage = 'home';
    public $fieldPrepend = 'woocommerce_shipvista_';
    public $userApiToken = '';
    // declare all variables
    public function __construct()
    {
        parent::__construct();
        $this->pageLink = menu_page_url(SHIPVISTA__PLUGIN_SLUG, false) . '&tab=shipping&section=shipvista';

        $request = isset($_GET['wcs_page']) ? sanitize_text_field($_GET['wcs_page']) : 'connect';
        // check method 
        $this->userApiToken = $this->get_option('shipvista_api_token');

        $methodName = $this->getMethod($request);
        if (method_exists($this, $methodName)) {
            $this->activePage = $methodName;
            $this->getRequiredForms($methodName . 'Form');
            $this->$methodName();
        } else {
            $this->home();
        }
    }


    function getMethod(string $request)
    {
        $request = str_replace('-', ' ', trim(strtolower($request)));
        $methodName = str_replace(' ', '', lcfirst(ucwords($request)));
        return $methodName;
    }

    function home()
    {
        $this->connect();
    }

    public function connect()
    {
        // check if user has logged in successfully
        $userToken = $this->get_option('shipvista_api_token');
        $this->form_fields = $this->sv_connectForm();
        $this->content['signup_verify']  = false;
        $this->content['signup_email'] = isset($_COOKIE['shipvista_user_email']) ? sanitize_email($_COOKIE['shipvista_user_email']) : '';
        $this->content['signup_names'] = isset($_COOKIE['shipvista_user_names']) ? sanitize_text_field($_COOKIE['shipvista_user_names']) : '';
        if (strlen($this->content['signup_email']) > 3 && isset($_GET['signup']) && substr($_GET['signup'], 0, 4) == 'true') {
            $token = explode('=', sanitize_text_field($_GET['signup']))[1];
            $verify = $this->shipvistaApi('/register/verifyemailaddress', ['token' => $token], 'GET');
            if ($verify['status'] == true) {
                $this->content['signup_verify']  = true;
                setcookie('shipvista_user_email', '',  time() - 3600);
                setcookie('shipvista_user_names', '',  time() - 3600);
                $this->form_fields['shipvista_user_name']['value'] = $this->content['signup_email'];
            }
        }

        $this->content['form'] = $this->generate_settings_html($this->form_fields, false);
        $this->content['user'] = [];
        $this->content['enabled'] = $this->enabled;
        $this->content['verificationLink'] = SHIPVISTA__PLUGIN_SITE . '/wp-admin/admin.php?page=wc-settings&tab=shipping&section=shipvista&wcs_page=connect&signup=true';
        
        $this->content['carrier'] = false;
        $carrier = $this->getActiveCarrierMethods();
        if(is_array($carrier) && count($carrier) > 0){
            $this->content['carrier'] = 'yes';
        }

        if (!empty($userToken) && $this->content['enabled'] == 'yes') { // user has logged in success and 
            // get user details
            $userRequest = $this->shipvistaApi("/User", '', 'GET');
            
            $accountBalance = isset($userRequest['accountBalance']) ? $userRequest['accountBalance']['currency'] . ' ' . $userRequest['accountBalance']['amount'] : 0;
            if (strlen($accountBalance) > 3) {
                $this->update_option('shipvista_user_balance', $accountBalance);
            } else {
                $this->update_option('shipvista_user_balance', '$0');
            }
            $address = @$userRequest['address'] ?: [];

            if (empty($this->get_option('shipvista_origin_postcode')) && isset($address['postalCode'])) {
                $this->update_option('shipvista_origin_postcode', $address['postalCode']);
                $this->update_option('shipvista_origin_address_2', $address['streetAddress2']);
                $this->update_option('shipvista_origin_address', $address['streetAddress']);
                $this->update_option('shipvista_origin_phone_number', $address['phone']);
            }

            $this->content['postcode'] = $this->get_option('shipvista_origin_postcode');
            $this->content['user']['name'] = $this->get_option('shipvista_user_name');
            $this->content['user']['avatar'] = $this->get_option('shipvista_user_avatar');
            $this->content['user']['email'] = $this->get_option('shipvista_user_email');
            if (empty($this->content['user']['email']) || count(explode('@', $this->content['user']['email'])) != 2) {
                $email = @$userRequest['email'] ?: $this->get_option('shipvista_user_email');
                $this->content['user']['email'] = $email;
            }

            $this->content['user']['balance'] = $this->get_option('shipvista_user_balance');
            $this->content['user']['currency'] = $this->get_option('shipvista_user_currency');
            if (empty($this->content['user']['currency'])) {
                $currency = @$userRequest['accountBalance']['currency'];
                $this->update_option('shipvista_user_currency', $currency);
            }
            $this->content['user']['password'] = $this->get_option('shipvista_user_pass');
            $this->content['user']['expires'] = date('Y-m-d h:i:s', strtotime($this->get_option('shipvista_token_expires'))); // $this->get_option('shipvista_user_pass');
            $this->content['user']['errors'] = @json_decode($this->get_option('shipvista_plugin_errors'), true) ?? []; // $this->get_option('shipvista_user_pass');
            $this->content['user']['password'] = substr($this->content['user']['password'], 0, 3) . preg_replace('#[^\*]#', '*', substr($this->content['user']['password'], 3));
            $this->content['user']['token'] = $this->get_option('shipvista_api_token');
            $this->content['user']['token'] = substr($this->content['user']['token'], 0, 5) . preg_replace('#[^\*]#', '*', substr($this->content['user']['token'], 5, 25));
            $this->content['user']['refresh'] = $this->get_option('shipvista_refresh_token');
            $this->content['user']['refresh'] = substr($this->content['user']['refresh'], 0, 5) . preg_replace('#[^\*]#', '*', substr($this->content['user']['refresh'], 5, 10));
        }
        $this->render('connect.php');
    }


    public function carriers()
    {
        // The country/state
        $store_raw_country = get_option('woocommerce_default_country');
        // Split the country/state
        $split_country = explode(":", $store_raw_country);
        $this->wooCountry = $split_country;
        // Country and state separated:
        $carriers = json_decode(file_get_contents(SHIPVISTA__PLUGIN_DIR . 'assets/config/carriers.json'), true);

        $this->carrier_settings['CanadaPost'] = (array) @json_decode($this->get_option('CanadaPost')) ?: [];
        $this->carrier_settings['UPS'] = (array) @json_decode($this->get_option('UPS')) ?: [];
        $merge = array_merge($this->carrier_settings['CanadaPost'], $this->carrier_settings['UPS']);
        foreach ($carriers as $key => $carrier) {
            foreach($carrier as $k => $item){
                $carriers[$key][$k]['checked']  = false;
                if(in_array($item['carrier_option'], $merge)){
                    $carriers[$key][$k]['checked'] = true;
                }
            }
        }
	// if(!array_key_exists("priority worldwide pak int'l", $this->carrier_settings['carrier_canada_post'])){
	// 	$this->carrier_settings['carrier_canada_post']["priority worldwide envelope int'l"] = ['name' => "Priority Worldwide Envelope INT'L", 'checked' => 0]; 
	// 	$this->carrier_settings['carrier_canada_post']["priority worldwide pak int'l"] = ['name' => "Priority Worldwide pak INT'L", 'checked' => 0];
	// }
    $this->carriers = $carriers;
    
        
	$this->render('carriers.php');
    }

    public function parser()
    {
        header('Content-type: application/json');
        $response = ['status' => 0, 'message' => ''];
        if (isset($_POST['order']) && isset($_POST['code'])) {
            $response['status'] = 200;
            $response['message'] = "";
        } else {
            $response['message'] = 'Invalid request';
        }
        echo json_encode($response);
        exit;
    }

    public function settings()
    {
        // get tap
        $this->settingTabs = isset($_GET['wcs_setting']) ? sanitize_text_field($_GET['wcs_setting']) : 'basic';
        $this->content['Form'] = '';
        $this->render('settings.php');
    }
}
