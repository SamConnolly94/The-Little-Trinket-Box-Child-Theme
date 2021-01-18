jQuery(function ($) { 
    $(document).ready(function() {
        AdjustBannerTo4Elements();
        ResizeLatestProductsContainers($);
        // DisableGreyOverlay()
    });

    function DisableGreyOverlay() {
        // Remove overlay from slides
        var slides = document.querySelector('.slides').childNodes;
        for (var i = 0; i < slides.length; i++) {
            var ele = slides[i];
            ele = $(ele);
            try {
                ele.removeClass('bg-dark');
                ele.removeClass('bg-dark-30');
            }
            catch (e) {
                // Don't really care, class probably wasn't attached
            }
            ele.addClass('bg-light');
        }
    }

    /**
     * Currently the banner takes 3 elements to fill the width of the screen, this function will adjust the bootstrap classes applied to banner containers to take 4 to fill the width.
     */
    function AdjustBannerTo4Elements() {
        var banner = document.querySelectorAll('.shop_isle_bannerss_section');
        for (var i = 0; i < banner.length; i++) {
            var bannerItems = banner[i].querySelectorAll(':scope > .col-sm-4');
            for (var j = 0; j < bannerItems.length; j++) {
                $(bannerItems[j]).removeClass('col-sm-4');
                $(bannerItems[j]).addClass('col-sm-3');
            }
        }
    }
    
    /**
     * The latest products category can often look too large with resized banner, adjust the bootstrap classes here to make them smaller.
     */
    function ResizeLatestProductsContainers() {
        var latest = document.querySelector('#latest');
        var latestContainer = latest.querySelector('.container');
        var rows = latestContainer.querySelector(':scope > .multi-columns-row');
        var divs = rows.querySelectorAll(':scope > div');
        for (var i = 0; i < divs.length; i++) {
            $(divs[i]).removeClass('col-sm-6');
            $(divs[i]).addClass('col-sm-3');
            // $(divs[i]).removeClass('col-md-3');
            // $(divs[i]).addClass('col-md-2');
            $(divs[i]).removeClass('col-lg-3');
            $(divs[i]).addClass('col-lg-2');
        }
    }
});