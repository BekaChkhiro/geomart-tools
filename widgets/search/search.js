jQuery(document).ready(function($) {
    let searchTimeout;
    const searchWidgets = $('.geomart-search-widget');

    searchWidgets.each(function() {
        const widget = $(this);
        const input = widget.find('.geomart-search-input');
        const results = widget.find('.geomart-search-results');
        const resultsInner = results.find('.geomart-search-results-inner');
        const loading = results.find('.geomart-search-loading');
        const noResults = results.find('.geomart-no-results');
        const itemTemplate = $('#geomart-search-item-template').html();
        const viewAllTemplate = $('#geomart-view-all-template').html();
        const minChars = parseInt(widget.data('min-chars')) || 3;

        input.on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val().trim();

            if (searchTerm.length < minChars) {
                results.hide();
                resultsInner.empty();
                loading.hide();
                noResults.hide();
                return;
            }

            loading.show();
            results.hide();
            resultsInner.empty();
            noResults.hide();

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: geomartSearch.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'geomart_search_products',
                        search_term: searchTerm,
                        nonce: geomartSearch.nonce
                    },
                    success: function(response) {
                        loading.hide();
                        if (response.success && response.data.products && response.data.products.length > 0) {
                            resultsInner.empty();
                            
                            // Add products
                            response.data.products.forEach(function(product) {
                                let itemHtml = itemTemplate
                                    .replace('{permalink}', product.permalink)
                                    .replace('{image}', product.image || '')
                                    .replace('{title}', product.title)
                                    .replace('{price}', product.price)
                                    .replace('{stock_status}', product.in_stock ? 'in-stock' : 'out-of-stock')
                                    .replace('{stock_text}', product.stock_text);
                                
                                resultsInner.append(itemHtml);
                            });

                            // Add view all button if there are more results
                            if (response.data.total_count > response.data.products.length) {
                                let viewAllHtml = viewAllTemplate
                                    .replace('{search_url}', response.data.search_url)
                                    .replace('{total_count}', response.data.total_count);
                                
                                resultsInner.append(viewAllHtml);
                            }

                            results.show();
                            $('body').addClass('geomart-search-active');
                        } else {
                            noResults.show();
                        }
                    },
                    error: function() {
                        loading.hide();
                        resultsInner.empty();
                        noResults.show().text('დაფიქსირდა შეცდომა');
                    }
                });
            }, 300);
        });

        // Close results when clicking outside
        $(document).on('click', function(e) {
            if (!widget.is(e.target) && widget.has(e.target).length === 0) {
                results.hide();
                $('body').removeClass('geomart-search-active');
            }
        });

        // Show results when input is focused
        input.on('focus', function() {
            if (resultsInner.children().length > 0) {
                results.show();
                $('body').addClass('geomart-search-active');
            }
        });
    });
});
