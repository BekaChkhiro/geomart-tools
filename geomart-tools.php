<?php
/**
 * Plugin Name: GeoMart Tools
 * Description: Custom tools and widgets for GeoMart website
 * Version: 1.0.0
 * Author: GeoMart
 * Text Domain: geomart-tools
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GEOMART_TOOLS_PATH', plugin_dir_path(__FILE__));
define('GEOMART_TOOLS_URL', plugin_dir_url(__FILE__));

// Include files
require_once GEOMART_TOOLS_PATH . 'admin/settings.php';

// Initialize admin settings
add_action('plugins_loaded', function() {
    GeoMart_Tools_Settings::get_instance();
});

// Initialize Elementor widgets
function geomart_tools_widgets_init() {
    // Check if Elementor is installed and activated
    if (!did_action('elementor/loaded')) {
        add_action('admin_notices', function() {
            if (current_user_can('activate_plugins')) {
                echo '<div class="notice notice-warning is-dismissible"><p>' . 
                     sprintf(__('GeoMart Tools requires Elementor to be installed and activated. <a href="%s">Please install Elementor</a>.', 'geomart-tools'), 
                     admin_url('plugin-install.php?s=elementor&tab=search&type=term')) . 
                     '</p></div>';
            }
        });
        return;
    }

    // Load widget files
    require_once GEOMART_TOOLS_PATH . 'widgets/search/widget.php';
    require_once GEOMART_TOOLS_PATH . 'widgets/text-editor/widget.php';
    
    // Register widgets
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \GeoMart_Search_Widget());
    \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \GeoMart_Text_Editor());
}

// Enqueue widget styles
function geomart_tools_enqueue_styles() {
    wp_enqueue_style(
        'geomart-search-main',
        GEOMART_TOOLS_URL . 'widgets/search/assets/css/main.css',
        [],
        '1.0.0'
    );

    wp_enqueue_style(
        'geomart-search-input',
        GEOMART_TOOLS_URL . 'widgets/search/assets/css/search-input.css',
        ['geomart-search-main'],
        '1.0.0'
    );

    wp_enqueue_style(
        'geomart-search-results',
        GEOMART_TOOLS_URL . 'widgets/search/assets/css/search-results.css',
        ['geomart-search-main'],
        '1.0.0'
    );
}

add_action('init', 'geomart_tools_widgets_init');
add_action('wp_enqueue_scripts', 'geomart_tools_enqueue_styles');
