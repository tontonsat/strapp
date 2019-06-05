/**
 * call ajaxAddfriend
 * returns twig template
 */
$(".btn-add-friend-profile").click(function () {
    $.ajax({
        url: '/addFriend/' + $(".btn-add-friend-profile").data('target'),
        type: 'GET',
        success: function (result) {
            $(".add-friend-container").html(result);
        }
    });
});
/* list user behavior */
$(document).on('click', '.btn-friendship', function () {
    $.ajax({
        url: '/treatFriendRequest/' + $(this).data('target') + '/' + $(this).data('slug'),
        type: 'GET',
        success: function (result) {
            $(".main-flash-container").html(result)
            updateFrienships()
        }
    });
});
/* profile behavior */
$(document).on('click', '.btn-friendship-profile', function () {
    $.ajax({
        url: '/treatFriendRequest/' + $(this).data('target') + '/' + $(this).data('slug'),
        type: 'GET',
        success: function (result) {
            $(".btn-status-friendship-container-profile").html(result)
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