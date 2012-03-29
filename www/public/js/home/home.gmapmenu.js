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
    var centerlatlng = new google.maps.LatLng(33.735911, -117.787177);
    var myOptions = {
        center: centerlatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map_canvas = document.getElementById('map_canvas');

    $map = new google.maps.Map(map_canvas, myOptions);
    $bounds = new google.maps.LatLngBounds();
    $infowindow = new google.maps.InfoWindow();

    for (var idx in $menus)
    {
        var menu = $menus[idx];

        addMarker(menu);
    }

    $map.fitBounds($bounds);
}

function addMarker(menu)
{
    var lat = menu['latitude'];
    var lng = menu['longitude'];
    var latlng = new google.maps.LatLng(lat, lng);

    var marker = new google.maps.Marker({
        map: $map,
        position: latlng
    });

    $bounds.extend(latlng);

    google.maps.event.addListener(marker, 'click', function() {
        var name = menu['name'];
        var addy = menu['address'];
        var link = menu['link'];

        var info = '<div>'+link+'<br/><span>'+addy+'</span></div>';

        $infowindow.setContent(info);
        $infowindow.open($map, marker);
    });
}

})();
