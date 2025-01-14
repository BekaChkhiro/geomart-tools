<?php

if (!defined('ABSPATH')) {
    exit;
}

class GeoMart_Tools_Settings {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_geomart-tools' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'geomart-tools-admin',
            plugins_url('css/admin.css', __FILE__),
            array(),
            '1.0.0'
        );

        wp_enqueue_script(
            'geomart-tools-admin',
            plugins_url('js/admin.js', __FILE__),
            array('jquery'),
            '1.0.0',
            true
        );
    }

    public function add_admin_menu() {
        add_menu_page(
            __('GeoMart Tools', 'geomart-tools'),
            __('GeoMart Tools', 'geomart-tools'),
            'manage_options',
            'geomart-tools',
            array($this, 'render_settings_page'),
            'dashicons-admin-generic',
            30
        );
    }

    public function register_settings() {
        register_setting('geomart_tools_options', 'geomart_tools_settings');

        add_settings_section(
            'geomart_tools_general',
            __('General Settings', 'geomart-tools'),
            array($this, 'render_section_description'),
            'geomart-tools'
        );

        // Add your general settings fields here
        add_settings_field(
            'example_field',
            __('Example Setting', 'geomart-tools'),
            array($this, 'render_example_field'),
            'geomart-tools',
            'geomart_tools_general'
        );
    }

    public function render_section_description() {
        echo '<p>' . __('Configure your GeoMart Tools settings here.', 'geomart-tools') . '</p>';
    }

    public function render_example_field() {
        $options = get_option('geomart_tools_settings');
        $value = isset($options['example_field']) ? $options['example_field'] : '';
        ?>
        <input type="text" name="geomart_tools_settings[example_field]" value="<?php echo esc_attr($value); ?>" class="regular-text">
        <?php
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        $tabs = apply_filters('geomart_tools_tabs', array(
            'general' => __('General', 'geomart-tools')
        ));
        ?>
        <div class="wrap geomart-tools-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="geomart-tools-container">
                <div class="geomart-tools-tabs">
                    <?php foreach ($tabs as $tab_id => $tab_name) : ?>
                        <a href="?page=geomart-tools&tab=<?php echo esc_attr($tab_id); ?>" 
                           class="geomart-tab <?php echo $current_tab === $tab_id ? 'active' : ''; ?>">
                            <?php echo esc_html($tab_name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div class="geomart-tools-content">
                    <form action="options.php" method="post">
                        <?php
                        if ($current_tab === 'general') {
                            settings_fields('geomart_tools_options');
                            do_settings_sections('geomart-tools');
                        } else {
                            do_action('geomart_tools_tab_content_' . $current_tab);
                        }
                        submit_button();
                        ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
