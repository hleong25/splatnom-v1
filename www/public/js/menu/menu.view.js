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
    elem.preventDefault();

    alert('henry');
}

})();
