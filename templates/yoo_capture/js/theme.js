/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function($) {

	var config = $('html').data('config') || {};

	// Social buttons
	$('article[data-permalink]').socialButtons(config);

    // parallax effect on elements with class .parallax
    $(".parallax").parallax();

    // Calculate slants
    $(window).on('resize', (function(){

        var fn = function(){

            $('.tm-headerbar.tm-slant-bottom > .tm-slant-block-bottom:before, .tm-slant-top + .tm-block > .tm-slant-block-top, .tm-slant-bottom > .tm-slant-block-bottom').each(function(){

                var elem        = $(this),
                    slantWidth  = elem.parent().outerWidth(),
                    slantHeight = slantWidth / 100 * 2.5,
                    css         = {'border-right-width': slantWidth, 'border-top-width': slantHeight, 'top': -slantHeight+1};

                if(elem.hasClass("tm-slant-block-bottom")) {
                    css.bottom = css.top;
                    css.top    = "";
                }

                elem.css(css);

            });
        };

        fn();

        return fn;

    })());

});
