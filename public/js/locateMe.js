//var x = document.getElementById("demo")

var getLocation = () => {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(setValue);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser."
    }
}

var setValue = (position) => {
    $('#form_coord').val(position.coords.longitude+','+position.coords.latitude)
    $('#registration_coord').val(position.coords.longitude+','+position.coords.latitude)

}

$(document).ready(getLocation())