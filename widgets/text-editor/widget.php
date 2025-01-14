<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class GeoMart_Text_Editor extends \Elementor\Widget_Base {
    public function get_name() {
        return 'geomart_text_editor';
    }

    public function get_title() {
        return __('GeoMart Text Editor', 'geomart-tools');
    }

    public function get_icon() {
        return 'eicon-text-area';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'geomart-tools'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'text_content',
            [
                'label' => __('Text Content', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::WYSIWYG,
                'default' => __('Enter your text here', 'geomart-tools'),
                'placeholder' => __('Type your text here', 'geomart-tools'),
            ]
        );

        $this->add_control(
            'text_align',
            [
                'label' => __('Alignment', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'geomart-tools'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'geomart-tools'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'geomart-tools'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'toggle' => true,
                'selectors' => [
                    '{{WRAPPER}} .geomart-text-editor' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Tab
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Style', 'geomart-tools'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .geomart-text-editor',
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => __('Text Color', 'geomart-tools'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .geomart-text-editor' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        ?>
        <div class="geomart-text-editor">
            <?php echo wp_kses_post($settings['text_content']); ?>
        </div>
        <?php
    }

    protected function content_template() {
        ?>
        <div class="geomart-text-editor">
            {{{ settings.text_content }}}
        </div>
        <?php
    }
}
