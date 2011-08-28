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
    // update indexes for hours
    $(form)
        .find('table#hours tr')
        .each(function(idx, tr) { js_admin.fixHourIndexes(idx, tr); })
    ;
    
    return true;
},

fixHourIndexes: function(idx, tr)
{
    $(tr)
        .find('input:checkbox')
        .each(function (idx_chk, input)
            {
                var attr_name = $(input).attr('name');
                attr_name = attr_name.replace(/@id/g, idx);
                $(input).attr('name', attr_name);
            })
    ;
}

};