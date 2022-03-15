<?php

namespace Action;

use NF_Abstracts_Action;

if (!defined('ABSPATH') || !class_exists('NF_Abstracts_Action')) exit;

/**
 * Class KAP_NF_action
 */
class KAP_NF_action extends NF_Abstracts_Action
{


    /**
     * @var string
     */
    public $_name = 'kap_strip';


    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */

    protected $_timing = 'normal';

    /**
     * @var int
     */
    protected $_priority = '10';

    protected $_current_form_id = '';

    protected $_pub_key = '';
    protected $_sec_key = '';
    protected $_old_pub_key = '';
    protected $_old_sec_key = '';


    private $active_script = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->_nicename = __('KAP Stripe');
        add_action('nf_admin_init', array($this, 'init_admin_settings'));

    }



    public
    function init_admin_settings()
    {


        $settings = [
            'kap_test' => array(
                'name' => 'kap_test',
                'type' => 'toggle',
                'label' => esc_html__('Test Key Strip', 'ninja-forms'),
                'width' => 'full',
                'group' => 'primary',
                'help' => '',
                'default' => 0,
                'value' => 1,
            ),

            'kap_test_pub_key' => array(
                'name' => 'kap_test_pub_key',
                'type' => 'textbox',
                'group' => 'primary',
                'label' => esc_html__('Test Publishable Key', 'ninja-forms'),
                'width' => 'full',

                'deps' => array(
                    'settings' => array(
                        array('name' => 'kap_test', 'value' => 1),
                    ),
                    'match' => 'all',
                ),
            ),
            'kap_test_sec_key' => array(
                'name' => 'kap_test_sec_key',
                'type' => 'textbox',
                'group' => 'primary',
                'label' => esc_html__('Test Secret Key', 'ninja-forms'),
                'width' => 'full',
                'deps' => array(
                    'settings' => array(
                        array('name' => 'kap_test', 'value' => 1),
                    ),
                    'match' => 'all',
                ),
            ),

            'kap_live_pub_key' => array(
                'name' => 'kap_live_pub_key',
                'type' => 'textbox',
                'group' => 'primary',
                'label' => esc_html__('Live Publishable Key', 'ninja-forms'),
                'width' => 'full',

                'deps' => array(
                    'settings' => array(
                        array('name' => 'kap_test', 'value' => 0),
                    ),
                    'match' => 'all',
                ),
            ),
            'kap_live_sec_key' => array(
                'name' => 'kap_live_sec_key',
                'type' => 'textbox',
                'group' => 'primary',
                'label' => esc_html__('Live Secret Key', 'ninja-forms'),
                'width' => 'full',
                'deps' => array(
                    'settings' => array(
                        array('name' => 'kap_test', 'value' => 0),
                    ),
                    'match' => 'all',
                ),
            ),
        ];

        $this->_settings = array_merge($this->_settings, $settings);


    }


    public
    function process($action_id, $form_id, $data)
    {


    }


}
