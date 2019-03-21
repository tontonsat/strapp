/* init tooltips for bootstrap */
$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

/* general behaviours */
$(document).ready(function() {

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