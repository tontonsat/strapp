var getComments = () => {
    $.ajax({
        url: '/ajaxComment',
        type: 'GET',
        success: (data) => {
            $('.comments-container').html(data)
        }
    })
}
$(document).ready(getComments())