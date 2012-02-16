var js_menu = (function() {

init();

return {
    purgeMenu: purgeMenu,
    view: view,
    export: export,
    hideAll: hideAll,
    showAll: showAll,
    googleSearchAddress: googleSearchAddress,
    moveMenu: moveMenu,
    menu_add: menu_add,
    menu_remove: menu_remove,
    menuitem_add: menuitem_add,
    menuitem_remove: menuitem_remove,
};

function init()
{
    $('div.onToggle').on({
        click: toggle_onClick,
        mouseover: toggle_onHoverIn,
        mouseout: toggle_onHoverOut
    });

    $('input:button.link_add').on('click', link_add);
    $('input:button.link_remove').on('click', link_remove);

    $('div.group_info input:text.menu_name').on('change', menuName_onChange);
//    $('div.controller input:button.menu_add').on('click', menu_add);
//    $('div.controller input:button.menu_remove').on('click', menu_remove);

    $('div.menu_item input:text').on('keyup', keyboardNavigation);
//    $('div.menu_item input:image.menuitem_add').on('click', menuitem_add);
//    $('div.menu_item input:image.menuitem_remove').on('click', menuitem_remove);

    $('a.button').button();
    $('input:button').button();
    $('input:submit').button();
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
    var objThis = $(this).parent('div.link_item');

    var clone_obj = objThis
        .clone(true)
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
}

function link_remove()
{
    var objThis = $(this).parent('div.link_item');

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

function menuName_onChange()
{
    input = $(this);
    var name = input.val();

    input
        .parents('div.menu')
        .find('div.heading > span.menu_name')
        .text(name)
    ;
}

function menu_add(item)
{
    var objThis = $(item).parents('div.menu');

    var clone_obj = objThis
        .clone()
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

        .find('input:checkbox')
            // uncheck items
            .attr('checked', false)
            .end()

        .find('div.group_info input[type="hidden"].sid')
            // reset id
            .val('')
            .end()

        .find('div.menu_item input[type="hidden"].mid')
            // reset id
            .val('')
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

    return false;
}

function menu_remove(item)
{
    var objThis = $(item).parents('div.menu');

    if (objThis.siblings('div.menu').length == 0)
    {
        // just reset it...
        objThis
            .find('div.menu_item')
                // remove all but one menu item
                .not(':first').remove().end()
                .end()

            .find('input:text')
                // reset the fields
                .val('')

                // reset the watermark
                .filter('.jq_watermark')
                    .attr('data-jq-watermark', '')
                    .watermark()
                    .end()

                // user friendly... go to the first input
                .first().focus().end()

                .end()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }

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
}

function menuitem_add(item)
{
    var objThis = $(item).parent('div.menu_item');

    var clone_obj = objThis
        .clone()
        .insertAfter(objThis)

        .find('input[type="hidden"].mid')
            // reset id
            .val('')
            .end()

        .find('input:text')
            // reset the fields
            .val('')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()

            // user friendly... go to the first input
            .first().focus().end()
            .end()

        .find('input:checkbox')
            // uncheck items
            .attr('checked', false)
            .end()
    ;

    return false;
}

function menuitem_remove(item)
{
    var objThis = $(item).parent('div.menu_item');

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

                // user friendly... go to the first input
                .first().focus().end()

                .end()

            .end()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }

    return false;
}

})();
