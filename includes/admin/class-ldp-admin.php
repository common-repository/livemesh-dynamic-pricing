<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class LDP_Admin {

    public function __construct() {

        // Initialize class
        add_action('admin_init', array($this, 'init'));

    }

    /**
     * Initialise hooks.
     *
     */
    public function init() {

        // Add to WC Screen IDs to load scripts.
        add_filter('woocommerce_screen_ids', array($this, 'add_screen_ids'));

        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));

        add_filter('plugin_action_links_' . plugin_basename(LDP_PLUGIN_FILE), array($this, 'add_plugin_action_links'), 10, 2);
    }


    /**
     *
     * Add the plugin pages to the screen IDs so the WooCommerce scripts are loaded.
     *
     *
     * @param array $screen_ids List of existing screen IDs.
     * @return array             List of modified screen IDs.
     */
    public function add_screen_ids($screen_ids) {

        $screen_ids[] = 'ldp_dynamic_pricing';

        return $screen_ids;
    }


    /**
     * Enqueue scripts and CSS files used by this plugin.
     *
     */
    public function admin_enqueue_scripts() {

        wp_register_style('livemesh-dynamic-pricing', plugins_url('assets/css/livemesh-dynamic-pricing.css', LDP_PLUGIN_FILE), array(), LDP_VERSION);

        wp_register_script('livemesh-dynamic-pricing', plugins_url('assets/js/livemesh-dynamic-pricing.js', LDP_PLUGIN_FILE), array(
            'jquery',
            'jquery-blockui',
            'jquery-ui-sortable',
            'select2',
            'lwc-repeater',
        ), LDP_VERSION);

        // Only load scripts on relevant pages
        if ((isset($_REQUEST['post']) && 'ldp_dynamic_pricing' == get_post_type($_REQUEST['post'])) ||
            (isset($_REQUEST['post_type']) && 'ldp_dynamic_pricing' == $_REQUEST['post_type']) ||
            (isset($_REQUEST['tab']) && 'ldp_pricing' == $_REQUEST['tab']) ||
            (isset($_GET['post']) && 'product' == get_post_type($_GET['post']))) :

            wp_localize_script('lwc-conditions', 'lwc2', array(
                'action_prefix' => 'ldp_',
            ));

            wp_localize_script('livemesh-dynamic-pricing', 'ldp', array(
                'post_id' => get_the_ID(),
                'nonce' => wp_create_nonce('lwc-ajax-nonce'),
            ));

            wp_enqueue_script('livemesh-dynamic-pricing');

            wp_enqueue_style('livemesh-dynamic-pricing');

            wp_enqueue_script('lwc-conditions');

        endif;
    }


    /**
     *
     * Add Dynamic Pricing tab link to the plugins.php page below the plugin name
     * and besides the 'activate', 'edit', 'delete' action links.
     *
     *
     * @param array $links List of existing links.
     * @param string $file Name of the current plugin being looped.
     * @return array         List of modified links.
     */
    public function add_plugin_action_links($links, $file) {

        $pricing_page[] = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=ldp_pricing')) . '">' . __('Settings', 'livemesh-dynamic-pricing') . '</a>';

        return array_merge($pricing_page, $links);;

    }

}
