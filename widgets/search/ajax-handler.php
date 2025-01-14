<?php
if (!defined('ABSPATH')) {
    exit;
}

function geomart_ajax_search_products() {
    // Check nonce for security
    check_ajax_referer('geomart_search_nonce', 'nonce');

    $search_term = sanitize_text_field($_POST['search_term']);
    $widget_id = isset($_POST['widget_id']) ? sanitize_text_field($_POST['widget_id']) : '';
    $widget_settings = [];
    
    if ($widget_id) {
        $elementor_data = get_post_meta(get_the_ID(), '_elementor_data', true);
        if ($elementor_data) {
            $elementor_data = json_decode($elementor_data, true);
            foreach ($elementor_data as $element) {
                if (isset($element['elements'])) {
                    foreach ($element['elements'] as $widget) {
                        if (isset($widget['id']) && $widget['id'] === $widget_id) {
                            $widget_settings = $widget['settings'];
                            break 2;
                        }
                    }
                }
            }
        }
    }
    
    // First try to find by SKU
    $sku_query = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'meta_query' => array(
            array(
                'key' => '_sku',
                'value' => $search_term,
                'compare' => 'LIKE'
            )
        )
    ));

    // If no results found by SKU, search by title
    if (!$sku_query->have_posts()) {
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            's' => $search_term,
            'posts_per_page' => 12
        );
        $query = new WP_Query($args);
    } else {
        $query = $sku_query;
    }

    $products = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $product = wc_get_product(get_the_ID());
            
            if (!$product) continue;

            $stock_status = $product->get_stock_status();
            $is_in_stock = $stock_status === 'instock';
            
            // Get stock text from widget settings or use defaults
            $stock_text = $is_in_stock ? 
                ($widget_settings['in_stock_text'] ?? __('მარაგშია', 'geomart-tools')) : 
                ($widget_settings['out_of_stock_text'] ?? __('არ არის მარაგში', 'geomart-tools'));

            $sku = $product->get_sku();
            $title = $product->get_name();
            if ($sku) {
                $sku_label = $widget_settings['sku_label'] ?? __('SKU', 'geomart-tools');
                $title .= ' (' . $sku_label . ': ' . $sku . ')';
            }

            $products[] = array(
                'id' => $product->get_id(),
                'title' => $title,
                'price' => $product->get_price_html(),
                'permalink' => get_permalink(),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: wc_placeholder_img_src(),
                'in_stock' => $is_in_stock,
                'stock_status' => $stock_status,
                'stock_text' => $stock_text,
                'sku' => $sku
            );
        }
        wp_reset_postdata();
    }

    // Get total count of all matching products
    $total_query = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => $search_term,
        'fields' => 'ids'
    ));

    $response = array(
        'success' => true,
        'data' => array(
            'products' => $products,
            'total_count' => $total_query->found_posts,
            'search_url' => add_query_arg('s', urlencode($search_term), get_permalink(wc_get_page_id('shop')))
        )
    );

    wp_send_json($response);
}

add_action('wp_ajax_geomart_search_products', 'geomart_ajax_search_products');
add_action('wp_ajax_nopriv_geomart_search_products', 'geomart_ajax_search_products');
