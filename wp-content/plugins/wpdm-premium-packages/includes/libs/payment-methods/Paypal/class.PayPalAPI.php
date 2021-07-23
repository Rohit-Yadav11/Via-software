<?php


namespace WPDMPP;


use WPDM\Session;

class PayPalAPI
{
    private $base = 'https://api.paypal.com/v1/';
    private $base_sandbox = 'https://api.sandbox.paypal.com/v1/';
    private $clientID;
    private $clientSecret;
    private $accessToken = null;
    public $planID = null;
    private $productID = null;
    private $orderID = null;
    private $orderTitle = null;

    function __construct($env, $clientID, $clientSecret)
    {
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
        if ($env === 'sandbox')
            $this->base = $this->base_sandbox;
        $this->getAccessToken();
        //->createProduct("BINGOX1", null, null, 'https://www.wpdownloadmanager.com/', 'https://www.wpdownloadmanager.com/wp-content/themes/wpdm5/images/wordpress-download-manager-logo.png')->createPlan(129);
    }

    function getAccessToken()
    {
        $params = array('grant_type' => 'client_credentials');
        $headers = array(
            "Authorization" => "Basic " . base64_encode($this->clientID . ':' . $this->clientSecret),
            "Content-Type" => "application/x-www-form-urlencoded"
        );
        $data = $this->_request('oauth2/token', $params, $headers);
        $this->accessToken = $data->access_token;
        return $this;
    }

    function createProduct($orderID, $name = null, $description = '', $url = null, $image = null)
    {
        if (!$name) $name = 'Order: ' . $orderID;
        if (!$description) $description = $name;
        if (!$url) $url = home_url('/');
        if (!$image) $image = get_site_icon_url();
        $params = array(
            'name' => $name,
            'description' => $description,
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
            'image_url' => $image,
            'home_url' => $url
        );
        $headers = array(
            "Authorization" => "Bearer " . $this->accessToken,
            "PayPal-Request-Id" => $orderID,
            "Content-Type" => "application/json"
        );
        //wpdmdd($params);
        $data = $this->_request('catalogs/products', json_encode($params), $headers);

        $this->orderID = $orderID;
        $this->orderTitle = $name;
        $this->productID = $data->id;
        return $this;
    }

    function createPlan($price)
    {
        global $wpdmpp_settings;
        $wpdmpp_settings['order_validity_period'] = (int)$wpdmpp_settings['order_validity_period'] > 0 ? (int)$wpdmpp_settings['order_validity_period'] : 365;
        $interval_count = $wpdmpp_settings['order_validity_period'] / 365;
        $interval_unit = 'YEAR';
        if ($interval_count < 1) {
            $interval_count = $wpdmpp_settings['order_validity_period'] / 30;
            $interval_unit = 'MONTH';
        }
        if (!is_int($interval_count)) {
            $interval_count = $wpdmpp_settings['order_validity_period'] / 7;
            $interval_unit = 'WEEK';
        }

        $params = array(
            'product_id' => $this->productID,
            'name' => $this->orderTitle,
            'description' => $this->orderTitle,
            'billing_cycles' => array(
                array(
                    'frequency' => array("interval_unit" => $interval_unit, "interval_count" => (int)$interval_count),
                    "tenure_type" => "REGULAR",
                    "sequence" => 1,
                    "total_cycles" => 0,
                    "pricing_scheme" => array(
                        "fixed_price" => array(
                            "value" => $price,
                            "currency_code" => wpdmpp_currency_code()
                        )
                    )
                )
            ),
            "payment_preferences" => array(
                "auto_bill_outstanding" => true,
                "setup_fee_failure_action" => "CONTINUE",
                "payment_failure_threshold" => 5
            ),
        );
        $reqid = Session::get('ppreqid');
        if(!$reqid){
            $reqid = $this->orderID;
            Session::set('ppreqid', $reqid);
        }
        $headers = array(
            "Authorization" => "Bearer " . $this->accessToken,
            "PayPal-Request-Id" => $reqid,
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "Prefer" => "return=representation"
        );

        $data = $this->_request('billing/plans', json_encode($params), $headers);
        //wpdmdd($data->billing_cycles[0]->pricing_scheme->fixed_price->value);
        $this->planID = $data->id;

        //Update pricing in case price is changed in cart
        if((double)$data->billing_cycles[0]->pricing_scheme->fixed_price->value !== (double)$price) {
            $reqid = $this->orderID . "_" . time();
            Session::set('ppreqid', $reqid);
            $headers = array(
                "Authorization" => "Bearer " . $this->accessToken,
                "PayPal-Request-Id" => $reqid,
                "Content-Type" => "application/json",
                "Accept" => "application/json",
                "Prefer" => "return=representation"
            );
            $data = $this->_request('billing/plans', json_encode($params), $headers);
            $this->planID = $data->id;
        }

        return $this;
    }


    function getSubscriptionDetails($subscriptionID)
    {
        $headers = array(
            "Authorization" => "Bearer " . $this->accessToken,
            "Content-Type" => "application/json",
        );
        $data = $this->_request("billing/subscriptions/{$subscriptionID}", array(), $headers, 'GET');
        return $data;
    }

    function _request($action, $params, $headers, $method = 'POST')
    {
        $url = $this->base . $action;
        $uparts = parse_url($this->base);
        $headers['Host'] = $uparts['host'];
        $response = wp_remote_post($url, array(
                'method' => $method,
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'body' => $params,
                'cookies' => array()
            )
        );
        return json_decode($response['body']);
    }

}
