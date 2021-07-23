<?php
if(!defined('ABSPATH')) die('!');
$burl = get_permalink();
$sap = strpos($burl, '?') ? '&' : '?';

if(isset($params['view']) && $params['view'] === 'table')
    include wpdm_tpl_path("list-packages-table.php");
else
    include wpdm_tpl_path("list-packages-panel.php");