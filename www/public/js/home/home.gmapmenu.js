var js_home = (function() {

init();

return {
    // empty
};

function init()
{
    $(window).load(loadmap);
}

function loadmap()
{
    var myOptions = {
        center: new google.maps.LatLng(-34.397, 150.644),
        zoom: 8,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var map_canvas = $('#map_canvas');
    var map = new google.maps.Map(map_canvas, myOptions);

    console.log(myOptions);
    console.log(map_canvas);
    console.log(map);
}

})();
