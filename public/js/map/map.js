mapboxgl.accessToken = 'pk.eyJ1IjoidG9udG9uc2F0IiwiYSI6ImNqc25jNTIwNjA5bDc0M280dGt4ejJtNXkifQ.h_Ox7WHHtfhpQK9Qr0oTlw';
var map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    continuousWorld: false,
    noWrap: true,
    center: [-73.98, 40.73],
    zoom: 13,
    maxBounds: [[-180, -85], [180, 85]]
});
map.addControl(new mapboxgl.GeolocateControl({
    positionOptions: {
        enableHighAccuracy: true
    },
    trackUserLocation: true
}));

var marker = new mapboxgl.Marker()
  .setLngLat([30.5, 50.5])
  .addTo(map);
var marker = new mapboxgl.Marker()
  .setLngLat([0, 0])
  .addTo(map);

/* map.on('mousemove', function (e) {
  document.getElementById('info').innerHTML =
  // e.point is the x, y coordinates of the mousemove event relative
  // to the top-left corner of the map
  JSON.stringify(e.point) + '<br />' +
  // e.lngLat is the longitude, latitude geographical position of the event
  JSON.stringify(e.lngLat);
}); */
map.on('click', (e) => {
  var marker = new mapboxgl.Marker()
  .setLngLat(e.lngLat)
  .addTo(map);
  console.log(JSON.stringify(e.lngLat));
  console.log(JSON.stringify(e.point));
  
})