var js_menu = (function() {

init();

return {
    forkit: forkit
};

function init()
{
    $('div.group')
        .mouseover(function() {
            $(this).addClass('zover');
            //$(this).css('cursor', 'pointer');
        })
        .mouseout(function() {
            $(this).removeClass('zover');
            //$(this).css('cursor', 'auto');
        })
    ;

    $('a.button').button();

    scrollToLink();
}

function forkit(elem, link)
{
    var objPanel =  $(elem).parents('div.g_panel');

    $.getJSON(link)
        .success(function(data){forkit_success(objPanel, data)})
        .error(function(){alert('Failed to fork it!');})
    ;
}

function forkit_success(objThis, data)
{
    if (data.status === 'error')
    {
        alert(data.msg);
    }
    else if ((data.status === 'forkit') || (data.status === 'unforkit'))

    {
        objThis
            .find('div.forkit')
                .toggle();
        ;
    }
    else
    {
        alert('Unexpected result...');
    }
}

function scrollToLink()
{
    var body = $('body');

    $('a[href^=#]').click(function(elem){
        var name = $(this).attr('href').substr(1);
        var pos = $('a[name='+name+']').offset();
        //body.animate({scrollTop: pos.top});
        body.scrollTop(pos.top);
        elem.preventDefault();
    });
}

})();
