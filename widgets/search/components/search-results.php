<?php
function render_search_results() {
    ?>
    <div class="geomart-search-results">
        <div class="geomart-search-results-inner"></div>
        <div class="geomart-search-loading">
            <div class="geomart-spinner">
                <div class="geomart-spinner-ring"></div>
            </div>
            <div class="geomart-loading-text">
                <?php esc_html_e('მიმდინარეობს ძებნა...', 'geomart-tools'); ?>
            </div>
        </div>
        <div class="geomart-no-results" style="display: none;">
            <?php esc_html_e('პროდუქტი ვერ მოიძებნა', 'geomart-tools'); ?>
        </div>
    </div>

    <template id="geomart-search-item-template">
        <a href="{permalink}" class="geomart-search-item">
            <div class="geomart-search-item-image">
                <img src="{image}" alt="{title}">
            </div>
            <div class="geomart-search-item-content">
                <h4 class="geomart-search-item-title">{title}</h4>
                <div class="geomart-search-item-sku">{sku}</div>
                <div class="geomart-search-item-meta">
                    <div class="geomart-search-item-price">{price}</div>
                    <div class="stock-status {stock_status}">{stock_text}</div>
                </div>
            </div>
        </a>
    </template>

    <template id="geomart-view-all-template">
        <a href="{search_url}" class="geomart-view-all">
            <?php esc_html_e('ყველა შედეგის ნახვა', 'geomart-tools'); ?> ({total_count})
        </a>
    </template>
    <?php
}
