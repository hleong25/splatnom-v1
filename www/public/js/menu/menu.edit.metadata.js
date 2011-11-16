var js_menu = (function() {

init();

return {
    toggleOnClick: toggleOnClick,
    toggleOnHoverIn: toggleOnHoverIn,
    toggleOnHoverOut: toggleOnHoverOut,
    formOnSubmit: formOnSubmit,
    view: view,
    export: export,
    purgeMenu: purgeMenu,
    hideAll: hideAll,
    showAll: showAll,
    addLink: addLink,
    removeLink: removeLink,
    googleSearchAddress: googleSearchAddress,
    moveMenu: moveMenu,
    menuNameOnChange: menuNameOnChange,
    addMenuItem: addMenuItem,
    removeMenuItem: removeMenuItem
};

function init()
{
    formOnSubmit();
}

function formOnSubmit()
{
    keyboardNavigation($('div.menu_item input:text'));
}

function keyboardNavigation(inputText)
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
}

function purgeMenu(purgeUrl)
{
    var bPurge = confirm('Are you sure you want to purge this menu?');

    if (!bPurge)
    {
        return false;
    }

    location.href = purgeUrl;
}

function toggleOnClick(elem)
{
    $(elem).siblings('div.toggle').toggle();
}

function toggleOnHoverIn(elem)
{
    $(elem).css('cursor', 'pointer');
}

function toggleOnHoverOut(elem)
{
    $(elem).css('cursor', 'auto');
}

function menuNameOnChange(input)
{
    input = $(input);
    var name = input.val();

    input
        .parents('div.menu')
        .find('div.heading > span.menu_name')
        .text(name)
    ;

}

function addMenu(elem)
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

    keyboardNavigation(clone_obj.find('div.menu_item input:text'));
}


function removeMenu(elem)
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
}

function addMenuItem(elem)
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

    keyboardNavigation(clone_obj.find('input:text'));
}

function removeMenuItem(elem)
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
}

function googleSearchAddress()
{
    var txtAddress = $('textarea[name="info_address"]').val();
    var params = $.param({
            'q': txtAddress
    });

    var url = 'http://maps.google.com/maps?' + params;

    var map_window = window.open(url, 'gmaps');

    return false;
}

function hideAll()
{
    $('div.pg > div.toggle').hide();
}

function showAll()
{
    $('div.pg > div.toggle').show();
}

function moveMenu(elem, position)
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

}

function addLink(elem)
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

    keyboardNavigation(clone_obj.find('input:text'));
}

function removeLink(elem)
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
}

function view(viewUrl)
{
    var bView = confirm('Are you sure you want to quit editting this menu?');

    if (!bView)
    {
        return false;
    }

    location.href = viewUrl;

}

function export(exportUrl)
{
    var bView = confirm('Are you sure you want to quit editting this menu?');

    if (!bView)
    {
        return false;
    }

    location.href = exportUrl;

}

})();
