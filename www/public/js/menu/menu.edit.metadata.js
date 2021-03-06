var js_menu = (function() {

init();

return {
    //empty
};

var $edit_form;
var $input_items;

function init()
{
    $('button').on('click', function(e){
        e.preventDefault();
    });

    $('div.action > input.button')
        .button()
        .each(onAction_button)
    ;

    $('.find_latlong')
        .button()
        .on('click', getLatLongFromAddress)
    ;

    $('.map_addy')
        .button()
        .on('click', showGoogleMapAddy)
    ;

    $('a.img_add')
        .button({
            text: false,
            icons: {primary: 'ui-icon-plusthick'},
        })
    ;

    $.template('tmpl_link', $('script#tmpl_link'));
    $.template('tmpl_menu', $('script#tmpl_menu'));
    $.template('tmpl_item', $('script#tmpl_item'));

    setup_link.call(document);
    setup_menu.call(document);

    $edit_form = $('#edit_mdt');
    $input_items = $('#input_items');

    show_form_input_count();
    autoupdate_show_form_input_count();
}

function show_form_input_count()
{
    $input_items.text($edit_form.serializeArray().length);
}

function autoupdate_show_form_input_count()
{
    show_form_input_count();
    setTimeout(autoupdate_show_form_input_count, 60000); // update very minute
}

function setup_link()
{
    $(this)
        .find('.link_add')
            .button({
                text: false,
                icons: {primary: 'ui-icon-plusthick'},
            })
            .on('click.link_add', link_add)
            .end()

        .find('.link_remove')
            .button({
                text: false,
                icons: {primary: 'ui-icon-closethick'},
            })
            .on('click.link_remove', link_remove)
            .end()
    ;
}

function setup_menu()
{
    $(this)
        .find('div.onToggle')
            .on({
                click: toggle_onClick,
                mouseover: toggle_onHoverIn,
                mouseout: toggle_onHoverOut
            })
            .end()

        .find('.move_up')
            .button({
                text: false,
                icons: {primary: 'ui-icon-arrowthick-1-n'},
            })
            .on('click.move_up', function(){
                move_menu(this, -1);
                return false;
            })
            .end()

        .find('.move_down')
            .button({
                text: false,
                icons: {primary: 'ui-icon-arrowthick-1-s'},
            })
            .on('click.menu_down', function(){
                move_menu(this, 1);
                return false;
            })
            .end()

        .find('.menu_add')
            .button({
                text: false,
                icons: {primary: 'ui-icon-plusthick'},
            })
            .on('click.menu_add', menu_add)
            .end()

        .find('.menu_remove')
            .button({
                text: false,
                icons: {primary: 'ui-icon-closethick'},
            })
            .on('click.menu_remove', menu_remove)
            .end()

        .find('input.menu_name')
            .on('change.menu_name', function(){
                var $this = $(this);
                var name = $this.val();

                $this
                    .parents('div.menu')
                    .find('.heading span.menu_name')
                    .text(name)
                ;
            })
            .end()

    ;

    setup_item.call(this);
}

function setup_item()
{
    var items = $('.item_add,.item_remove,.item_up,.item_down,.item_label,.item_price,.item_notes', this);

    for (var ii = 0, jj = items.length; ii < jj; ii++)
    {
        var $this = $(items[ii]);

        if ($this.hasClass('item_add'))
        {
            $this
                .button({
                    text: false,
                    icons: {primary: 'ui-icon-plusthick'},
                })
                .on('click.item_add', item_add)
            ;
        }

        if ($this.hasClass('item_remove'))
        {
            $this
                .button({
                    text: false,
                    icons: {primary: 'ui-icon-closethick'},
                })
                .on('click.item_remove', item_remove)
            ;
        }

        if ($this.hasClass('item_up'))
        {
            $this
                .button({
                    text: false,
                    icons: {primary: 'ui-icon-arrowthick-1-n'},
                })
                .on('click.item_up', function(){
                    move_item(this, -1);
                    return false;
                })
           ;
        }

        if ($this.hasClass('item_down'))
        {
            $this
                .button({
                    text: false,
                    icons: {primary: 'ui-icon-arrowthick-1-s'},
                })
                .on('click.item_down', function(){
                    move_item(this, 1);
                    return false;
                })
           ;
        }

        if (($this.hasClass('item_label')) ||
            ($this.hasClass('item_price')) ||
            ($this.hasClass('item_notes')) )
        {
            $this
                .on('keyup', keyboardNavigation)
           ;
        }
    }
}

function onAction_button()
{
    var $this = $(this);
    var action = $this.data('action');

    switch (action)
    {
        case 'save':
            // do default
            break;

        case 'export':
            $this.on('click', function(){
                var url = $this.data('url');
                location.href = url;
            });
            break;

        case 'view':
            $this.on('click', function(){
                window.open($this.data('url'));
            });
            break;

        case 'refresh':
        case 'delete':
            $this.on('click', function(){
                var bAction = confirm('Are you sure you want to '+action+' this menu?');

                if (!bAction)
                    return false;

                var url = $this.data('url');
                location.href = url;
            });
            break;

        case 'hideall':
            $this.on('click', function(){
                $('form#edit_mdt div.toggle').hide();
            });
            break;

        case 'showall':
            $this.on('click', function(){
                $('form#edit_mdt div.toggle').show();
            });
            break;

        default:
            //console.log(action);
    }
}

function toggle_onClick()
{
    $(this).siblings('div.toggle').toggle();
}

function toggle_onHoverIn()
{
    $(this).css('cursor', 'pointer');
}

function toggle_onHoverOut()
{
    $(this).css('cursor', 'auto');
}

function link_add()
{
    var $this = $(this).parent('div.link_item');

    var new_dom = $.tmpl('tmpl_link').insertAfter($this);

    setup_link.call(new_dom);

    new_dom.find('.link_url').focus();

    show_form_input_count();
    return false;
}

function link_remove()
{
    var $this = $(this).parent('div.link_item');

    if ($this.siblings('div.link_item').length == 0)
    {
        link_add.call(this);
    }

    $this.remove();

    show_form_input_count();
    return false;
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

                if (loc.adminArea1 === 'United States of America')
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

function move_menu(elem, position)
{
    var $this = $(elem).parents('div.menu');
    var node = null;
    var speed = '';
    var insertFunc = '';

    if (position > 0)
    {
        // move down
        node = $this.next('div.menu');

        if (node.length == 0)
            return;

        insertFunc = 'insertAfter';
    }

    if (position < 0)
    {
        // move up
        node = $this.prev('div.menu');

        if (node.length == 0)
            return;

        insertFunc = 'insertBefore';
    }

    // animate the move
    $this
        .slideUp(speed, function(){
            $this
                [insertFunc](node)
                .slideDown(speed)
            ;
        })
    ;

    //$('html, body').animate({scrollTop: $this.offset().top}, 0);
}

function move_item(elem, position)
{
    var $this = $(elem).parents('div.menu_item');
    var node = null;
    var speed = '';
    var insertFunc = '';

    if (position > 0)
    {
        // move down
        node = $this.next('div.menu_item');

        if (node.length == 0)
            return;

        insertFunc = 'insertAfter';
    }

    if (position < 0)
    {
        // move up
        node = $this.prev('div.menu_item');

        if (node.length == 0)
            return;

        insertFunc = 'insertBefore';
    }

    // animate the move
    $this
        .slideUp(speed, function(){
            $this
                [insertFunc](node)
                .slideDown(speed)
            ;
        })
    ;
}

function item_add(event)
{
    var $this = $(this).parents('div.menu_item');
    var item_label = $this.find('.item_label').val();

    if (event && event.ctrlKey && (item_label.length > 0) && (item_label.search(/@@/) >= 0) && (item_label.search(/\n/) >= 0))
    {
        // special case when adding multiple lines -- helper for importing a lot of items
        var parsed_dom = $this;
        var lines = item_label.split("\n");

        var new_dom;
        var line;
        for (var idx in lines)
        {
            line = lines[idx];

            if (line.length == 0)
                continue;

            new_dom = $
                .tmpl('tmpl_item')
                .find('.item_label').text(line).end()
                .insertAfter($this)
            ;

            //setup_item.call(new_dom);
            $this = new_dom;
        }

        // remove the original parsed dom since we're done with it
        parsed_dom.remove();

        // get the group of items so we can set all them up at one shot
        var group = $this.parents('.menu_group');
        setup_item.call(group);
    }
    else
    {
        // regular item add
        var new_dom = $.tmpl('tmpl_item').insertAfter($this);

        setup_item.call(new_dom);

        new_dom.find('.item_label').focus().end()
    }

    show_form_input_count();
    return false;
}

function item_remove()
{
    var $this = $(this).parents('div.menu_item');

    if ($this.siblings('div.menu_item').length == 0)
    {
        item_add.call(this);
    }

    // remove it...
    $this.remove();

    show_form_input_count();
    return false;
}

function menu_add()
{
    var $this = $(this).parents('div.menu');

    var new_item = $.tmpl('tmpl_item');

    var new_dom = $.tmpl('tmpl_menu')
        .find('.menu_group').append(new_item).end()
        .insertAfter($this)
    ;

    setup_menu.call(new_dom);

    new_dom.find('.menu_name').focus().end()

    show_form_input_count();
    return false;
}

function menu_remove()
{
    var $this = $(this).parents('div.menu');

    if ($this.siblings('div.menu').length == 0)
    {
        menu_add.call(this);
    }

    // remove it...
    $this.remove();

    show_form_input_count();
    return false;
}

function keyboardNavigation(event)
{
    if (event.ctrlKey)
    {
        switch (event.keyCode)
        {
            case 38: // up
            case 40: // down
                var $this = $(this);
                var placeholder = $this.attr('placeholder');
                var newThis = null;

                if (event.keyCode == 38)
                    newThis = $this.parents('div.menu_item').prev();
                else
                    newThis = $this.parents('div.menu_item').next();

                newThis.find('textarea.item_'+placeholder.toLowerCase()).focus();

                break;
        }
    }
}


})();
