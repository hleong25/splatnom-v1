var js_home = (function() {

init();

return {
    // empty
};

var $map;

function init()
{
    var centerlatlng = new google.maps.LatLng(33.735911, -117.787177);
    var myOptions = {
        center: centerlatlng,
        zoom: 10,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map_canvas = $('#map_canvas')[0]; // must NOT be a jquery obj

    $map = new google.maps.Map(map_canvas, myOptions);

    var infowindow = new google.maps.InfoWindow();
    var bounds = new google.maps.LatLngBounds();

    for (var idx in $menus)
    {
        var menu = $menus[idx];

        var lat = menu['latitude'];
        var lng = menu['longitude'];
        var latlng = new google.maps.LatLng(lat, lng);

        var marker = new google.maps.Marker({
            map: $map,
            position: latlng
        });

        bounds.extend(latlng);

        google.maps.event.addListener(marker, 'click', (function(marker, menu) {
            return function (){
                var name = menu['name'];
                var addy = menu['address'];
                var link = menu['link'];

                var info = '<div>'+link+'<br/><span>'+addy+'</span></div>';
                infowindow.setContent(info);
                infowindow.open($map, marker);
            }
        })(marker, menu));
    }

    $map.fitBounds(bounds);
}

})();
