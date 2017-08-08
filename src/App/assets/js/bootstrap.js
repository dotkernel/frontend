// Load jQuery and Bootstrap
try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {}

$(document).ready(function() {
    // sidebar menu slowly slide on mobile
    $(window).resize(function () {
        if ($(window).width() > 767) {
            $(".sidebar-container > .sidebar").css("display", "block");
        }
    });
    $(".sidebar-menu-header").on("click", function () {
        $(".sidebar-container > .sidebar").slideToggle("slow", function () {
            // Animation complete.
        });
    });

    // accordion toggle class on open/close
    $('.panel-title .collapsed').on("click", function () {
        var e = $('.panel-title .closed').removeClass("closed").addClass("open");
        $(this).toggleClass("open closed");
        if ($(this).attr("aria-expanded") === 'true')
        {
            e.removeClass("closed").addClass("open");
        }
    });
});
