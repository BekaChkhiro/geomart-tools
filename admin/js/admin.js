jQuery(document).ready(function($) {
    // Save active tab to session storage
    $('.geomart-tab').on('click', function() {
        sessionStorage.setItem('geomartActiveTab', $(this).attr('href'));
    });

    // Restore active tab from session storage
    var activeTab = sessionStorage.getItem('geomartActiveTab');
    if (activeTab) {
        $('.geomart-tab[href="' + activeTab + '"]').addClass('active');
    }
});
