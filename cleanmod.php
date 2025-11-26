<?php
/**
 * Plugin Name: CleanMod – AI Comment Moderation
 * Plugin URI: https://cleanmod.dev
 * Description: Uses CleanMod to detect toxic comments and automatically hold or block them.
 * Version: 0.1.0
 * Author: CleanMod
 * Author URI: https://cleanmod.dev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-cleanmod
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'CLEANMOD_VERSION', '0.1.0' );
define( 'CLEANMOD_API_BASE', 'https://cleanmod.dev' );
define( 'CLEANMOD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CLEANMOD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load dependencies
require_once CLEANMOD_PLUGIN_DIR . 'includes/class-cleanmod-client.php';
require_once CLEANMOD_PLUGIN_DIR . 'includes/class-cleanmod-settings.php';
require_once CLEANMOD_PLUGIN_DIR . 'includes/class-cleanmod-moderation.php';

/**
 * Initialize CleanMod plugin
 */
function cleanmod_init() {
	// Initialize settings
	$settings = new CleanMod_Settings();
	$settings->init();

	// Initialize moderation
	$moderation = new CleanMod_Moderation();
	$moderation->init();
}
add_action( 'plugins_loaded', 'cleanmod_init' );

