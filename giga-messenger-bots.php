<?php
/*
Plugin Name: Giga Messenger Bots
Plugin URI: https://giga.ai
Description: Rapid Messenger Bot for Developers & Marketers.
Version: 2.2.2
Author: Giga AI <hello@giga.ai>
Author URI: http://binaty.org
License: GPL2+
*/

// Prevent loading this file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//----------------------------------------------------------
// Define plugin URL for loading static files or doing AJAX
//------------------------------------------------------------
if ( ! defined( 'GIGA_URL' ) ) {
	define( 'GIGA_URL', plugin_dir_url( __FILE__ ) );
}

define( 'GIGA_JS_URL', trailingslashit( GIGA_URL . 'assets/js' ) );
define( 'GIGA_CSS_URL', trailingslashit( GIGA_URL . 'assets/css' ) );

// ------------------------------------------------------------
// Plugin paths, for including files
// ------------------------------------------------------------
if ( ! defined( 'GIGA_DIR' ) ) {
	define( 'GIGA_DIR', plugin_dir_path( __FILE__ ) );
}

define( 'GIGA_INC_DIR', trailingslashit( GIGA_DIR . 'inc' ) );

require_once GIGA_INC_DIR . 'Session.php';
require_once GIGA_INC_DIR . 'bootstrap.php';

// Load Shortcodes
include GIGA_INC_DIR . 'shortcodes/class-post-generic.php';
include GIGA_INC_DIR . 'shortcodes/class-random-text.php';

include GIGA_INC_DIR . 'class-main.php';

// These modules load on Dashboard only
if ( is_admin() ) {

	// Always check Migration first
	include GIGA_INC_DIR . 'class-migration.php';

	// Settings Page
	include GIGA_INC_DIR . 'settings/class-settings.php';

	// Builder Module
	include GIGA_INC_DIR . 'builder/class-builder.php';

	// CRM Module
	include GIGA_INC_DIR . 'crm/class-lead-list-table.php';
	include GIGA_INC_DIR . 'crm/class-crm.php';
	include GIGA_INC_DIR . 'crm/class-lead-page.php';
}

// Account Linking Module
if (file_exists(GIGA_INC_DIR . 'account/class-linking.php')) {
    include GIGA_INC_DIR . 'account/class-linking.php';
}

// Subscription & Notification Module
if (file_exists(GIGA_INC_DIR . 'subscription/class-subscription.php')) {
    include GIGA_INC_DIR . 'subscription/class-subscription.php';
    include GIGA_INC_DIR . 'subscription/class-routine.php';
}

// Rest Module
include GIGA_INC_DIR . 'class-rest.php';

new GigaAI\Main;