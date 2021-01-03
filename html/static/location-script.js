function showPosition(position) {
	var latitude;
	var longitude;
  	latitude = position.coords.latitude;
  	longitude = position.coords.longitude;

	// initialize the map on the "map" div with a given center and zoom
	var map = L.map('mapid', {
	    center: [latitude, longitude],
	    zoom: 18
	});

	var tile_layer = L.tileLayer(
	        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
	        {
	        "attribution": null,
	        "detectRetina": false,
	        "maxNativeZoom": 18,
	        "maxZoom": 18,
	        "minZoom": 0,
	        "noWrap": false,
	        "opacity": 1,
	        "subdomains": "abc",
	        "tms": false
	}).addTo(map);

	var marker = L.marker([latitude, longitude]).addTo(map);
}


if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(showPosition);
} else { 
    console.log("Geolocation is not supported by this browser.");
}
