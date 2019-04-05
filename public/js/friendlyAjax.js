$(".btn-add-friend-profile").click(function () {
    $.ajax({
        url: '/addFriend/' + $(".btn-add-friend-profile").data('target'),
        type: 'GET',
        success: function (result) {
            $(".add-friend-container").html(result);
        }
    });
});

$(document).on('click', '.btn-friendship', function () {
    $.ajax({
        url: '/treatFriendRequest/' + $(".btn-friendship").data('target') + '/' + $(".btn-friendship").data('slug'),
        type: 'GET',
        success: function (result) {
            $(".main-flash-container").html(result)
            updateFrienships()
        }
    });
});

var updateFrienships = () => {
    $.ajax({
        url: '/ajaxListUser/' + $(".ajax-filter").data('path'),
        type: 'GET',
        success: function (result) {
            $(".ajax-user-list").html(result)
        }
    });
}