(function($) {
    $.fn.mnmwp_menu_creater = function(options) {
        var mnmwp_main_menu = $(this),
            settings = $.extend({
                title: 'Menu',
                icon: '<button class="btn menu-btn" id="toggle"><span class="one"></span><span class="two"></span><span class="three"></span></button>',
                format: 'dropdown',
                sticky: false
            }, options);

        return this.each(function() {
            mnmwp_main_menu.prepend('<div id="mnm-menu-button">' + settings.icon + '</div>');
            $(this).find("#mnm-menu-button").on('click', function() {
                $(this).toggleClass('mnm-menu-opened');
                var mainmenu = $(this).next('ul');
                if (mainmenu.hasClass('open')) {
                    mainmenu.hide().removeClass('open');
                } else {
                    mainmenu.show().addClass('open');
                    if (settings.format === "dropdown") {
                        mainmenu.find('ul').show();
                    }
                }
            });

            mnmwp_main_menu.find('li ul').parent().addClass('has-sub');

            multiTg = function() {
                mnmwp_main_menu.find(".has-sub").prepend('<span class="mnm-submenu-button"></span>');
                mnmwp_main_menu.find('.mnm-submenu-button').on('click', function() {
                    $(this).toggleClass('mnm-submenu-opened');
                    if ($(this).siblings('ul').hasClass('open')) {
                        $(this).siblings('ul').removeClass('open').hide();
                    } else {
                        $(this).siblings('ul').addClass('open').show();
                    }
                });
            };

            if (settings.format === 'multitoggle') multiTg();
            else mnmwp_main_menu.addClass('dropdown');

            if (settings.sticky === true) mnmwp_main_menu.css('position', 'fixed');

            resizeFix = function() {
                setTimeout( function(){ 
                    if ($("html").hasClass("is_mobile")) {
                        mnmwp_main_menu.find('ul').hide().removeClass('open');
                        mnmwp_main_menu.find('#mnm-menu-button').removeClass('mnm-menu-opened');
                        mnmwp_main_menu.find('.mnm-submenu-button').removeClass('mnm-submenu-opened');
                    } else{
                        mnmwp_main_menu.find('ul').show();
                    }
                }  , 1);
            };
            resizeFix();
            return $(window).on('resize', resizeFix);
        });
    };
})(jQuery);

(function($) {
    $(document).ready(function() {
        $("#mnmwp-main-menu").mnmwp_menu_creater({
            title: "",
            format: "multitoggle"
        });
    });
})(jQuery);