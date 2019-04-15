$(document).on('click', '.nav-notif-title', function (e) {
    e.stopPropagation()
    getNotifs()
});
$(document).on('click', '.notif-data-clear', function (e) {
    e.stopPropagation()
    getNotifs()
});

var clearNotifs = () => {
    $.ajax({
        url: $('.notif-data-clear').data('clear'),
        type: 'GET',
        success: () => {
            getCounter()
        }
    });

}
$(function ($) {
    $('.nav-notif-show').bind('scroll', function () {
        var offset = $(this).data('offset')

        if ($(this).scrollTop() + $(this).outerHeight() >= $(this)[0].scrollHeight) {
            $.ajax({
                url: '/ajaxListNotifScroll/' + offset,
                type: 'GET',
                success: (result) => {
                    $(this).append(result)
                    $(this).data('offset', offset + 10)
                }
            });
        }
    })
});

/* ajax refresh notif counter & bell */
var getCounter = () => {
    $.ajax({
        url: '/ajaxGetCounter',
        type: 'GET',
        success: (result) => {
            $(".nav-notif-title").html(result)
        }
    });
}

/* ajax refresh notifs */
var getNotifs = () => {
    $.ajax({
        url: '/ajaxListNotif',
        type: 'GET',
        success: (result) => {
            $(".nav-notif-show").html(result)
            clearNotifs()
        }
    });
}
setInterval(getCounter, 20000)