jQuery(function($) {
    class GeoMartSearch {
        constructor(widget) {
            this.widget = widget;
            this.input = widget.find('.geomart-search-input');
            this.results = widget.find('.geomart-search-results');
            this.loading = widget.find('.geomart-search-loading');
            this.minChars = 2;
            this.isMobile = window.innerWidth <= 767;
            this.searchTimeout = null;

            this.initializeHandlers();
        }

        initializeHandlers() {
            this.setupEventListeners();
            this.handleResize();
        }

        setupEventListeners() {
            // Input handler
            this.input.on('input', () => {
                clearTimeout(this.searchTimeout);
                const searchTerm = this.input.val().trim();

                // Show results container on mobile immediately
                if (this.isMobile && searchTerm.length === 1) {
                    $('body').addClass('geomart-search-active');
                    this.results.show();
                }

                if (searchTerm.length >= this.minChars) {
                    this.loading.show();
                    this.searchTimeout = setTimeout(() => this.performSearch(searchTerm), 300);
                } else {
                    this.results.empty();
                    if (!this.isMobile) {
                        this.results.hide();
                    }
                    this.loading.hide();
                }
            });

            // Focus handler
            this.input.on('focus', () => {
                if (this.isMobile) {
                    $('body').addClass('geomart-search-active');
                    this.results.show();
                }
            });

            // Close button handler
            this.widget.find('.geomart-search-close').on('click', () => {
                this.hideResults();
                this.input.val('');
                this.input.blur();
            });

            // Click outside handler
            $(document).on('click', (e) => {
                if (!this.widget.is(e.target) && this.widget.has(e.target).length === 0) {
                    this.hideResults();
                }
            });
        }

        performSearch(searchTerm) {
            console.log('Performing search for:', searchTerm);
            console.log('AJAX URL:', geomart_search.ajax_url);

            $.ajax({
                url: geomart_search.ajax_url,
                type: 'POST',
                data: {
                    action: 'geomart_search_products',
                    search_term: searchTerm,
                    nonce: geomart_search.nonce
                },
                beforeSend: () => {
                    console.log('Sending search request...');
                    this.results.html('<div class="geomart-no-results">მიმდინარეობს ძიება...</div>');
                    this.results.show();
                },
                success: (response) => {
                    console.log('Search response:', response);
                    this.loading.hide();
                    if (response.success && response.data.products && response.data.products.length > 0) {
                        const resultsHtml = `
                            <div class="geomart-search-results-inner">
                                ${response.data.products.map(product => this.getProductHtml(product)).join('')}
                            </div>
                            <a href="${response.data.shop_search_url}" class="geomart-view-all">
                                <span>მეტის ნახვა</span>
                                <span class="geomart-view-all-count">${response.data.total_count}</span>
                            </a>
                        `;
                        this.results.html(resultsHtml);
                    } else {
                        this.results.html('<div class="geomart-no-results">პროდუქტი ვერ მოიძებნა</div>');
                    }
                },
                error: (xhr, status, error) => {
                    console.error('Search error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    this.loading.hide();
                    this.results.html('<div class="geomart-no-results">დაფიქსირდა შეცდომა</div>');
                }
            });
        }

        getProductHtml(product) {
            return `
                <a href="${product.permalink}" class="geomart-search-item">
                    <div class="geomart-search-item-image">
                        <img src="${product.image}" alt="${product.title}">
                    </div>
                    <div class="geomart-search-item-content">
                        <h4 class="geomart-search-item-title">${product.title}</h4>
                        <div class="geomart-search-item-sku">SKU: ${product.sku}</div>
                        <div class="geomart-search-item-meta">
                            <div class="geomart-search-item-price">${product.price}</div>
                            <div class="stock-status ${product.stock_status}">${product.stock_text}</div>
                        </div>
                    </div>
                </a>
            `;
        }

        hideResults() {
            this.results.empty();
            this.results.hide();
            $('body').removeClass('geomart-search-active');
        }

        handleResize() {
            $(window).on('resize', () => {
                this.isMobile = window.innerWidth <= 767;
            });
        }
    }

    // Initialize search
    $('.geomart-search-widget').each(function() {
        new GeoMartSearch($(this));
    });
});
