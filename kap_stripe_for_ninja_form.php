<?php
/**
 * @package KAP Stripe for Ninja form
 * @version 0.01.07
 */

/**
 * Plugin Name: KAP Stripe for Ninja form
 * Plugin URI: https://webhelpagency.com/
 * Description: Ninja form - Strips connect to one form
 * Author: Andrii Omelianenko <omelianenko993@gmail.com> ,WHA
 * Version: 0.01.01
 * Author URI: https://webhelpagency.com/
 **/
require_once __DIR__ . '/vendor/autoload.php';

function KAP_NF_strip()
{
    return KAP_NF_init::instance();

}


add_action('plugins_loaded', 'kap_strips_activate_init');
function kap_strips_activate_init()
{
    if (!is_plugin_active('ninja-forms/ninja-forms.php')) {

        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-error">
                <p>
                    <?php esc_html_e('Plugin not works KAP Stripe for Ninja form  . Please active plugin MEC  ', ' kiwanis_academy_addons'); ?>
                </p>
            </div>
            <?php
        });
    } elseif (!is_plugin_active('ninja-forms-stripe/ninja-forms-stripe.php')) {

        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-error">
                <p>
                    <?php esc_html_e('Plugin not works KAP Stripe for Ninja form  . Please active plugin  inja Forms - Stripe  ', ' kiwanis_academy_addons'); ?>
                </p>
            </div>
            <?php
        });
    } else {
        KAP_NF_strip();
    }


}
