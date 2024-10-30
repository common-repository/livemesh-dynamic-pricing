<?php

/**
 * Plugin Name: Dynamic Pricing for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/livemesh-dynamic-pricing/
 * Description: Design custom pricing rules for specific products. Apply price adjustments like bulk pricing and discounts per user role etc.
 * Author: Livemesh
 * Author URI: https://livemeshwp.com/woocommerce-dynamic-pricing/
 * License: GPL3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Version: 1.2.1
 * WC requires at least: 5.2
 * WC tested up to: 6.9
 * Text Domain: livemesh-dynamic-pricing
 * Domain Path: languages
 *
 * Dynamic Pricing for WooCommerce is distributed under the terms of the GNU
 * General Public License as published by the Free Software Foundation,
 * either version 2 of the License, or any later version.
 *
 * Dynamic Pricing for WooCommerce is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Dynamic Pricing for WooCommerce. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 *
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'ldp_fs' ) ) {
    ldp_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    // Ensure the free version is deactivated if premium is running
    
    if ( !function_exists( 'ldp_fs' ) ) {
        // Plugin version
        define( 'LDP_VERSION', '1.2.1' );
        // Plugin Root File
        define( 'LDP_PLUGIN_FILE', __FILE__ );
        // Plugin Folder Path
        define( 'LDP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'LDP_PLUGIN_SLUG', dirname( plugin_basename( __FILE__ ) ) );
        // Plugin Folder URL
        define( 'LDP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
        // Plugin Help Page URL
        define( 'LDP_PLUGIN_HELP_URL', admin_url() . 'admin.php?page=livemesh_dynamic_pricing_documentation' );
        // Create a helper function for easy SDK access.
        function ldp_fs()
        {
            global  $ldp_fs ;
            
            if ( !isset( $ldp_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ldp_fs = fs_dynamic_init( array(
                    'id'             => '10244',
                    'slug'           => 'livemesh-dynamic-pricing',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_9406c166ce28f1fb6852164410a76',
                    'is_premium'     => false,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'first-path' => 'plugins.php',
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $ldp_fs;
        }
        
        // Init Freemius.
        ldp_fs();
        // Signal that SDK was initiated.
        do_action( 'ldp_fs_loaded' );
    }
    
    require_once dirname( __FILE__ ) . '/plugin.php';
}
