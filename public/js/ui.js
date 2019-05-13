/* init tooltips for bootstrap */
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

/* general behaviours */
$(document).ready(function () {

    var submit_focus = false
    $('#mood_mood').focus(function () {
        $('#mood_save').show().addClass('visible')
    }).blur(function () {
        if (submit_focus) {
            submit_focus = true

        } else {
            $('#mood_save').hide().removeClass('visible')
        }
    })

    $('#mood_save').mousedown(function () {
        submit_focus = true
    })
})

$('.home-vote-container').on('click', (e) => {
    var path = $(e.currentTarget).data('path')
    window.location = path
})

/* pagination */
/* trigger is used to block ajax request when no match is found */
var trigger = 1

var paginate = () => {
    let offset = $('.list-bottom-ajax').data('offset')
    let path = $('.list-bottom-ajax').data('path')
    $('.list-bottom-ajax').html('<i class="fas fa-compass fa-spin compass-listuser"></i>')
    $.ajax({
        url: path + '/' + offset,
        type: 'GET',
        success: (data) => {
            $('.list-bottom-ajax').html('')
            $('.list-bottom-ajax').data('offset', offset + 48)
            if (data !== '' && data !== null) {
                $('.user-list-row').append(data)
                trigger = 1

            } else {
                trigger = 0
                $('.user-list-row').append('<div class="end-result">No more results</div>')
            }
        }
    })
}
$(window).scroll(function () {
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
        if (trigger === 1) {
            paginate()
        }
    }
})

var getTimer = (obj) => {
    let rawDate = $(obj).data('dateend')
    let now = new Date().getTime()
    var countDownDate = new Date(rawDate)

    var duration = countDownDate - now

    var hours = Math.floor((duration % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((duration % (1000 * 60 * 60)) / (1000 * 60));

    if (duration > 0) {
        data = hours + ':' + minutes
    } else {
        data = 'over'
        $(obj).prev('.vote-card-img').removeClass('vote-card-img-active')
        $(obj).prev('.vote-card-img').addClass('vote-card-img-depleted')
    }
    $(obj).html(data)
}

$(document).ready(() => {
    $('.vote-timer-spoiler').each((index, elem) => {
        getTimer(elem)
    })
    setInterval(() => {
        $('.vote-timer-spoiler').each((index, elem) => {
            getTimer(elem)
        })
    }, 30000)
})