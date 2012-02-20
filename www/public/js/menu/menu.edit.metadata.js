var js_menu = (function() {

init();

return {
    //empty
};

function init()
{
    $('button').on('click', function(e){
        e.preventDefault();
    });

    $('div.action > input.button')
        .button()
        .each(onAction_button)
    ;

    $('.search_address')
        .button()
        .on('click', googleSearchAddress)
    ;

    $('div.onToggle').on({
        click: toggle_onClick,
        mouseover: toggle_onHoverIn,
        mouseout: toggle_onHoverOut
    });

    $('a.img_add')
        .button({
            text: false,
            icons: {primary: 'ui-icon-plusthick'},
        })
    ;

    $.proxy(events_link, document)();
    $.proxy(events_menu, document)();
}

function events_link()
{
    $('.link_add', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-plusthick'},
        })
        .off('click.link_add')
        .on('click.link_add', link_add);

    $('.link_remove', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-closethick'},
        })
        .off('click.link_remove')
        .on('click.link_remove', link_remove);
}

function events_menu()
{
    $('.move_up', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-arrowthick-1-n'},
        })
        .off('click.move_up')
        .on('click.move_up', function(){
            move_menu(this, -1);
            return false;
        })
    ;

    $('.move_down', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-arrowthick-1-s'},
        })
        .off('click.menu_down')
        .on('click.menu_down', function(){
            move_menu(this, 1);
            return false;
        })
    ;

    $('.menu_add', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-plusthick'},
        })
        .off('click.menu_add')
        .on('click.menu_add', menu_add)
    ;

    $('.menu_remove', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-closethick'},
        })
        .off('click.menu_remove')
        .on('click.menu_remove', menu_remove)
    ;

    $('input.menu_name', this)
        .off('change.menu_name')
        .on('change.menu_name', function(){
            var $this = $(this);
            var name = $this.val();

            $this
                .parents('div.menu')
                .find('.heading span.menu_name')
                .text(name)
            ;
        })
    ;

    $('.menu_group', this)
        .off('focusin.menu')
        .off('focusout.menu')
        .off('keyup.menu')
        .on('focusin.menu', 'textarea', function(){
            var $this = $(this);
            $this
                .addClass('item_edit')
            ;
        })
        .on('focusout.menu', 'textarea', function(){
            var $this = $(this);
            $this
                .removeClass('item_edit')
            ;
        })
        .on('keyup.menu', function(e){
            if (e.keyCode == 27)
                $(e.target).focusout();
        })
    ;

    $.proxy(events_item, this)();
}

function events_item()
{
    $('.item_add', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-plusthick'},
        })
        .off('click.item_add')
        .on('click.item_add', item_add)
    ;

    $('.item_remove', this)
        .button({
            text: false,
            icons: {primary: 'ui-icon-closethick'},
        })
        .off('click.item_remove')
        .on('click.item_remove', item_remove)
    ;

    $('.item_label,.item_price,.item_notes', this)
        .off('keyup.item_textarea')
        .on('keyup', keyboardNavigation)
    ;
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

    var clone_obj = $this
        .clone()
        .insertAfter($this)

        .find('input:text')
            // reset the fields
            .val('')

            // remove watermark events
            .off('.jq_watermark')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()

            // user friendly... go to the first input
            .first().focus().end()

            .end()
    ;

    // set the events
    $.proxy(events_link, clone_obj)();

    return false;
}

function link_remove()
{
    var $this = $(this).parent('div.link_item');

    if ($this.siblings('div.link_item').length == 0)
    {
        $.proxy(link_add, this)();
    }

    $this.remove();

    return false;
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

        if (node == 0)
            return;

        insertFunc = 'insertAfter';
    }

    if (position < 0)
    {
        // move up
        node = $this.prev('div.menu');

        if (node == 0)
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

function item_add()
{
    var $this = $(this).parent('div.menu_item');

    var clone_obj = $this
        .clone()
        .insertAfter($this)

        .find('input[type="hidden"].mid')
            // reset id
            .val('')
            .end()

        .find('input:checkbox')
            // uncheck items
            .attr('checked', false)
            .end()

        .find('textarea')
            // reset the fields
            .text('')

            // remove watermark events
            .off('.jq_watermark')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()

            // user friendly... go to the first input
            .first().focus().end()

            .end()
    ;

    // set the events
    $.proxy(events_item, clone_obj)();

    return false;
}

function item_remove()
{
    var $this = $(this).parent('div.menu_item');

    if ($this.siblings('div.menu_item').length == 0)
    {
        $.proxy(item_add, this)();
    }

    // remove it...
    $this.remove();

    return false;
}

function menu_add()
{
    var $this = $(this).parents('div.menu');

    var clone_obj = $this
        .clone()
        .insertAfter($this)

        .find('input[type="hidden"].sid')
            // reset id
            .val('')
            .end()

        .find('span.menu_name')
            .text('')
            .end()

        .find('div.menu_item:gt(0)')
            .remove()
            .end()

        .find('input:text')
            // reset the fields
            .val('')

            // remove watermark events
            .off('.jq_watermark')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()

            .end()

        .find('textarea')
            // reset the fields
            .text('')

            // remove watermark events
            .off('.jq_watermark')

            // reset the watermark
            .filter('.jq_watermark')
                .attr('data-jq-watermark', '')
                .watermark()
                .end()

            .end()
    ;

    // clear the item values
    $.proxy(item_remove, clone_obj.find('.item_remove'))();

    // user friendly... go to the first input
    clone_obj.find('input:text:first').focus().end();

    // set the events
    $.proxy(events_menu, clone_obj)();

    return false;
}

function menu_remove()
{
    var $this = $(this).parents('div.menu');

    if ($this.siblings('div.menu').length == 0)
    {
        $.proxy(menu_add, this)();
    }

    // remove it...
    $this.remove();

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
                var title = $this.attr('title');
                var newThis = null;

                if (event.keyCode == 38)
                    newThis = $this.parents('div.menu_item').prev();
                else
                    newThis = $this.parents('div.menu_item').next();

                newThis.find('textarea[title="'+title+'"]').focus();

                break;
        }
    }
}


})();
