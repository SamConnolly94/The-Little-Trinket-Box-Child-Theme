jQuery(function ($) { 
    $(document).ready(function() {
        var transparent = true;

        if ($( '.navbar-color-on-scroll' ).length !== 0) {
            var navbarHome       = $( '.navbar-color-on-scroll' ),
                headerWithTopbar = 0;

            if ( navbarHome.hasClass( 'header-with-topbar' ) ) {
                headerWithTopbar = 40;
            }

            $( window ).on('scroll', function () {
                        var customNavbarStylingEles = [
                            $(document.querySelector('.site-title a')), 
                            $(document.querySelector('.site-description a')),
                            $(document.querySelector('.header-search-button')),
                            $(document.querySelector('.icon-basket'))

                        ];
                        
                        // Find all the menu items, add them to our list of things to apply a custom style to
                        var menuItems = document.querySelectorAll('.menu-item');
                        for (var menuItemCount = 0; menuItemCount < menuItems.length; menuItemCount++) {
                            var menuItemLinks = menuItems[menuItemCount].querySelector(':scope > a');
                            customNavbarStylingEles.push($(menuItemLinks));

                            var dropdown = menuItems[menuItemCount].querySelector(':scope > .dropdownmenu');
                            if (dropdown !== null && dropdown !== undefined) {
                                customNavbarStylingEles.push($(dropdown));
                            }
                        }

                        if ($( document ).scrollTop() > headerWithTopbar) {
                            if (transparent) {
                                transparent = false;
                                for (var i = 0; i < customNavbarStylingEles.length; i++) {
                                    customNavbarStylingEles[i].addClass( 'non-transparent-navbar-custom-properties' );
                                }
                            }
                        } else {
                            if ( ! transparent) {
                                transparent = true;
                                for (var i = 0; i < customNavbarStylingEles.length; i++) {
                                    customNavbarStylingEles[i].removeClass( 'non-transparent-navbar-custom-properties' );
                                }
                            }
                        }
                    });
            }
    });
});