<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function WPDMPP()
{
    global $wpdmpp;
    return $wpdmpp;
}

//number of total sales
function wpdmpp_total_purchase($pid = '')
{
    global $wpdb;
    if (!$pid) $pid = get_the_ID();
    $sales = $wpdb->get_var("select count(*) from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id and oi.pid='$pid' and  ( o.payment_status='Completed' or o.payment_status='Expired' )");

    return $sales;
}


//number of total sales
function wpdmpp_total_sales($uid = '', $pid = '', $sdate = '', $edate = '')
{
    global $wpdb;

    $pid_cond = ($pid > 0) ? "and oi.pid='$pid'" : "";
    $uid_cond = ($uid > 0) ? "and oi.sid='$uid'" : "";

    $sdate = $sdate == '' ? date("Y-m-01") : $sdate;
    $edate = $edate == '' ? date("Y-m-d", strtotime("last day of this month")) : $edate;
    $sdate_cond = $sdate != '' ? " and o.date >= '" . strtotime($sdate) . "'" : "and o.date >= '" . strtotime(date("Y-m-01")) . "'";
    $edate_cond = $sdate != '' ? " and o.date <= '" . strtotime($edate) . "'" : "and o.date <= '" . strtotime(date("Y-m-d", strtotime("last day of this month"))) . "'";

    if ($pid_cond != '' || $uid_cond != '')
        $sales = $wpdb->get_var("select sum(oi.price * oi.quantity) from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id {$pid_cond} {$uid_cond} {$sdate_cond} {$edate_cond} and ( o.payment_status='Completed' or o.payment_status='Expired' )");
    else
        $sales = $wpdb->get_var("select sum(o.total) from {$wpdb->prefix}ahm_orders o where  ( o.payment_status='Completed' or o.payment_status='Expired' ) {$sdate_cond} {$edate_cond}");

    return number_format($sales, 2, '.', '');
}


function wpdmpp_daily_sales($uid = '', $pid = '', $sdate = '', $edate = '')
{
    global $wpdb;

    $pid_cond = ($pid > 0) ? "and oi.pid='$pid'" : "";
    $uid_cond = ($uid > 0) ? "and oi.sid='$uid'" : "";
    $sdate = $sdate == '' ? date("Y-m-01") : $sdate;
    $edate = $edate == '' ? date("Y-m-d", strtotime("last day of this month")) : $edate;
    $sdate_cond = $sdate != '' ? " and o.date >= '" . strtotime($sdate) . "'" : "and o.date >= '" . strtotime(date("Y-m-01")) . "'";
    $edate_cond = $sdate != '' ? " and o.date <= '" . strtotime($edate) . "'" : "and o.date <= '" . strtotime(date("Y-m-d", strtotime("last day of this month"))) . "'";

    $sales = $wpdb->get_results("select sum(oi.price * oi.quantity) as daily_sale,  sum(oi.quantity) as quantities, oi.date, oi.year, oi.month, oi.day from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id {$pid_cond} {$uid_cond} {$sdate_cond} {$edate_cond} and  ( o.payment_status='Completed' or o.payment_status='Expired' ) group by oi.date");

    $diff = date_diff(date_create($edate), date_create($sdate))->days;
    $sdata = array();
    $i = 0;
    do {
        $i++;
        $sdata['sales'][$sdate] = 0;
        $sdata['quantities'][$sdate] = 0;
        $sdate = date('Y-m-d', strtotime('+1 day', strtotime($sdate)));
    } while ($i <= $diff);

    foreach ($sales as $sale) {
        $sdata['sales'][$sale->date] = $sale->daily_sale;
        $sdata['quantities'][$sale->date] = $sale->quantities;
    }

    return $sdata;
}

function wpdmpp_top_sellings_products($uid = '', $sdate = '', $edate = '', $s = 0, $e = 1000)
{
    global $wpdb;

    $uid_cond = ($uid > 0) ? "and oi.sid='$uid'" : "";
    //$sdate = $sdate == ''?date("Y-m-01"):$sdate;
    //$edate = $edate == ''?date("Y-m-31"):$edate;
    $sdate_cond = $sdate != '' ? " and o.date >= '" . strtotime($sdate) . "'" : "";
    $edate_cond = $sdate != '' ? " and o.date <= '" . strtotime($edate) . "'" : "";

    $tsp = $wpdb->get_results("select oi.pid, sum(oi.price) as sales,  sum(oi.quantity) as quantities, oi.date, oi.year, oi.month, oi.day from {$wpdb->prefix}ahm_orders o, {$wpdb->prefix}ahm_order_items oi where oi.oid=o.order_id  {$uid_cond} {$sdate_cond} {$edate_cond} and  ( o.payment_status='Completed' or o.payment_status='Expired' ) group by oi.pid ORDER BY quantities DESC limit $s, $e");
    return $tsp;
}

function wpdmpp_recent_sales($uid = '', $count = 10)
{
    global $wpdb;

    $uid_cond = ($uid > 0) ? "and {$wpdb->prefix}ahm_order_items.sid='$uid'" : "";
    $tsp = $wpdb->get_results("select {$wpdb->prefix}ahm_order_items.pid as product_id,{$wpdb->prefix}ahm_order_items.price, ({$wpdb->prefix}ahm_order_items.price * {$wpdb->prefix}ahm_order_items.quantity) as total, {$wpdb->prefix}ahm_orders.date as time_stamp,  {$wpdb->prefix}ahm_order_items.date, {$wpdb->prefix}ahm_order_items.year, {$wpdb->prefix}ahm_order_items.month, {$wpdb->prefix}ahm_order_items.day from {$wpdb->prefix}ahm_order_items LEFT JOIN {$wpdb->prefix}ahm_orders on {$wpdb->prefix}ahm_order_items.oid={$wpdb->prefix}ahm_orders.order_id  {$uid_cond} and  ( {$wpdb->prefix}ahm_orders.payment_status='Completed' or {$wpdb->prefix}ahm_orders.payment_status='Expired' ) ORDER BY {$wpdb->prefix}ahm_orders.date DESC limit 0, $count");
    foreach ($tsp as &$_tsp) {
        $_tsp->post_title = get_the_title($_tsp->product_id);
    }
    return $tsp;
}

function wpdmpp_get_licenses()
{
    $pre_licenses = get_wpdmpp_option('licenses', array(
        'single' => array('name' => 'Standard', 'description' => '', 'use' => 1),
        'extended' => array('name' => 'Extended', 'description' => '', 'use' => 5),
        'unlimited' => array('name' => 'Unlimited', 'description' => '', 'use' => 99),
    ));
    $pre_licenses = maybe_unserialize($pre_licenses);
    return $pre_licenses;

}

function get_wpdmpp_option($name, $default = '')
{
    global $wpdmpp_settings;

    $name = explode('/', $name);

    if (!is_array($wpdmpp_settings)) return $default;

    if (count($name) == 1)
        return isset($wpdmpp_settings[$name[0]]) ? $wpdmpp_settings[$name[0]] : $default;
    else if (count($name) == 2)
        return isset($wpdmpp_settings[$name[0]], $wpdmpp_settings[$name[0]][$name[1]]) ? $wpdmpp_settings[$name[0]][$name[1]] : $default;
    else if (count($name) == 3)
        return isset($wpdmpp_settings[$name[0]], $wpdmpp_settings[$name[0]][$name[1]], $wpdmpp_settings[$name[0]][$name[1]][$name[2]]) ? $wpdmpp_settings[$name[0]][$name[1]][$name[2]] : $default;
    else
        return $default;
}

function wpdmpp_countries()
{
    return array('AF' => 'AFGHANISTAN', 'AL' => 'ALBANIA', 'DZ' => 'ALGERIA', 'AS' => 'AMERICAN SAMOA', 'AD' => 'ANDORRA', 'AO' => 'ANGOLA', 'AI' => 'ANGUILLA', 'AQ' => 'ANTARCTICA', 'AG' => 'ANTIGUA AND BARBUDA', 'AR' => 'ARGENTINA', 'AM' => 'ARMENIA', 'AW' => 'ARUBA', 'AU' => 'AUSTRALIA', 'AT' => 'AUSTRIA', 'AZ' => 'AZERBAIJAN', 'BS' => 'BAHAMAS', 'BH' => 'BAHRAIN', 'BD' => 'BANGLADESH', 'BB' => 'BARBADOS', 'BY' => 'BELARUS', 'BE' => 'BELGIUM', 'BZ' => 'BELIZE', 'BJ' => 'BENIN', 'BM' => 'BERMUDA', 'BT' => 'BHUTAN', 'BO' => 'BOLIVIA', 'BA' => 'BOSNIA AND HERZEGOVINA', 'BW' => 'BOTSWANA', 'BV' => 'BOUVET ISLAND', 'BR' => 'BRAZIL', 'IO' => 'BRITISH INDIAN OCEAN TERRITORY', 'BN' => 'BRUNEI DARUSSALAM', 'BG' => 'BULGARIA', 'BF' => 'BURKINA FASO', 'BI' => 'BURUNDI', 'KH' => 'CAMBODIA', 'CM' => 'CAMEROON', 'CA' => 'CANADA', 'CV' => 'CAPE VERDE', 'KY' => 'CAYMAN ISLANDS', 'CF' => 'CENTRAL AFRICAN REPUBLIC', 'TD' => 'CHAD', 'CL' => 'CHILE', 'CN' => 'CHINA', 'CX' => 'CHRISTMAS ISLAND', 'CC' => 'COCOS (KEELING) ISLANDS', 'CO' => 'COLOMBIA', 'KM' => 'COMOROS', 'CG' => 'CONGO', 'CD' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'CK' => 'COOK ISLANDS', 'CR' => 'COSTA RICA', 'CI' => 'COTE DIVOIRE', 'HR' => 'CROATIA', 'CU' => 'CUBA', 'CY' => 'CYPRUS', 'CZ' => 'CZECH REPUBLIC', 'DK' => 'DENMARK', 'DJ' => 'DJIBOUTI', 'DM' => 'DOMINICA', 'DO' => 'DOMINICAN REPUBLIC', 'EC' => 'ECUADOR', 'EG' => 'EGYPT', 'SV' => 'EL SALVADOR', 'GQ' => 'EQUATORIAL GUINEA', 'ER' => 'ERITREA', 'EE' => 'ESTONIA', 'ET' => 'ETHIOPIA', 'FK' => 'FALKLAND ISLANDS (MALVINAS)', 'FO' => 'FAROE ISLANDS', 'FJ' => 'FIJI', 'FI' => 'FINLAND', 'FR' => 'FRANCE', 'GF' => 'FRENCH GUIANA', 'PF' => 'FRENCH POLYNESIA', 'TF' => 'FRENCH SOUTHERN TERRITORIES', 'GA' => 'GABON', 'GM' => 'GAMBIA', 'GE' => 'GEORGIA', 'DE' => 'GERMANY', 'GH' => 'GHANA', 'GI' => 'GIBRALTAR', 'GR' => 'GREECE', 'GL' => 'GREENLAND', 'GD' => 'GRENADA', 'GP' => 'GUADELOUPE', 'GU' => 'GUAM', 'GT' => 'GUATEMALA', 'GN' => 'GUINEA', 'GW' => 'GUINEA-BISSAU', 'GY' => 'GUYANA', 'HT' => 'HAITI', 'HM' => 'HEARD ISLAND AND MCDONALD ISLANDS', 'VA' => 'HOLY SEE (VATICAN CITY STATE)', 'HN' => 'HONDURAS', 'HK' => 'HONG KONG', 'HU' => 'HUNGARY', 'IS' => 'ICELAND', 'IN' => 'INDIA', 'ID' => 'INDONESIA', 'IR' => 'IRAN, ISLAMIC REPUBLIC OF', 'IQ' => 'IRAQ', 'IE' => 'IRELAND', 'IL' => 'ISRAEL', 'IT' => 'ITALY', 'JM' => 'JAMAICA', 'JP' => 'JAPAN', 'JO' => 'JORDAN', 'KZ' => 'KAZAKHSTAN', 'KE' => 'KENYA', 'KI' => 'KIRIBATI', 'KP' => 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'KR' => 'KOREA, REPUBLIC OF', 'KW' => 'KUWAIT', 'KG' => 'KYRGYZSTAN', 'LA' => 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'LV' => 'LATVIA', 'LB' => 'LEBANON', 'LS' => 'LESOTHO', 'LR' => 'LIBERIA', 'LY' => 'LIBYAN ARAB JAMAHIRIYA', 'LI' => 'LIECHTENSTEIN', 'LT' => 'LITHUANIA', 'LU' => 'LUXEMBOURG', 'MO' => 'MACAO', 'MK' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'MG' => 'MADAGASCAR', 'MW' => 'MALAWI', 'MY' => 'MALAYSIA', 'MV' => 'MALDIVES', 'ML' => 'MALI', 'MT' => 'MALTA', 'MH' => 'MARSHALL ISLANDS', 'MQ' => 'MARTINIQUE', 'MR' => 'MAURITANIA', 'MU' => 'MAURITIUS', 'YT' => 'MAYOTTE', 'MX' => 'MEXICO', 'FM' => 'MICRONESIA, FEDERATED STATES OF', 'MD' => 'MOLDOVA, REPUBLIC OF', 'MC' => 'MONACO', 'MN' => 'MONGOLIA', 'MS' => 'MONTSERRAT', 'MA' => 'MOROCCO', 'MZ' => 'MOZAMBIQUE', 'MM' => 'MYANMAR', 'NA' => 'NAMIBIA', 'NR' => 'NAURU', 'NP' => 'NEPAL', 'NL' => 'NETHERLANDS', 'AN' => 'NETHERLANDS ANTILLES', 'NC' => 'NEW CALEDONIA', 'NZ' => 'NEW ZEALAND', 'NI' => 'NICARAGUA', 'NE' => 'NIGER', 'NG' => 'NIGERIA', 'NU' => 'NIUE', 'NF' => 'NORFOLK ISLAND', 'MP' => 'NORTHERN MARIANA ISLANDS', 'NO' => 'NORWAY', 'OM' => 'OMAN', 'PK' => 'PAKISTAN', 'PW' => 'PALAU', 'PS' => 'PALESTINIAN TERRITORY, OCCUPIED', 'PA' => 'PANAMA', 'PG' => 'PAPUA NEW GUINEA', 'PY' => 'PARAGUAY', 'PE' => 'PERU', 'PH' => 'PHILIPPINES', 'PN' => 'PITCAIRN', 'PL' => 'POLAND', 'PT' => 'PORTUGAL', 'PR' => 'PUERTO RICO', 'QA' => 'QATAR', 'RE' => 'REUNION', 'RO' => 'ROMANIA', 'RU' => 'RUSSIAN FEDERATION', 'RW' => 'RWANDA', 'SH' => 'SAINT HELENA', 'KN' => 'SAINT KITTS AND NEVIS', 'LC' => 'SAINT LUCIA', 'PM' => 'SAINT PIERRE AND MIQUELON', 'VC' => 'SAINT VINCENT AND THE GRENADINES', 'WS' => 'SAMOA', 'SM' => 'SAN MARINO', 'ST' => 'SAO TOME AND PRINCIPE', 'SA' => 'SAUDI ARABIA', 'SN' => 'SENEGAL', 'CS' => 'SERBIA AND MONTENEGRO', 'SC' => 'SEYCHELLES', 'SL' => 'SIERRA LEONE', 'SG' => 'SINGAPORE', 'SK' => 'SLOVAKIA', 'SI' => 'SLOVENIA', 'SB' => 'SOLOMON ISLANDS', 'SO' => 'SOMALIA', 'ZA' => 'SOUTH AFRICA', 'GS' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'ES' => 'SPAIN', 'LK' => 'SRI LANKA', 'SD' => 'SUDAN', 'SR' => 'SURINAME', 'SJ' => 'SVALBARD AND JAN MAYEN', 'SZ' => 'SWAZILAND', 'SE' => 'SWEDEN', 'CH' => 'SWITZERLAND', 'SY' => 'SYRIAN ARAB REPUBLIC', 'TW' => 'TAIWAN, PROVINCE OF CHINA', 'TJ' => 'TAJIKISTAN', 'TZ' => 'TANZANIA, UNITED REPUBLIC OF', 'TH' => 'THAILAND', 'TL' => 'TIMOR-LESTE', 'TG' => 'TOGO', 'TK' => 'TOKELAU', 'TO' => 'TONGA', 'TT' => 'TRINIDAD AND TOBAGO', 'TN' => 'TUNISIA', 'TR' => 'TURKEY', 'TM' => 'TURKMENISTAN', 'TC' => 'TURKS AND CAICOS ISLANDS', 'TV' => 'TUVALU', 'UG' => 'UGANDA', 'UA' => 'UKRAINE', 'AE' => 'UNITED ARAB EMIRATES', 'GB' => 'UNITED KINGDOM', 'US' => 'UNITED STATES', 'UM' => 'UNITED STATES MINOR OUTLYING ISLANDS', 'UY' => 'URUGUAY', 'UZ' => 'UZBEKISTAN', 'VU' => 'VANUATU', 'VE' => 'VENEZUELA', 'VN' => 'VIET NAM', 'VG' => 'VIRGIN ISLANDS, BRITISH', 'VI' => 'VIRGIN ISLANDS, U.S.', 'WF' => 'WALLIS AND FUTUNA', 'EH' => 'WESTERN SAHARA', 'YE' => 'YEMEN', 'ZM' => 'ZAMBIA', 'ZW' => 'ZIMBABWE');
}

function wpdmpp_tax_active()
{
    global $wpdmpp_settings;
    return isset($wpdmpp_settings['tax']) && isset($wpdmpp_settings['tax']['enable']) ? true : false;
}

function wpdmpp_show_tax()
{
    global $wpdmpp_settings;
    return isset($wpdmpp_settings['tax']) && isset($wpdmpp_settings['tax']['tax_on_cart']) ? true : false;
}


//Send notification before delete product
add_action('wp_trash_post', 'wpdmpp_notify_product_rejected');
function wpdmpp_notify_product_rejected($post_id)
{
    global $post_type;
    if ($post_type != 'wpdmpro') return;

    $post = get_post($post_id);
    $post_meta = get_post_meta($post_id, "_z_user_review", true);

    if ($post_meta != ""):
        $author = get_userdata($post->post_author);
        $author_email = $author->user_email;
        $email_subject = "Your product has been rejected.";

        ob_start(); ?>
        <html>
        <head>
            <title>New post at <?php bloginfo('name') ?></title>
        </head>
        <body>
        <p>
            Hi <?php echo $author->user_firstname ?>,
        </p>

        <p>
            Your product <?php the_title() ?> has not been approved by team.
        </p>
        </body>
        </html>
        <?php
        $message = ob_get_contents();
        ob_end_clean();

        wp_mail($author_email, $email_subject, $message);
    endif;
}

// Product accept notification email
function wpdmpp_notify_product_accepted($post_id)
{
    global $post_type;
    if ($post_type != 'wpdmpro') return;

    if (($_POST['post_status'] == 'publish') && ($_POST['original_post_status'] != 'publish')) {
        $post = get_post($post_id);
        $post_meta = get_post_meta($post_id, "_z_user_review", TRUE);
        if ($post_meta != ""):

            $author = get_userdata($post->post_author);
            $author_email = $author->user_email;
            $email_subject = "Your post has been published.";

            ob_start(); ?>
            <html>
            <head>
                <title>Your Product Status at <?php bloginfo('name') ?></title>
            </head>
            <body>
            <p>Hi <?php echo $author->user_firstname ?>,</p>
            <p>Your product <a href="<?php echo get_permalink($post->ID) ?>"><?php the_title_attribute() ?></a> has been
                published.</p>
            </body>
            </html>
            <?php
            $message = ob_get_clean();

            wp_mail($author_email, $email_subject, $message);
        endif;
    }
}


/**
 * Calculate pending balance and matured balance of the seller
 *
 * @return array $seller_balances Array of balances. Access using `pending` and `matured`
 * @since 3.8.9
 */
function wpdmpp_seller_balances()
{
    global $wpdb, $current_user;
    $uid = $current_user->ID;
    $sql = "select sum(i.price*i.quantity) from {$wpdb->prefix}ahm_orders o,
                          {$wpdb->prefix}ahm_order_items i,
                          {$wpdb->prefix}posts p
                          where p.post_author=$uid and
                                i.oid=o.order_id and
                                i.pid=p.ID and
                                i.quantity > 0 and
                                o.payment_status='Completed'";

    $total_sales = $wpdb->get_var($sql);
    $commission = wpdmpp_site_commission();
    $total_commission = $total_sales * $commission / 100;
    $total_earning = $total_sales - $total_commission;
    $sql = "select sum(amount) from {$wpdb->prefix}ahm_withdraws where uid=$uid";
    $total_withdraws = $wpdb->get_var($sql);
    $balance = $total_earning - $total_withdraws;

    //finding matured balance
    $payout_duration = get_option("wpdmpp_payout_duration");
    $dt = $payout_duration * 24 * 60 * 60;
    $sqlm = "select sum(i.price*i.quantity) from {$wpdb->prefix}ahm_orders o,
                          {$wpdb->prefix}ahm_order_items i,
                          {$wpdb->prefix}posts p
                          where p.post_author=$uid and
                                i.oid=o.order_id and
                                i.pid=p.ID and
                                i.quantity > 0 and
                                o.payment_status='Completed'
                                and (o.date+($dt))<" . time() . "";

    $tempbalance = $wpdb->get_var($sqlm);
    $tempbalance = $tempbalance - ($tempbalance * $commission / 100);
    $matured_balance = $tempbalance - $total_withdraws;

    //finding pending balance
    $pending_balance = $balance - $matured_balance;

    $seller_balances = array();
    $seller_balances['pending'] = $pending_balance;
    $seller_balances['matured'] = $matured_balance;

    return $seller_balances;
}

//for withdraw request
function wpdmpp_withdraw_request()
{
    global $wpdb, $current_user;

    $uid = $current_user->ID;

    if (isset($_POST['withdraw'], $_POST['withdraw_amount']) && $_POST['withdraw'] == 1 && $_POST['withdraw_amount'] > 0) {

        // Check if matured balance is greater than 0
        $seller_balances = wpdmpp_seller_balances();
        if ($seller_balances['matured'] <= 0) {
            echo 'denied';
            die();
        }

        $wpdb->insert(
            "{$wpdb->prefix}ahm_withdraws",
            array(
                'uid' => $uid,
                'date' => time(),
                'amount' => absint($_POST['withdraw_amount']),
                'status' => 0
            ),
            array(
                '%d',
                '%d',
                '%f',
                '%d'
            )
        );

        if (wpdm_is_ajax()) {
            _e("Withdraw Request Sent!", "wpdm-premium-packages");
            die();
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        die();
    }

}

function wpdmpp_redirect($url)
{
    if (!headers_sent())
        header("location: " . $url);
    else
        echo "<script>location.href='{$url}';</script>";
    die();
}

function wpdmpp_js_redirect($url)
{
    echo "&nbsp;Redirecting...<script>location.href='{$url}';</script>";
    die();
}

function wpdmpp_members_page()
{
    $settings = get_option('_wpdmpp_settings');
    return isset($settings['members_page_id']) ? get_permalink($settings['members_page_id']) : wpdm_user_dashboard_url(array('udb_page' => 'account-credits'));
}

function wpdmpp_orders_page($part = '')
{
    global $wpdmpp_settings;
    $settings = $wpdmpp_settings;

    $url = get_permalink($settings['orders_page_id']);
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }

    $udbpage = get_option('__wpdm_user_dashboard', 0);
    if ((int)$udbpage > 0 && (int)$settings['orders_page_id'] === (int)$udbpage) {

        $udbpage = get_permalink($udbpage);
        $sap = strstr($udbpage, '?') ? "&udb_page=" : "?udb_page=";
        $url = $udbpage . $sap . "purchases/orders/";
        if ($part != '') {
            $part = explode("=", $part);
            $url = $udbpage . $sap . "purchases/order/" . end($part) . "/";
        }
    }
    return $url;
}

function wpdmpp_guest_order_page($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    $url = get_permalink($settings['guest_order_page_id']);
    if (!isset($settings['guest_download']) || $settings['guest_download'] == 0) return '';
    if ($part != '') {
        if (strpos($url, '?')) $url .= "&" . $part;
        else $url .= "?" . $part;
    }
    return $url;
}

/**
 * Returns cart page url
 * @param array $params
 * @return false|string
 */
function wpdmpp_cart_page($params = array())
{
    global $wpdmpp_settings;
    if (!$wpdmpp_settings)
        $wpdmpp_settings = get_option('_wpdmpp_settings');

    $url = get_permalink($wpdmpp_settings['page_id']);

    $url = add_query_arg($params, $url);

    return $url;
}

function wpdmpp_cart_url($params = array())
{
    return wpdmpp_cart_page($params);
}


function wpdmpp_continue_shopping_url($part = '')
{
    $settings = get_option('_wpdmpp_settings');
    return $settings['continue_shopping_url'];
}


function wpdmpp_save_billing_info()
{
    global $current_user;
    if (isset($_POST['__wpdm_store_owner']))
        $__wpdm_store_owner = isset($_POST['__wpdm_store_owner']) ? 1 : 0;
    update_user_meta($current_user->ID, '__wpdm_store_owner', $__wpdm_store_owner);
    if (isset($_POST['__wpdm_store'])) {
        $store_data = wpdm_sanitize_array($_POST['__wpdm_store']);
        update_user_meta($current_user->ID, '__wpdm_store', $store_data);
    }
    if (isset($_POST['checkout']) && isset($_POST['checkout']['billing'])) {
        $codata = wpdm_sanitize_array($_POST['checkout']);
        update_user_meta($current_user->ID, 'user_billing_shipping', serialize($codata));
    }
}

/**
 * Get the list of purchased items of the current user
 */
function wpdmpp_get_purchased_items()
{
    if (!isset($_GET['wpdmppaction']) || $_GET['wpdmppaction'] != 'getpurchaseditems') return;
    if (wpdm_query_var('user') != '') {
        $user = wp_signon(array('user_login' => wpdm_query_var('user'), 'user_password' => wpdm_query_var('pass')));
        if ($user->ID) wp_set_current_user($user->ID);
    }
    if (wpdm_query_var('wpdm_access_token') != '') {
        $at = wpdm_query_var('wpdm_access_token');
        if (!$at) die(json_encode(array('error' => 'Invalid Access Token!')));
        $atx = explode("x", $at);
        $uid = end($atx);
        $uid = (int)$uid;
        if (!$uid) die(json_encode(array('error' => 'Invalid Access Token!')));
        $sat = get_user_meta($uid, '__wpdm_access_token', true);
        if ($sat === '') die(json_encode(array('error' => 'Invalid Access Token!')));
        if ($sat === $at)
            wp_set_current_user($uid);
        else
            die(json_encode(array('error' => "Invalid Access Token!")));
    }
    if (is_user_logged_in())
        wp_send_json(\WPDMPP\Libs\Order::getPurchasedItems());
    else
        wp_send_json(array('error' => '<a href="https://www.wpdownloadmanager.com/user-dashboard/?redirect_to=[redirect]">You need to login first!</a>'));
    die();
}

/**
 * Retrienve Site Commissions on User's Sales
 * @param null $uid
 * @return mixed
 */
function wpdmpp_site_commission($uid = null)
{
    global $current_user;
    $user = $current_user;
    if ($uid) $user = get_userdata($uid);
    $comission = get_option("wpdmpp_user_comission");
    $comission = isset($comission[$user->roles[0]]) ? (double)$comission[$user->roles[0]] : 0;
    return $comission;
}

function wpdmpp_get_user_earning()
{

}


function wpdmpp_product_price($pid, $license = '')
{
    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = wpdmpp_sales_price($pid);
    $price = (double)($sales_price) > 0 && $sales_price < $base_price ? (double)$sales_price : (double)$base_price;

    if (floatval($price) == 0) return number_format(0, 2, ".", "");
    return number_format($price, 2, ".", "");
}

function wpdmpp_is_ajax()
{
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) return TRUE;
    return false;
}

//delete product from front-end
function wpdmpp_delete_product()
{
    if (is_user_logged_in() && isset($_GET['dproduct'])) {
        global $current_user;
        $pid = intval($_GET['dproduct']);
        $pro = get_post($pid);

        if ($current_user->ID == $pro->post_author) {
            wp_update_post(array('ID' => $pid, 'post_status' => 'trash'));
            $settings = get_option('_wpdmpp_settings');
            if ($settings['frontend_product_delete_notify'] == 1) {
                wp_mail(get_option('admin_email'), "I had to delete a product", "Hi, Sorry, but I had to delete following product for some reason:<br/>{$pro->post_title}", "From: {$current_user->user_email}\r\nContent-type: text/html\r\n\r\n");
            }
            \WPDM\Session::set('dpmsg', __('Product Deleted', 'wpdm-premium-packages'));
            header("location: " . $_SERVER['HTTP_REFERER']);
            die();
        }
    }
}

function wpdmpp_order_completed_mail()
{

}

function wpdmpp_head()
{
    $wpdmpp_txt = array(
        'cart_button_label' => get_wpdmpp_option('a2cbtn_label', '<i class="fas fa-shopping-basket mr-2"></i>' . __('Add To Cart', 'wpdm-premium-packages')),
        'pay_now' => get_wpdmpp_option('cobtn_label', __('Complete Purchase', 'wpdm-premium-packages')),
        'checkout_button_label' => get_wpdmpp_option('cobtn_label', __('Complete Purchase', 'wpdm-premium-packages')),
    );

    ?>
    <script>
        var wpdmpp_base_url = '<?php echo plugins_url('/wpdm-premium-packages/'); ?>';
        var wpdmpp_currency_sign = '<?php echo wpdmpp_currency_sign(); ?>';
        var wpdmpp_csign_before = '<?php echo wpdmpp_currency_sign_position() == 'before' ? wpdmpp_currency_sign() : ''; ?>';
        var wpdmpp_csign_after = '<?php echo wpdmpp_currency_sign_position() == 'after' ? wpdmpp_currency_sign() : ''; ?>';
        var wpdmpp_currency_code = '<?php echo wpdmpp_currency_code(); ?>';
        var wpdmpp_cart_url = '<?php echo wpdmpp_cart_page(); ?>';

        var wpdmpp_txt = <?php echo json_encode($wpdmpp_txt); ?>;

    </script>
    <style>p.wpdmpp-notice {
            margin: 5px;
        }</style>
    <?php
}


function wpdmpp_delete_frontend_order()
{
    if (!wp_verify_nonce($_REQUEST['nonce'], NONCE_KEY)) {
        exit("No naughty business please");
    }

    $result['type'] = 'failed';
    global $wpdb;
    $order_id = sanitize_text_field(esc_sql($_REQUEST['order_id']));
    $uid = get_current_user_id();
    $ret = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_orders WHERE order_id = %s and uid='$uid'", $order_id));

    if ($ret) {
        $ret = $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ahm_order_items WHERE oid = %s", $order_id));

        if ($ret) $result['type'] = 'success';
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $result = json_encode($result);
        echo $result;
    } else {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
    }

    die();
}


/**
 * Update Guest Billing Info
 */
function wpdmpp_update_guest_billing()
{
    $billinginfo = array
    (
        'first_name' => '',
        'last_name' => '',
        'company' => '',
        'address_1' => '',
        'address_2' => '',
        'city' => '',
        'postcode' => '',
        'country' => '',
        'state' => '',
        'order_email' => '',
        'email' => '',
        'phone' => '',
        'taxid' => ''
    );
    $sbillinginfo = wpdm_sanitize_array($_POST['billing']);
    $billinginfo = shortcode_atts($billinginfo, $sbillinginfo);
    \WPDMPP\Libs\Order::Update(array('billing_info' => serialize($billinginfo)), \WPDM\Session::get('guest_order'));
    die('Saved!');
}

function wpdmpp_recalculate_sales()
{
    if (!isset($_POST['id'])) return;
    global $wpdb;
    $id = (int)$_POST['id'];
    $sql = "select sum(quantity*price) as sales_amount, sum(quantity) as sales_quantity from {$wpdb->prefix}ahm_order_items oi, {$wpdb->prefix}ahm_orders o where oi.oid = o.order_id and oi.pid = {$id} and o.order_status IN ('Completed', 'Expired')";
    $data = $wpdb->get_row($sql);

    header('Content-type: application/json');
    update_post_meta($id, '__wpdm_sales_amount', $data->sales_amount);
    update_post_meta($id, '__wpdm_sales_count', $data->sales_quantity);
    $data->sales_amount = wpdmpp_currency_sign() . floatval($data->sales_amount);
    $data->sales_quantity = intval($data->sales_quantity);
    echo json_encode($data);
    die();
}

function wpdmpp_sales_price($pid)
{
    $sales_price = get_post_meta($pid, "__wpdm_sales_price", true);
    $sales_price_expire = get_post_meta($pid, "__wpdm_sales_price_expire", true);
    if ($sales_price_expire != '') {
        $sales_price_expire = strtotime($sales_price_expire);
        if (time() > $sales_price_expire && $sales_price_expire > 0) $sales_price = 0;
    }
    return number_format((double)$sales_price, 2, ".", "");
}

function wpdmpp_sales_price_info($product_id)
{
    $sales_price_expire = get_post_meta($product_id, '__wpdm_sales_price_expire', true);
    if ($sales_price_expire != '')
        $sales_price_expire = strtotime($sales_price_expire);
    $sales_price_info = $sales_price_expire != '' ? sprintf(__("Sales price will expire on %s", "wpdm-premium-packages"), date(get_option("date_format") . " H:i", $sales_price_expire)) : __("This is a discounted price for a limited time", "wpdm-premium-packages");
    $sales_price_info = apply_filters("wpdmpp_sales_price_info", $sales_price_info, $product_id, $sales_price_expire);
    return $sales_price_info;

}

/**
 * @param $pid
 * @return string
 */
function wpdmpp_effective_price($pid)
{
    global $current_user;
    if (get_post_type($pid) != 'wpdmpro') return 0;
    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $base_price = $base_price ? (double)$base_price : 0;
    $sales_price = wpdmpp_sales_price($pid);
    $price = (double)($sales_price) > 0 ? $sales_price : $base_price;
    $role = is_user_logged_in() && is_array($current_user->roles) && isset($current_user->roles[0]) ? $current_user->roles[0] : 'guest';
    $discount = maybe_unserialize(get_post_meta($pid, '__wpdm_discount', true));
    if (!is_array($discount) || count($discount) == 0) return number_format((float)$price, 2, ".", "");

    $discount[$role] = isset($discount[$role]) ? $discount[$role] : 0;
    $discount[$role] = (double)$discount[$role];
    $user_discount = (($price * $discount[$role]) / 100);
    $price -= $user_discount;

    if (!$price) $price = 0;
    return number_format($price, 2, ".", "");
}

/**
 * @param $pid
 * @return int|mixed
 */
function wpdmpp_role_discount($pid, $name = false)
{
    global $current_user, $wp_roles;
    $role_discount = 0;
    $role_name = '';
    //$role = ?$current_user->roles[0]:'guest';
    $discount = maybe_unserialize(get_post_meta($pid, '__wpdm_discount', true));

    $roles = $wp_roles->role_names;


    if (is_user_logged_in() && is_array($discount)) {
        foreach ($current_user->roles as $role) {
            if (isset($discount[$role]) && $discount[$role] > $role_discount) {
                $role_discount = $discount[$role];
                $role_name = isset($roles[$role]) ? $roles[$role] : $role;
            }
        }
    }
    if (!is_user_logged_in() && is_array($discount) && isset($discount['guest'])) $role_discount = $discount['guest'];
    if (!is_array($discount) || count($discount) == 0) return 0;

    return $name ? $role_name : $role_discount;
}


function wpdmpp_price_range($pid)
{
    $pre_licenses = wpdmpp_get_licenses();
    $license_infs = get_post_meta($pid, "__wpdm_license", true);
    $license_infs = maybe_unserialize($license_infs);
    $licprices = array();

    $base_price = get_post_meta($pid, "__wpdm_base_price", true);
    $sales_price = wpdmpp_sales_price($pid);
    $base_price = intval($sales_price) > 0 ? $sales_price : $base_price;

    foreach ($pre_licenses as $licid => $lic) {
        if (isset($license_infs[$licid]) && $license_infs[$licid]['active'] == 1) {
            $licprices[] = isset($license_infs[$licid]['price']) ? $license_infs[$licid]['price'] : $base_price;
        }
    }

    $price_range = wpdmpp_price_format((float)$base_price, true, true);

    if (count($licprices) > 1 && get_post_meta($pid, "__wpdm_enable_license", true) == 1) {
        sort($licprices);
        $fromprice = $licprices[0];
        $sales_price = wpdmpp_sales_price($pid);
        if ($sales_price < $fromprice && $sales_price > 0) $fromprice = $sales_price;
        $price_range = wpdmpp_price_format($fromprice, true, true) . " &mdash; " . wpdmpp_price_format(end($licprices), true, true);
    }
    return $price_range;
}

function wpdmpp_order_id()
{
    return \WPDM\Session::get('orderid');
}

function wpdmpp_currency_sign()
{
    $settings = get_option('_wpdmpp_settings');
    $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
    $cdata = \WPDMPP\Libs\Currencies::GetCurrency($currency);
    $sign = is_array($cdata) ? $cdata['symbol'] : '$';
    $sign = apply_filters("wpdmpp_currency_sign", $sign);
    return $sign;
}

function wpdmpp_currency_sign_position()
{
    $settings = get_option('_wpdmpp_settings');
    $currency_position = isset($settings['currency_position']) ? $settings['currency_position'] : 'before';
    return $currency_position;
}

function wpdmpp_currency_code()
{
    $settings = get_option('_wpdmpp_settings');
    $currency = isset($settings['currency']) ? $settings['currency'] : 'USD';
    $currency = apply_filters("wpdmpp_currency_code", $currency);
    return $currency;
}

/**
 * Validating download request using 'wpdm_onstart_download' WPDM hook
 * @param $package
 * @return mixed
 */
function wpdmpp_validate_download($package)
{

    $price = wpdmpp_effective_price($package['ID']);
    if (floatval($price) > 0) {

        // Check The Master Key
        if (wpdm_query_var('masterkey') !== '' && \WPDMPP\WPDMPremiumPackage::authorize_masterkey()) return $package;

        // Validate Download Key
        if (is_wpdmkey_valid($package['ID'], wpdm_query_var('_wpdmkey')) && get_wpdmpp_option('authorize_masterkey') === 1) return $package;
        if ((int)\WPDM\Session::get('__wpdmpp_authorized_download') === 1) return $package;

        WPDM_Messages::error('You do not have permission to download this file', 1);

    }

    return $package;

}

/**
 * Assign an order to specific user
 */
function wpdmpp_assign_user_2order()
{
    if (!current_user_can('manage_options') || !wp_verify_nonce($_REQUEST['__nonce'], NONCE_KEY)) {
        WPDM_Messages::error(__('Unauthorized Operation!', 'wpdm-premium-packages'), 1);
    }
    $wpdmpp_settings = get_option('_wpdmpp_settings');
    if (isset($_REQUEST['assignuser']) && isset($_REQUEST['order'])) {
        if (is_email($_REQUEST['assignuser']))
            $u = get_user_by('email', sanitize_email($_REQUEST['assignuser']));
        else
            $u = get_user_by('login', sanitize_text_field($_REQUEST['assignuser']));
        if (is_object($u) && isset($u->ID)) {
            $order = new \WPDMPP\Libs\Order();
            $oid = esc_attr($_REQUEST['order']);
            $order->Update(array('uid' => $u->ID), sanitize_text_field($oid));
            $logo = isset($settings['logo_url']) && $wpdmpp_settings['logo_url'] != "" ? "<img src='{$wpdmpp_settings['logo_url']}' alt='" . get_bloginfo('name') . "'/>" : get_bloginfo('name');
            $params = array(
                'date' => date(get_option('date_format'), time()),
                'homeurl' => home_url('/'),
                'sitename' => get_bloginfo('name'),
                'order_link' => "<a href='" . wpdmpp_orders_page('id=' . $oid) . "'>" . wpdmpp_orders_page('id=' . $oid) . "</a>",
                'register_link' => "<a href='" . wpdmpp_orders_page('orderid=' . $oid) . "'>" . wpdmpp_orders_page('orderid=' . $oid) . "</a>",
                'name' => $u->user_login,
                'orderid' => $oid,
                'to_email' => $u->user_email,
                'order_url' => wpdmpp_orders_page('id=' . $oid),
                'order_url_admin' => admin_url('edit.php?post_type=wpdmpro&page=orders&task=vieworder&id=' . $oid),
                'img_logo' => $logo
            );
            \WPDMPP\Libs\User::addCustomer($u);
            \WPDM\Email::send("purchase-confirmation", $params);
            die('<div class="color-green" style="padding: 7px 15px;margin: 0;border-radius: 2px">Order Is Linked to ' . esc_attr($_REQUEST['assignuser']) . "</div>");
        } else
            die('<div class="alert alert-danger" style="padding: 7px 15px;background: rgba(255,0,23,0.05);margin: 0;border-radius: 2px">' . __('User Not Found!', 'wpdm-premium-packages') . '</div>');
    }
}


function wpdmpp_download_order_note_attachment()
{
    global $current_user;
    if (!isset($_GET['_atcdl']) || !is_user_logged_in()) return false;
    $key = \WPDM\libs\Crypt::Decrypt(esc_attr($_GET['_atcdl']));
    $key = explode("|||", $key);
    $order = new \WPDMPP\Libs\Order($key[0]);
    if ($order->uid != $current_user->ID && !current_user_can('manage_options')) wp_die('Unauthorized Access');
    $files = $order->order_notes['messages'][$key[1]]['file'];
    $filename = preg_replace("/^[0-9]+?wpdm_/", "", wpdm_basename($key[2]));
    if (in_array($key[2], $files)) {
        wpdm_download_file(UPLOAD_DIR . $key[2], $filename);
        die();
    }
}

/**
 * Return array of country objects
 * @return array
 */
function wpdmpp_get_countries()
{
    global $wpdb;
    $countries = $wpdb->get_results("select * from {$wpdb->prefix}ahm_country order by country_name");

    return $countries;
}

/**
 * Return Premium Package Template Directory
 * @return string
 */
function wpdmpp_tpl_dir()
{
    return WPDMPP_TPL_DIR;
}

function wpdmpp_email_template_tags($tags)
{
    $tags["[#orderid#]"] = array('value' => '', 'desc' => 'Order ID');
    $tags["[#items#]"] = array('value' => '', 'desc' => 'List of purchased items');
    $tags["[#order_url#]"] = array('value' => '', 'desc' => 'Order URL');
    $tags["[#guest_order_url#]"] = array('value' => '', 'desc' => 'Guest Order URL');
    return $tags;
}

function wpdmpp_email_templates($templates)
{
    $templates['purchase-confirmation-guest'] = array(
        'label' => __('Purchase Confirmation - Guest', 'wpdmpro'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('Thanks For Your Purchase', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello ,<br/>Thanks for your order at [#sitename#].<br/>Your Order ID: [#orderid#]<br/>Purchased Items:<br/>[#items#]<br/>You need to create an account to access your order and to get future updates.<br/>Please click on the following link to create your account:<br/><a class="button green" style="display: block; text-align: center;" href="[#order_url#]">Signup</a>If you already have account simply click the above url and login<br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    $templates['purchase-confirmation'] = array(
        'label' => __('Purchase Confirmation', 'wpdmpro'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('Thanks For Your Purchase', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello ,<br/>Thanks for your order at [#sitename#].<br/>Your Order ID: [#orderid#]<br/>Purchased Items:<br/>[#items#]<br/>You can download your purchased item(s) from the following link:<br/><a href="[#order_url#]">[#order_url#]</a><br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    $templates['subscription-reminder'] = array(
        'label' => __('Subscription Reminder', 'wpdmpro'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('[#sitename#] Subscription Reminder', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello,<br/>Thanks for your continued support.<br/>We\'re sending this message to remind you that, as your subscription is active, your Order# [#orderid#] will be renewed automatically on [#expire_date#]. <br/><br/><strong>Associated Items:</strong><hr/>[#items#]<hr/><br/> <a href="[#order_url#]" style="display: block;text-align: center" class="button">Review Order</a><br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    $templates['renew-confirmation'] = array(
        'label' => __('Order Renew Confirmation', 'wpdmpro'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('Order Renewed Successfully', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello,<br/>Thanks for your continued support.<br/>Your Order# [#orderid#] is renewed successfully.<br/>As always, you can download the latest version from the following link:<br/><a href="[#order_url#]">[#order_url#]</a><br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    $templates['sale-notification'] = array(
        'label' => __('New Sale Notification', 'wpdmpro'),
        'for' => 'admin',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('Congratulations! You have a sale.', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'to_email' => get_option('admin_email'),
            'message' => 'Hello ,<br/>Congratulations! You have a sale just now.<br/>Order ID: [#orderid#]<br/>Sold Items:<br/>[#items#]<br/>Review Order: [#order_url_admin#]'
        )
    );

    $templates['os-notification'] = array(
        'label' => __('Order Status Notification', 'wpdmpro'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('Order ([#orderid#]) Status Changed', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello ,<br/>The order <strong>[#orderid#]</strong> is changed to <strong>[#order_status#]</strong><br/>Review Order: <a href="[#order_url#]">[#order_url#]</a><br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    $templates['order-expire'] = array(
        'label' => __('Order Expiry Notification', 'wpdmpro'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array('subject' => __('Your order is about to expire', 'wpdmpro'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello [#name#],<br/>Your order is about to expire.<br/>Order# [#orderid#]<br/><br/>Purchased Items# [#order_items#]<br/>Please renew your order to get continuous support and updates.<br/><a class="button" href="[#order_url#]">Renew Order</a><br/><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    $templates['email-saved-cart'] = array(
        'label' => __('Email Saved Cart', 'wpdm-premium-packages'),
        'for' => 'customer',
        'plugin' => 'Premium Packages',
        'default' => array(
            'subject' => __('Someone sent you a cart!', 'wpdm-premium-packages'),
            'from_name' => get_option('blogname'),
            'from_email' => get_option('admin_email'),
            'message' => 'Hello,<br/>Someone sent you a cart from [#sitename#]:<br/>View Cart & Checkout from here:<br/><b><a href="[#carturl#]">[#carturl#]</a></b><br/>Best Regards,<br/>Sales Team<br/><b>[#sitename#]</b>'
        )
    );

    return $templates;
}

function wpdmpp_reactivate()
{
    return __("Database error detected. Please try deactivate and then reactivating plugin.", "wpdm-premium-packages");
}

function wpdmpp_expiry_check()
{
    $order = new \WPDMPP\Libs\Order();
    $uid = get_current_user_id();
    $orders = $order->getOrders($uid);
    foreach ($orders as $_order) {
        $expire_date = $_order->expire_date > 0 ? $_order->expire_date : $_order->date + (get_wpdmpp_option('order_validity_period', 365) * 86400);
        if (time() > $expire_date && $_order->order_status != 'Expired') {
            $order->Update(array('order_status' => 'Expired', 'payment_status' => 'Expired', 'expire_date' => $expire_date), $_order->order_id);
        }
    }

}

function wpdmpp_sanitize_alphanum($id)
{
    return preg_replace('/[^a-zA-Z0-9 -]/', "", $id);
}

/**
 * @usage Format price
 * @param $price
 * @return string
 */
function wpdmpp_price_format($price, $currency_sign = true, $thousand_separator = true)
{
    $ts = $thousand_separator ? get_wpdmpp_option('thousand_separator') : '';
    $ds = $thousand_separator ? get_wpdmpp_option('decimal_separator') : '.';
    $dp = $thousand_separator ? (int)get_wpdmpp_option('decimal_points') : 2;
    $currency_sign = $currency_sign ? wpdmpp_currency_sign() : '';
    $price = (double)$price;
    $price = number_format($price, $dp, $ds, $ts);
    return (get_wpdmpp_option('currency_position', 'before') === 'before') ? $currency_sign . $price : $price . $currency_sign;
}

function wpdmppdl_encode($content) {
    $content = json_encode($content);
    $content = base64_encode($content);
    $content = trim($content, '=');
    return $content;
}
function wpdmppdl_decode($cyper) {
    $jsonstr = base64_decode($cyper);
    $json = json_decode($jsonstr, true);
    return $json;
}

/**
 * @usage Generate ordinal number
 * @param $number
 * @return string
 */
function wpdmpp_ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}


