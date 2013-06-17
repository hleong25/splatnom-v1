var js_splatnom = (function() {

init();

return {
    // empty
};

var $new_vendor_cnt;

function init()
{
    $new_vendor_cnt = 0;

    $('button').on('click', function(e){
        e.preventDefault();
    });

    $('.find_latlong')
        .button()
        .on('click', getLatLongFromAddress)
    ;

    $('.map_addy')
        .button()
        .on('click', showGoogleMapAddy)
    ;

    $.template('tmpl_vendor', $('script#tmpl_vendor'));

    setup_vendor_info.call(document);

    tinyMCE.init({
        selector: 'textarea.vendor_desc',
        plugins: 'code,preview,visualblocks',
        toolbars: 'preview',

        // custom CSS
        content_css: '/css/event/view.css',
        style_formats: [
            //{title: 'Food Info', block: 'p', classes: 'food_info'},
            {title: 'Food Name', inline: 'span', classes: 'food_name'},
            {title: 'Food Description', inline: 'span', classes: 'food_desc'}
        ]

    });
}

function getLatLongFromAddress()
{
    var $this = $(this);

    var txtAddress = $('textarea[name="info_address"]').val();
    txtAddress = txtAddress.replace('\n', ',');

    var params = {
        'inFormat' : 'kvp',
        'outFormat' : 'json',
        'ignoreLatLngInput' : 'true',
        'thumbMaps' : 'false',
        'location' : txtAddress,
    };

    var url_params = $.param(params);

    var url = 'http://open.mapquestapi.com/geocoding/v1/address?callback=?&'+url_params;

    $this.attr('disabled','disabled');

    var latlong_dbg = $('.latlong_js_msg');
    latlong_dbg.val('Calling mapquest api...');

    $.getJSON(url, function (data) {
            if (data.info.statuscode != 0)
            {
                latlong_dbg.val('Failed. Status code is '+data.info.statuscode);
                return;
            }

            if (data.results.length < 1)
            {
                latlong_dbg.val('Failed. Results length is '+data.results.length);
                return;
            }

            var locations = data.results[0].locations;

            if (locations.length < 1)
            {
                latlong_dbg.val('No location found.');
                return;
            }

            for (var ii = 0, jj = locations.length; ii < jj; ii++)
            {
                var loc = locations[ii];

                if ((loc.adminArea1 === 'US') ||
                    (loc.adminArea1 === 'United States of America'))
                {
                    var latlng = loc.latLng;

                    $('.info_latitude').val(latlng.lat);
                    $('.info_longitude').val(latlng.lng);

                    latlong_dbg.val('Found ('+latlng.lat+','+latlng.lng+')');
                    return;
                }
            }

            latlong_dbg.val('No locations are valid.');

        })
    .complete(function (){
        $this.removeAttr('disabled');
    });
}

function showGoogleMapAddy()
{
    var $this = $(this);

    var lat = $('.info_latitude').val();
    var lng = $('.info_longitude').val();

    var txtAddress = $('textarea[name="info_address"]').val();
    txtAddress = txtAddress.replace('\n', ',');

    var params = $.param({
        'key': $this.data('google-api-key'),
        'sensor': 'false',
        'maptype': 'roadmap',
        'size': '800x800'
    });

    var delim = '%7C';
    params += '&markers=color:blue' + delim + 'label:L' + delim + lat + ',' + lng;
    params += '&markers=color:green' + delim + 'label:A' + delim + txtAddress;

    var url = 'http://maps.google.com/maps/api/staticmap?'+params;
    window.open(url, '_blank');
}

function setup_vendor_info()
{
    $(this)
        .find('.vendor_add')
            .button({
                text: false,
                icons: {primary: 'ui-icon-plusthick'},
            })
            .on('click.vendor_add', vendor_add)
            .end()

        .find('.vendor_delete')
            .button({
                text: false,
                icons: {primary: 'ui-icon-closethick'},
            })
            .on('click.vendor_delete', vendor_delete)
            .end()
    ;
}

function vendor_add()
{
    $new_vendor_cnt++;
    var dom_id = 'new_vendor_' + $new_vendor_cnt;

    var $this = $(this).parents('div.vendor_info');
    var new_dom = $.tmpl('tmpl_vendor')
        .insertAfter($this)
        .find('textarea.vendor_desc')
            .attr('id', dom_id)
            .end()
    ;

    // set tinymce to the new vendor editor
    tinyMCE.execCommand('mceAddEditor', true, dom_id);

    setup_vendor_info.call(new_dom);

    new_dom.find('.vendor_name').focus();

    return false;
}

function vendor_delete()
{
    var $this = $(this).parents('div.vendor_info');

    if ($this.siblings('div.vendor_info').length == 0)
    {
        vendor_add.call(this);
    }

    $this.remove();

    return false;
}

})();
