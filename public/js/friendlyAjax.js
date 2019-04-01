$(".btn-add-friend-profile").click(function () {
    $.ajax({
        url: '/addFriend/' + $(".btn-add-friend-profile").data('target'),
        type: 'GET',
        success: function (result) {
            $(".add-friend-container").html(result);
        }
    });
});