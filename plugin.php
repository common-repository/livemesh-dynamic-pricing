<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!class_exists('Livemesh_Dynamic_Pricing')) :

    /**
     * Main Livemesh_Dynamic_Pricing Class
     *
     */
    final class Livemesh_Dynamic_Pricing {

        /** Singleton *************************************************************/

        private static $instance;

        /**
         * @var object|LDP_Post_Type
         */
        public $post_type;

        /**
         * @var object|LDP_Match_Conditions
         */
        public $matcher;

        /**
         * @var object|LDP_Pricing_Helper
         */
        public $pricing_helper;

        /**
         * @var object|LDP_Ajax
         */
        public $ajax;

        /**
         * @var object|LDP_Admin
         */
        public $admin;

        /**
         * @var object|LDP_Setttings
         */
        public $settings;

        /**
         * @var object|LDP_Product_Pricing_Panel
         */
        public $product_tab;

        /**
         * Livemesh_Dynamic_Pricing Singleton Instance
         *
         * Allow only one instance of the class to be created.
         */
        public static function instance() {

            if (!isset(self::$instance) && !(self::$instance instanceof Livemesh_Dynamic_Pricing)) {

                self::$instance = new Livemesh_Dynamic_Pricing;

                if (!self::$instance->is_woocommerce_active())
                    return;

                self::$instance->setup_debug_constants();

                add_action('plugins_loaded', array(self::$instance, 'load_plugin_textdomain'));

                self::$instance->includes();

                self::$instance->hooks();

                self::$instance->init();

                do_action( 'livemesh_dynamic_pricing_loaded' );

            }
            return self::$instance;
        }

        /**
         * Throw error if someone tries to clone the object since this is a singleton class
         *
         */
        public function __clone() {
            // Cloning instances of the class is forbidden
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'livemesh-dynamic-pricing'), '1.2.1');
        }

        /**
         * Disable deserialization
         */
        public function __wakeup() {

            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'livemesh-dynamic-pricing'), '1.2.1');
        }

        private function is_woocommerce_active(): bool {

            if (!function_exists('is_plugin_active'))
                require_once(ABSPATH . '/wp-admin/includes/plugin.php');

            if (!is_plugin_active('woocommerce/woocommerce.php') && !function_exists('WC')) {

                add_action('admin_notices', array($this, 'woocommerce_required_notice'));

                return false;
            }

            return true;
        }

        public function woocommerce_required_notice() {

            $class = 'notice notice-error';

            $message = esc_html__('WooCommerce is required for Livemesh Dynamic Pricing plugin to work. Please install or activate WooCommerce plugin', 'livemesh-dynamic-pricing');

            printf('<div class="%1$s"><p>%2$s</p></div>', $class, $message);

        }

        /**
         * Setup debug constants required for the plugin
         */
        private function setup_debug_constants() {

            $enable_debug = true;

            $settings = get_option('ldp_settings');

            if ($settings && isset($settings['ldp_enable_debug']) && $settings['ldp_enable_debug'] == "true")
                $enable_debug = true;

            // Enable script debugging
            if (!defined('LDP_SCRIPT_DEBUG')) {
                define('LDP_SCRIPT_DEBUG', $enable_debug);
            }

            // Minified JS file name suffix
            if (!defined('LDP_JS_SUFFIX')) {
                if ($enable_debug)
                    define('LDP_JS_SUFFIX', '');
                else
                    define('LDP_JS_SUFFIX', '.min');
            }
        }

        /**
         * Include required files
         *
         */
        private function includes() {

            require_once LDP_PLUGIN_DIR . '/includes/class-ldp-post-type.php';

            require_once LDP_PLUGIN_DIR . '/includes/class-ldp-pricing.php';

            require_once LDP_PLUGIN_DIR . '/includes/class-ldp-pricing-helper.php';

            require_once LDP_PLUGIN_DIR . '/includes/class-ldp-match-conditions.php';

            require_once LDP_PLUGIN_DIR . '/includes/utility-functions.php';

            require_once LDP_PLUGIN_DIR . '/includes/libraries/lwc-conditions/functions.php';

            if (is_admin()) {
                require_once LDP_PLUGIN_DIR . '/includes/admin/class-ldp-admin.php';

                require_once LDP_PLUGIN_DIR . '/includes/admin/class-ldp-settings-pricing-tab.php';
                require_once LDP_PLUGIN_DIR . '/includes/admin/class-ldp-pricing-condition.php';

                require_once LDP_PLUGIN_DIR . '/includes/admin/single-product/class-ldp-product-pricing-panel.php';
                require_once LDP_PLUGIN_DIR . '/includes/admin/single-product/class-ldp-product-pricing-condition.php';
            }

        }

        /**
         * Load Plugin Text Domain
         *
         * Looks for the plugin translation files in certain directories and loads
         * them to allow the plugin to be localised
         */
        public function load_plugin_textdomain() {

            $lang_dir = apply_filters('ldp_lang_dir', trailingslashit(LDP_PLUGIN_DIR . 'languages'));

            // Traditional WordPress plugin locale filter
            $locale = apply_filters('plugin_locale', get_locale(), 'livemesh-dynamic-pricing');
            $mofile = sprintf('%1$s-%2$s.mo', 'livemesh-dynamic-pricing', $locale);

            // Setup paths to current locale file
            $mofile_local = $lang_dir . $mofile;

            if (file_exists($mofile_local)) {
                // Look in the /wp-content/plugins/livemesh-dynamic-pricing/languages/ folder
                load_textdomain('livemesh-dynamic-pricing', $mofile_local);
            }
            else {
                // Load the default language files
                load_plugin_textdomain('livemesh-dynamic-pricing', false, $lang_dir);
            }

            return false;
        }

        /**
         * Setup the default hooks and actions
         */
        private function hooks() {

        }

        private function init() {
            
            $this->post_type = new LDP_Post_Type();
            
            $this->matcher = new LDP_Match_Conditions();

            $this->pricing_helper = new LDP_Pricing_Helper();

            if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
                require_once LDP_PLUGIN_DIR . '/includes/class-ldp-ajax.php';
                $this->ajax = new LDP_Ajax();
            }

            if ( is_admin() ) {
                $this->admin = new LDP_Admin();
                $this->settings = new LDP_Pricing_Tab();
                $this->product_pricing_tab = new LDP_Product_Pricing_Panel();
            }
        }

    }

endif; // End if class_exists check


/**
 * The main function responsible for returning the one true Livemesh_Dynamic_Pricing
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 */
function Livemesh_Dynamic_Pricing() {
    return Livemesh_Dynamic_Pricing::instance();
}

// Get Livemesh_Dynamic_Pricing Running
Livemesh_Dynamic_Pricing();