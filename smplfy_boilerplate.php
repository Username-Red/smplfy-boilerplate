<?php
/**
 * Plugin Name: SMPLFY Boiler Plate
 * Version: 1.0.3
 * Description: Starter plugin with custom plugin framework to get started
 * Author: Thomas Picolo-Donnelly and Jared Greeff
 * Author URI: https://simplifybiz.com/
 * Requires PHP: 7.4
 * Requires Plugins:  smplfy-core
 *
 * @package Bliksem
 * @author Thomas Picolo-Donnelly
 * @since 0.0.1
 */

namespace SMPLFY\boilerplate;

prevent_external_script_execution();

define( 'SITE_URL', get_site_url() );
define( 'SMPLFY_NAME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SMPLFY_NAME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// 1. Load utilities
require_once SMPLFY_NAME_PLUGIN_DIR . 'admin/utilities/smplfy_require_utilities.php';

// 2. Load the bootstrap file
// NOTE: Verify that the file name is EXACTLY smplfy_bootstrap.php in your includes folder
require_once SMPLFY_NAME_PLUGIN_DIR . 'includes/smplfy_bootstrap.php';

/**
 * TEST: If you see the text below when you refresh your site, the engine is working.
 * If the site loads normally, the "require_once" above is likely failing due to a path issue.
 * Once confirmed, comment out the line below.
 */
// die('SUCCESS: The main file is calling the bootstrap function correctly.');

// 3. Run the function that initializes the whole plugin
bootstrap_boilerplate_plugin();

function prevent_external_script_execution(): void {
    if ( ! function_exists( 'get_option' ) ) {
        header( 'HTTP/1.0 403 Forbidden' );
        die;
    }
}