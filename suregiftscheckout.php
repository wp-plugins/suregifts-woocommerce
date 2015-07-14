<?php
/*
   Plugin Name: SureGifts WooCommerce
   Plugin URI: http://wordpress.org/extend/plugins/suregiftscheckout/
   Version: 3.0
   Author: SureGifts
   Description: Integrates SureGifts gift card codes with your store’s coupon/discount field on cart/checkout page
   Text Domain: Suregifts Woocommerce
   License: GPLv3
   Author URI: http://www.suregifts.com.ng/
   Copyright (c) 2014 suregifts inc All rights reserved.
  */


$Suregiftscheckout_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function Suregiftscheckout_noticePhpVersionWrong() {
    global $Suregiftscheckout_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "suregiftscheckout" requires a newer version of PHP to be running.',  'suregiftscheckout').
            '<br/>' . __('Minimal version of PHP required: ', 'suregiftscheckout') . '<strong>' . $Suregiftscheckout_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'suregiftscheckout') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function Suregiftscheckout_PhpVersionCheck() {
    global $Suregiftscheckout_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $Suregiftscheckout_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'Suregiftscheckout_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Suregiftscheckout_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('suregiftscheckout', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// First initialize i18n
Suregiftscheckout_i18n_init();


// Next, run the version check.
// If it is successful, continue with initialization for this plugin
if (Suregiftscheckout_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('suregiftscheckout_init.php');
    Suregiftscheckout_init(__FILE__);
}

