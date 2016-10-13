(function($) {

    $.fn.navBar = function () {
        var
            navbarBrand    = jQuery('.navbar-brand', this),
            navbarSandwich = jQuery('.navbar-sandwich', this),
            navbarMenu     = jQuery('.navbar-menu', this),
            menuWidthCompensation = 30, // Try change this if the navbar is collapsing too early or to later.
            navbarWidth    = navbarBrand.width() + navbarMenu.width() + menuWidthCompensation,
            navbarCollapse = function () {
                if (jQuery(window).width() < navbarWidth) {
                    // Get the navbar items and put them into the sandwich menu.
                    navbarMenu
                        .find('.navbar-collapsable-item')
                        .appendTo(navbarSandwich.find('.navbar-sandwich-content'));
                    navbarSandwich.show();
                } else {
                    // Give the items back to the navbar.
                    navbarSandwich
                        .hide()
                        .find('.navbar-collapsable-item ')
                        .prependTo(navbarMenu);
                    navbarMenu.show();
                }
            }
        ;
        // Check to collapse on page load.
        navbarCollapse();
        // ...or when window resize.
        jQuery(window).on('resize', function(){
            navbarCollapse();
        });
    };

}(jQuery));