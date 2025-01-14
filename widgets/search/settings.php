<?php

if (!defined('ABSPATH')) {
    exit;
}

class GeoMart_Search_Settings {
    private static $instance = null;
    private $option_name = 'geomart_tools_search_settings';
    private $page_slug = 'geomart-tools';
    private $section_id = 'geomart_tools_search';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('geomart_tools_tabs', array($this, 'add_search_tab'));
        add_action('geomart_tools_tab_content_search', array($this, 'render_settings'));
    }

    public function add_search_tab($tabs) {
        $tabs['search'] = __('Search Widget', 'geomart-tools');
        return $tabs;
    }

    private function register_settings() {
        register_setting($this->page_slug, $this->option_name);

        add_settings_section(
            $this->section_id,
            __('Search Widget Settings', 'geomart-tools'),
            array($this, 'render_section_description'),
            $this->page_slug
        );
    }

    public function render_section_description() {
        echo '<p>' . __('Search widget settings.', 'geomart-tools') . '</p>';
    }

    public function render_settings() {
        // Register settings only when rendering the search tab
        $this->register_settings();
        
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="geomart-search-settings">
            <?php
            settings_fields($this->page_slug);
            do_settings_sections($this->page_slug);
            ?>
        </div>
        <?php
    }
}

// Initialize the settings
GeoMart_Search_Settings::get_instance();
