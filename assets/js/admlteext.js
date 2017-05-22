$(function () {
    "use strict";
    /*
     * Add collapse and remove events to boxes And save cookies
     */
    $("[data-widget='collapse']").off("click").each(function () {
        var box = $(this).parents(".box").first();
        //console.log('found '+box.attr('id') + ' cookstate = ' + $.cookie(box.attr('id')+'-state') );
        if ($.cookie(box.attr('id') + '-state') == "hide") {
            if (!box.hasClass("collapsed-box")) {
                box.addClass("collapsed-box");
            }
            $(this).find('i').removeClass('fa-minus').addClass('fa-plus');
        } else {
            if (box.hasClass("collapsed-box")) {
                box.removeClass("collapsed-box");
            }
            $(this).find('i').removeClass('fa-plus').addClass('fa-minus');
        }
    });
    $("[data-widget='collapse']").on('click', function () {
        var box = $(this).parents(".box").first();
        if (!box.hasClass("collapsed-box")) {
            $.cookie(box.attr('id') + '-state', "hide");
        } else {
            $.cookie(box.attr('id') + '-state', "show");
        }
        //console.log('clicked '+box.attr('id') + ' cookstate = ' + $.cookie(box.attr('id')+'-state') );
    });
});