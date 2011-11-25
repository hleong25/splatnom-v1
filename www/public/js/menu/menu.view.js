var js_menu = (function() {

init();

return {
    // empty
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

    $('a.forkit').click(forkit);
}

function forkit(elem)
{
    var objThis = $(this);
    var url = objThis.attr('href');

    elem.preventDefault();

    $.getJSON(url)
        .success(function(data){forkit_success(objThis, data)})
        .error(function(){alert('Failed to fork it!');})
    ;
}

function forkit_success(objThis, data)
{
    if (data.status === 'error')
    {
        alert(data.msg);
    }
    else if (data.status === 'forkit')
    {
        objThis
            .find('img')
                .addClass('forkit')
            //.end()
        ;
    }
    else if (data.status === 'unfork')
    {
        objThis
            .find('img')
                .removeClass('forkit')
            //.end()
        ;
    }
    else
    {
        alert('Unexpected result...');
    }
}

})();
