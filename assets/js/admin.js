jQuery(document).ready(function($) {
    $('.site-vital-summary-box').each(function() {
        var $box = $(this);
        var category = $box.data('category');

        // Show spinner or loading text is already there as placeholder

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'site_vitals_get_category_data',
                nonce: siteVitals.nonce, // You'll need to localize a nonce in PHP
                category: category
            },
            success: function(response) {
                if (response.success) {
                    // Remove the loading text
                    $box.find('.sv-loading').remove();

                    // Insert the actual counts
                    var countsHtml = '';
                    countsHtml += '<span class="sv-good-count">Good: ' + response.data.good + '</span>';
                    countsHtml += '<span class="sv-warning-count">Needs Attention: ' + response.data.warning + '</span>';
                    countsHtml += '<span class="sv-danger-count">Needs Improvement: ' + response.data.danger + '</span>';

                    $box.find('.site-vital-status-counts').html(countsHtml);
                } else {
                    $box.find('.sv-loading').text('Error loading data.');
                }
            },
            error: function() {
                $box.find('.sv-loading').text('Error loading data.');
            }
        });
    });
});
