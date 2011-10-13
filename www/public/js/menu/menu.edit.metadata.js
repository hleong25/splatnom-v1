var js_menu = {

onDocReady: function()
{
    js_menu.keyboardNavigation($('div.menu_item input:text'));
},

keyboardNavigation: function(inputText)
{
    inputText
        .keyup(function(event){
                if (event.ctrlKey)
                {
                    switch (event.keyCode)
                    {
                        case 38: // up
                        case 40: // down
                            var objThis = $(this);
                            var title = objThis.attr('title');
                            var newThis = null;

                            if (event.keyCode == 38)
                                newThis = objThis.parents('div.menu_item').prev();
                            else
                                newThis = objThis.parents('div.menu_item').next();

                            newThis.find('input[type="text"][title="'+title+'"]').focus();

                            break;
                    }
                }
            })
    ;
},

purgeMenu: function(purgeUrl)
{
    var bPurge = confirm('Are you sure you want to purge this menu?');

    if (!bPurge)
    {
        return false;
    }

    location.href = purgeUrl;
},

toggleOnClick: function(elem)
{
    $(elem).siblings('div.toggle').toggle();
},

toggleOnHoverIn: function(elem)
{
    $(elem).css('cursor', 'pointer');
},

toggleOnHoverOut: function(elem)
{
    $(elem).css('cursor', 'auto');
},

menuNameOnChange: function(input)
{
    input = $(input);
    var name = input.val();

    input
        .parents('div.menu')
        .find('div.heading > span.menu_name')
        .text(name)
    ;

},

addMenu: function(elem)
{
    var objThis = $(elem).parents('div.menu');

    var clone_obj = objThis
        .clone(false)
        .insertAfter(objThis)

        .find('div.menu_item')
            // remove all but one menu item
            .not(':first')
                .remove()
                .end()
            .end()

        .find('input:text')
            // reset the fields
            .val('')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()
            .end()

        .find('span.menu_name')
            .text('')
            .end()

        .find('input:text')
            // user friendly... go to the first input
            .first()
                .focus()
                .end()
            .end()
    ;

    js_menu.keyboardNavigation(clone_obj.find('div.menu_item input:text'));
},


removeMenu: function(elem)
{
    var objThis = $(elem).parents('div.menu');

    if (objThis.siblings('div.menu').length == 0)
    {
        // just reset it...
        objThis
            .find('div.menu_item')
                // remove all but one menu item
                .not(':first')
                    .remove()
                    .end()
                .end()

            .find('input:text')
                // reset the fields
                .val('')

                // reset the watermark
                .filter('.jq_watermark')
                    .attr('data-jq-watermark', '')
                    .watermark()
                    .end()
                .end()

            .find('input:text')
                // user friendly... go to the first input
                .first()
                    .focus()
                    .end()
                .end()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }
},

addMenuItem: function(elem)
{
    var objThis = $(elem).parent('div.menu_item');

    var clone_obj = objThis
        .clone(false)
        .insertAfter(objThis)

        .find('input:text')
            // reset the fields
            .val('')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()
            .end()

        .find('input:text')
            // user friendly... go to the first input
            .first()
                .focus()
                .end()
            .end()
    ;

    js_menu.keyboardNavigation(clone_obj.find('input:text'));
},

removeMenuItem: function(elem)
{
    var objThis = $(elem).parent('div.menu_item');

    if (objThis.siblings('div.menu_item').length == 0)
    {
        // just reset it...
        objThis
            .find('input:text')
                // reset the fields
                .val('')

                // reset the watermark
                .filter('.jq_watermark')
                    .attr('data-jq-watermark', '')
                    .watermark()
                    .end()

                .end()

            .find('input:text')
                // user friendly... go to the first input
                .first()
                    .focus()
                    .end()
                .end()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }
},

googleSearchAddress: function()
{
    var txtAddress = $('textarea[name="info_address"]').val();
    var params = $.param({
            'q': txtAddress
    });

    var url = 'http://maps.google.com/maps?' + params;

    var map_window = window.open(url, 'gmaps');

    return false;
},

hideAll: function()
{
    $('div.pg > div.toggle').hide();
},

showAll: function()
{
    $('div.pg > div.toggle').show();
},

moveMenu: function(elem, position)
{
    var objThis = $(elem).parents('div.menu');
    var node = null;

    if (position > 0)
    {
        // move down
        node = objThis.next('div.menu');

        if (node == 0)
            return;

        objThis.insertAfter(node);
    }

    if (position < 0)
    {
        // move up
        node = objThis.prev('div.menu');

        if (node == 0)
            return;

        objThis.insertBefore(node);
    }

    //$('html, body').animate({scrollTop: objThis.offset().top}, 0);

},

addLink: function(elem)
{
    var objThis = $(elem).parent('div.link_item');

    var clone_obj = objThis
        .clone(false)
        .insertAfter(objThis)

        .find('input:text')
            // reset the fields
            .val('')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()
            .end()

        .find('input:text')
            // user friendly... go to the first input
            .first()
                .focus()
                .end()
            .end()
    ;

    js_menu.keyboardNavigation(clone_obj.find('input:text'));
},

removeLink: function(elem)
{
    var objThis = $(elem).parent('div.link_item');

    if (objThis.siblings('div.link_item').length == 0)
    {
        // just reset it...
        objThis
            .find('input:text')
                // reset the fields
                .val('')

                // reset the watermark
                .filter('.jq_watermark')
                    .attr('data-jq-watermark', '')
                    .watermark()
                    .end()

                .end()

            .find('input:text')
                // user friendly... go to the first input
                .first()
                    .focus()
                    .end()
                .end()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }
},

view: function(viewUrl)
{
    var bView = confirm('Are you sure you want to quit editting this menu?');

    if (!bView)
    {
        return false;
    }

    location.href = viewUrl;

}

}
