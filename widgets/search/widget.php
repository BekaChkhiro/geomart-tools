<?php

class GeoMart_Search_Widget extends \Elementor\Widget_Base {
    private $widget_dir;
    private $assets_url;

    public function __construct($data = [], $args = null) {
        parent::__construct($data, $args);

        $this->widget_dir = GEOMART_TOOLS_PATH . 'widgets/search/';
        $this->assets_url = GEOMART_TOOLS_URL . 'widgets/search/assets/';
        
        // Include components
        require_once $this->widget_dir . 'components/search-input.php';
        require_once $this->widget_dir . 'components/search-results.php';
        
        // Include AJAX handler
        require_once $this->widget_dir . 'ajax-handler.php';

        // Register styles
        wp_register_style(
            'geomart-search-main',
            $this->assets_url . 'css/main.css',
            [],
            '1.0.0'
        );

        wp_register_style(
            'geomart-search-input',
            $this->assets_url . 'css/search-input.css',
            ['geomart-search-main'],
            '1.0.0'
        );

        wp_register_style(
            'geomart-search-results',
            $this->assets_url . 'css/search-results.css',
            ['geomart-search-main'],
            '1.0.0'
        );

        // Register scripts
        wp_register_script(
            'geomart-search',
            $this->assets_url . 'js/search.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('geomart-search', 'geomart_search', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('geomart_search_nonce')
        ]);
    }

    public function get_name() {
        return 'geomart_search';
    }

    public function get_title() {
        return __('GeoMart Search', 'geomart-tools');
    }

    public function get_icon() {
        return 'eicon-search';
    }

    public function get_categories() {
        return ['general'];
    }

    public function get_script_depends() {
        return ['geomart-search'];
    }

    public function get_style_depends() {
        return ['geomart-search-main', 'geomart-search-input', 'geomart-search-results'];
    }

    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'geomart-tools'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'placeholder',
            [
                'label' => __('Search Placeholder', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'პროდუქტის ძებნა...',
            ]
        );

        $this->add_control(
            'in_stock_text',
            [
                'label' => __('In Stock Text', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'მარაგშია',
            ]
        );

        $this->add_control(
            'out_of_stock_text',
            [
                'label' => __('Out of Stock Text', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'არ არის მარაგში',
            ]
        );

        $this->add_control(
            'sku_label',
            [
                'label' => __('SKU Label', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => 'SKU',
            ]
        );

        $this->end_controls_section();

        // Style Section
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'geomart-tools'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'input_background',
            [
                'label' => __('Input Background', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .geomart-search-input' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'input_text_color',
            [
                'label' => __('Input Text Color', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .geomart-search-input' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $settings['widget_id'] = $this->get_id();

        // Add search nonce to JS
        wp_localize_script('geomart-search', 'geomart_search', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('geomart_search_nonce')
        ));
        
        // Render the search input
        render_search_input($settings);
        
        // Render the search results container
        echo '<div class="geomart-search-results"></div>';
    }
}

// Register the widget
function register_geomart_search_widget($widgets_manager) {
    $widgets_manager->register(new GeoMart_Search_Widget());
}
add_action('elementor/widgets/register', 'register_geomart_search_widget');
