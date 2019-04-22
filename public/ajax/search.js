var searchType = () => {
    let query = $('.search-bar').val()
    $.ajax({
        url: '/ajaxSearch/' + query,
        type: 'GET',
        success: (data) => {
            $('.search-result-container').html(data)
        }
    })
}

/* here the use of setTimeout and clearTimeout is essential to have a throttle for ajax requests */
var searchBar = $('.search-bar')
var typingTimer
var doneTypingInterval = 800

searchBar.on('click paste keyup', () => {
    clearTimeout(typingTimer)
    if (searchBar.val().length >= 3) {
        $('.search-bar-icon').css({
            'border-bottom-right-radius': '0',
            'border-bottom-left-radius': '0',
            'border-top-left-radius': '0'
        })
        $('.search-bar').css({
            'border-bottom-left-radius': '0',
        })
        $('.search-result-container').html('<i class="fas fa-compass fa-spin"></i>') 
        typingTimer = setTimeout(searchType, doneTypingInterval)
    }
})

$(searchBar).on("change paste keyup", () => {   
    if (searchBar.val().length <= 2) {
        $('.search-result-container').html('')
        $('.search-bar-icon').css({
            'border-bottom-right-radius': '.25rem',
        })
        $('.search-bar').css({
            'border-bottom-left-radius': '.25rem',
        })
    }
})
$(window).click(function() {
    $('.search-result-container').html('')
});

