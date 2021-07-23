<?php
/**
 * Plugin Name: WPDM - Elementor
 * Plugin URI: https://www.wpdownloadmanager.com/download/wpdm-elementor/
 * Description: Download Manger modules for Elementor
 * Version: 1.0.5
 * Author: WordPress Download Manager
 * Text Domain: wpdm-elementor
 * Author URI: https://www.wpdownloadmanager.com/
 */

use WPDM\Elementor\Main;

define("__WPDM_ELEMENTOR__", true);

require_once __DIR__.'/src/api/API.php';
require_once __DIR__.'/src/Main.php';

Main::getInstance();
