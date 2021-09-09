<?php
header('Content-Type: application/json');

class ShipvistaActions extends WC_Shipping_Method

{
    public $result = ['status' => 0, 'message' => 'Invalid request'];
    public function __construct()
    {
        parent::__construct();
        global $woocommerce;
        $this->init_settings();
    }

    public function unlinkAccount()
    {
        $name = get_option('shipvista_user_name');

        update_option('enabled', 'no');
        update_option('shipvista_user_email', '');
        update_option('shipvista_user_pass', '');
        update_option('shipvista_user_token', '');
        update_option('shipvista_user_avatar', '');
        update_option('shipvista_user_name', '');
        update_option('shipvista_user_balance', '');
        update_option('shipvista_user_currency', '');
        update_option('shipvista_refresh_token', '');
        // create a dblog

        $this->response['status'] = 200;
        $this->response['message'] = $name . ' thank you for using shipvista.';

        return $this->response;
    }
}


$action = new ShipvistaActions();
$request = @$_GET['wcs_action']  ?: $_POST['wcs_action'] ?: 'home';
$request = str_replace('-', ' ', trim(strtolower($request)));
$methodName = str_replace(' ', '', lcfirst(ucwords($request)));
$response = ['status' => '', 'message' => 'Invalid request'];
if (method_exists($action, $methodName)) {
    $response = $action->$methodName() ?: $action->response;
}
echo json_encode($response);
exit;
