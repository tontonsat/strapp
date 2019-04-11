$(document).on('click', '.nav-notif-title', function (e) {
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