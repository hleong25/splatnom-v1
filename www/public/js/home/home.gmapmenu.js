var js_home = (function() {

init();

return {
    // empty
};

var $map;
var $bounds;
var $infowindow;

function init()
{
    $.template('tmpl_places', $('script#tmpl_places'));

    var centerlatlng = new google.maps.LatLng(33.735911, -117.787177);
    var myOptions = {
        center: centerlatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map_canvas = document.getElementById('map_canvas');

    $map = new google.maps.Map(map_canvas, myOptions);
    $bounds = new google.maps.LatLngBounds();
    $infowindow = new google.maps.InfoWindow();

    for (var idx in $menus_tmpl_data)
    {
        var $objLatLng = $menus_tmpl_data[idx];
        addMarker($objLatLng);
    }

    $map.fitBounds($bounds);
}

function addMarker(places)
{
    if (!places.length)
    {
        return;
    }

    var lat = places[0]['lat'];
    var lng = places[0]['lng'];
    var latlng = new google.maps.LatLng(lat, lng);

    var marker = new google.maps.Marker({
        map: $map,
        position: latlng
    });

    $bounds.extend(latlng);

    google.maps.event.addListener(marker, 'click', function() {

        var info = '';

        for (var idx in places)
        {
            info += $.tmpl('tmpl_places', places[idx]).outerHTML();
        }

        $infowindow.setContent(info);
        $infowindow.open($map, marker);
    });
}

})();
