$(document).ready(function() {
    $(window).resize(function () {
        if ($(window).width() > 767) {
            $(".sidebar-container > .sidebar").css("display", "block");
        }
    });
});
