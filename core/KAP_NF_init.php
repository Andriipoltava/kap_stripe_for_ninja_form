<?php

use Action\KAP_NF_action;

class KAP_NF_init
{
    /**
     * @var KAP_NF_init
     * @since 3.0
     */
    private static $instance;

    /**
     * Plugin Directory
     *
     * @since 3.0
     * @var string $dir
     */
    public static $dir = '';

    /**
     * Plugin URL
     *
     * @since 3.0
     * @var string $url
     */
    public static $url = '';

    public function __construct()
    {

        add_filter('ninja_forms_after_upgrade_settings', array($this, 'upgrade_settings'));
        add_filter('ninja_forms_register_actions', array($this, 'register_actions'), 999);

        add_action('init', array($this, 'init_settings_form'));


    }

    public function init_settings_form()
    {
        if (!is_admin()) {
            $keyword = url_to_postid((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            $content = get_post($keyword);
            $matches = array();

            preg_match_all("/(?<=\[ninja_form id=').+?(?='])/", $content->post_content, $matches);

            if (count($matches)) {

                $this->display_form_setting('20');
            }


        }


    }


    public function display_form_setting($form_id)

    {
        $test = false;
        $actions = Ninja_Forms()->form($form_id)->get_actions();
        foreach ($actions as $action) {
            $setting = $action->get_settings();
            if ($setting['type'] == 'kap_strip' && $setting['active'] === '1') {
                $test = $setting['kap_test'];
                $new_settings = array(

                    'test_secret_key' => $setting['kap_test_sec_key']?:'',
                    'test_publishable_key' => $setting['kap_test_pub_key']?:'',

                    // Live Credentials
                    'live_secret_key' => $setting['kap_live_sec_key']?:'',
                    'live_publishable_key' => $setting['kap_live_pub_key']?:'',
                );
            }
        }

        $array = [];
        $array['test_secret_key'] = Ninja_Forms()->get_setting('stripe_test_secret_key');
        $array['test_publishable_key'] = Ninja_Forms()->get_setting('stripe_test_publishable_key');
        $array['live_secret_key'] = Ninja_Forms()->get_setting('stripe_live_secret_key');
        $array['live_publishable_key'] = Ninja_Forms()->get_setting('stripe_live_publishable_key');



        if (empty($new_settings)
            && ((Ninja_Forms()->get_setting('kep_ninjaform_strip_old') && Ninja_Forms()->get_setting('stripe_test_secret_key'))
                || (count(array_diff(is_array(Ninja_Forms()->get_setting('kep_ninjaform_strip_' . $form_id)) ? Ninja_Forms()->get_setting('kep_ninjaform_strip_' . $form_id) : [], $array))))
        ) {
            $new_settings = Ninja_Forms()->get_setting('kep_ninjaform_strip_old');
            Ninja_Forms()->update_setting('kep_ninjaform_strip_old', false);
            Ninja_Forms()->update_setting('kep_ninjaform_strip_' . $form_id, false);
            if ($new_settings && is_array($new_settings))
                foreach ($new_settings as $name => $value) {
                    Ninja_Forms()->update_setting('stripe_' . $name, $value);

                }


            return true;
        }


        if (empty($new_settings))
            return true;

        $old_Strip = Ninja_Forms()->get_setting('kep_ninjaform_strip_old');
        if ($test === '1') {
            unset($array['live_secret_key']);
            unset($array['live_publishable_key']);
            unset($new_settings['live_secret_key']);
            unset($new_settings['live_publishable_key']);
            if (isset($old_Strip['live_secret_key']))
                unset($old_Strip['live_secret_key']);
            if (isset($old_Strip['live_publishable_key']))
                unset($old_Strip['live_publishable_key']);


        } elseif ($test === '0') {
            unset($array['test_secret_key']);
            unset($array['test_publishable_key']);
            unset($new_settings['test_secret_key']);
            unset($new_settings['test_publishable_key']);
            if (isset($old_Strip['test_secret_key']))
                unset($old_Strip['test_secret_key']);
            if (isset($old_Strip['test_publishable_key']))
                unset($old_Strip['test_publishable_key']);
        }


        foreach ($new_settings as $key => $item) {
            if ($item == '')
                unset($new_settings[$key]);
        }



        if (isset($new_settings) && count($new_settings)) {


            if (!Ninja_Forms()->get_setting('kep_ninjaform_strip_' . $form_id) || (!$old_Strip && !Ninja_Forms()->get_setting('kep_ninjaform_strip_' . $form_id)) ||
                !$this->arrayCheck($array, $old_Strip)
            ) {
                Ninja_Forms()->update_setting('kep_ninjaform_strip_' . $form_id, $new_settings);
                Ninja_Forms()->update_setting('kep_ninjaform_strip_old', $this->arrayCheck($array, $new_settings) ? $old_Strip : $array);
                if ($new_settings && is_array($new_settings))
                    foreach ($new_settings as $name => $value) {
                        Ninja_Forms()->update_setting('stripe_' . $name, $value);
                    }
            }
        }
    }

    public function arrayCheck(array $a, array $b): bool
    {
        $c = 0;
        foreach ($a as $key => $value) {
            if ($a[$key] == $b[$key]) {
                $c++;
            }
        }
        if (count($a) == $c) {
            return true;
        }

        return false;
    }


    function upgrade_settings($data)
    {

        return $data;

    }

    /**
     * Main Plugin Instance
     *
     * Insures that only one instance of a plugin class exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @return KAP_NF_init Highlander Instance
     * @since 3.0
     * @static
     * @static var array $instance
     */

    public static function instance()
    {
        if (!isset(self::$instance) && !(self::$instance instanceof KAP_NF_init)) {
            self::$instance = new KAP_NF_init();

            self::$dir = plugin_dir_path(__FILE__);

            self::$url = plugin_dir_url(__FILE__);

        }

        return self::$instance;
    }

    public function register_actions($actions)
    {

        $actions['kap_strip'] = new KAP_NF_action(); // KAP_NF_action.php
        return $actions;
    }


}
