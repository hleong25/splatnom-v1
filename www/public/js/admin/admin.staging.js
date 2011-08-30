var js_admin = {

onDocReady: function ()
{
    js_admin.initToggleDiv();
    
},

initToggleDiv: function()
{
    $('div.onToggle').click(function()
    {
        $(this).siblings('div.toggle').toggle();
    });
},

formOnSubmit: function(form)
{
    
    return true;
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
    
}

};