$(document).ready(function () {
    $('.panel-title .collapsed').on("click", function () {
        var e = $('.panel-title .closed').removeClass("closed").addClass("open");
        $(this).toggleClass("open closed");
        if ($(this).attr("aria-expanded") === 'true')
        {
            e.removeClass("closed").addClass("open");
        }
    });
});

