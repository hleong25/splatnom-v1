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
        })
        .mouseout(function() {
            $(this).removeClass('zover');
        })
    ;
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

})();
