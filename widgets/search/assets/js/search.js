jQuery(document).ready(function($) {
    let searchTimeout = null;
    const searchInput = $('.geomart-search-input');
    const searchResults = $('.geomart-search-results');
    const searchLoading = $('.geomart-search-loading');
    const searchClose = $('.geomart-search-close');
    const isMobile = window.matchMedia('(max-width: 767px)').matches;

    function performSearch(searchTerm, widgetId) {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        if (!isMobile && searchTerm.length < 2) {
            searchResults.hide();
            return;
        }

        searchTimeout = setTimeout(function() {
            searchLoading.show();
            
            $.ajax({
                url: geomart_search.ajax_url,
                type: 'POST',
                data: {
                    action: 'geomart_search_products',
                    search_term: searchTerm,
                    widget_id: widgetId,
                    nonce: geomart_search.nonce
                },
                success: function(response) {
                    searchLoading.hide();
                    
                    // Validate response structure
                    if (!response || !response.success || !response.data || !Array.isArray(response.data.products)) {
                        searchResults.html('<div class="geomart-no-results">პროდუქტი ვერ მოიძებნა</div>');
                        searchResults.show();
                        return;
                    }

                    if (response.data.products.length > 0) {
                        let resultsHtml = response.data.products.map(function(product) {
                            if (!product) return '';
                            
                            const skuHtml = product.sku ? `<span class="sku">${product.sku}</span>` : '';
                            const title = product.title || '';
                            const permalink = product.permalink || '#';
                            const image = product.image || '';
                            const price = product.price || '';
                            const stockText = product.stock_text || '';
                            const inStock = !!product.in_stock;
                            
                            return `
                                <a href="${permalink}" class="geomart-search-item">
                                    <div class="geomart-search-item-image">
                                        <img src="${image}" alt="${title}" />
                                    </div>
                                    <div class="geomart-search-item-content">
                                        <h4 class="geomart-search-item-title">${title}</h4>
                                        <div class="geomart-search-item-meta">
                                            <span class="price">${price}</span>
                                            <span class="stock-status ${inStock ? 'in-stock' : 'out-of-stock'}">${stockText}</span>
                                            ${skuHtml}
                                        </div>
                                    </div>
                                </a>
                            `;
                        }).join('');

                        // Add view all link if there are more results
                        if (response.data.total_count > response.data.products.length && response.data.search_url) {
                            resultsHtml += `<a href="${response.data.search_url}" class="geomart-view-all">
                                ყველა შედეგის ნახვა (${response.data.total_count})
                            </a>`;
                        }

                        searchResults.html(resultsHtml);
                        searchResults.show();
                    } else {
                        searchResults.html('<div class="geomart-no-results">პროდუქტი ვერ მოიძებნა</div>');
                        searchResults.show();
                    }
                },
                error: function() {
                    searchLoading.hide();
                    searchResults.html('<div class="geomart-no-results">შეცდომა ძებნისას</div>');
                    searchResults.show();
                }
            });
        }, isMobile ? 100 : 300); // Faster response on mobile
    }

    // Show results container on mobile when input is focused
    searchInput.on('focus', function() {
        if (isMobile) {
            $('body').addClass('geomart-search-active');
            searchResults.show();
            searchClose.show();
            performSearch($(this).val().trim(), $(this).data('widget-id'));
        }
    });

    searchInput.on('input', function() {
        const searchTerm = $(this).val().trim();
        const widgetId = $(this).data('widget-id');
        performSearch(searchTerm, widgetId);
        
        if (searchTerm.length > 0 || isMobile) {
            searchClose.show();
        } else if (!isMobile) {
            searchClose.hide();
        }
    });

    searchClose.on('click', function() {
        searchInput.val('');
        searchClose.hide();
        
        if (isMobile) {
            $('body').removeClass('geomart-search-active');
            searchResults.hide();
            searchInput.blur();
        } else {
            searchResults.hide();
        }
    });

    // Close search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.geomart-search-input-wrapper, .geomart-search-results').length) {
            if (!isMobile) {
                searchResults.hide();
            }
        }
    });
});
