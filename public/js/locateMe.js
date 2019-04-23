var getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setValue);
    } else {
        x.innerHTML = "Geolocation is not supported by this browser."
    }
}

var setValue = (position) => {
    $('.updateCoordBtn').data('pos', position.coords.longitude + ',' + position.coords.latitude)
    $('#registration_coord').val(position.coords.longitude + ',' + position.coords.latitude)
    $('#vote_coord').val(position.coords.longitude + ',' + position.coords.latitude)

}

$(document).ready(getLocation())

$(".updateCoordBtn").click(function () {
    $.ajax({
        url: '/updateCoord/' + $(".updateCoordBtn").data('pos'),
        type: 'GET',
        success: function (result) {
            $(".main-flash-container").html(result)
        }
    });
});