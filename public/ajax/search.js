var searchType = () => {
    let query = $('.search-bar').val()
    $.ajax({
        url: '/ajaxSearch/' + query,
        type: 'GET',
        success: (data) => {
            $('.search-result-container').html(data)
            console.log('search');
        }
    })
}

/* here the use of setTimeout and clearTimeout is essential to have a throttle for ajax requests */
var searchBar = $('.search-bar')
var typingTimer
var doneTypingInterval = 800

searchBar.on('paste keyup', () => {
    clearTimeout(typingTimer)
    if (searchBar.val().length >= 3) {
        $('.search-result-container').html('<i class="fas fa-compass fa-spin"></i>') 
        typingTimer = setTimeout(searchType, doneTypingInterval)
    }
})

$(searchBar).on("change paste keyup", () => {   
    if (searchBar.val().length <= 2) {
        $('.search-result-container').html('')
    }
})
$(window).click(function() {
    $('.search-result-container').html('')
});

