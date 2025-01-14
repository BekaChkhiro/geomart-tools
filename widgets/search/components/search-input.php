<?php
function render_search_input($settings = array()) {
    $placeholder = $settings['search_placeholder'] ?? 'პროდუქტის ძებნა...';
    $widget_id = $settings['widget_id'] ?? '';
    ?>
    <div class="geomart-search-input-wrapper">
        <span class="geomart-search-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </span>
        <input type="text" 
               class="geomart-search-input" 
               placeholder="<?php echo esc_attr($placeholder); ?>"
               data-widget-id="<?php echo esc_attr($widget_id); ?>" />
        <div class="geomart-search-loading"></div>
        <div class="geomart-search-close"></div>
    </div>
    <?php
}
