var js_menu = {

onDocReady: function()
{
    js_menu.init();
},

init: function()
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

}
