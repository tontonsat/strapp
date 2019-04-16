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

var searchBar = $('.search-bar')

$(searchBar).on("focus paste keyup", () => {   
    if (searchBar.val().length >= 3) {
        $('.search-result-container').html('<i class="fas fa-compass fa-spin"></i>') 
        searchType()
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