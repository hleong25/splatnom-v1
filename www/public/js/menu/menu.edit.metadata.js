var js_menu = {

onDocReady: function()
{
    // empty
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

addNewMenu: function(elem)
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
},


removeNewMenu: function(elem)
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

addNewMenuItem: function(elem)
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
},

removeNewMenuItem: function(elem)
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
}

}
