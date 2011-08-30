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
        .each(function(idx, tr) {js_admin.fixHourIndexes(idx, tr);})
    ;
    
    return true;
},

fixHourIndexes: function(idx, tr)
{
    if ($(tr).hasClass('template'))
        return;
    
    $(tr)
        .find('input,select')
        .each(function (idx_chk, input)
            {
                var attr_name = $(input).attr('name');
                attr_name = attr_name.replace(/@id/g, idx);
                $(input).attr('name', attr_name);
            })
    ;
},

addMoreTimeOnClick: function()
{
    var table = $('table#hours');
    var tr_hours = table.find('tr.template');
    var tbody = table.find('tbody');
    
    for (var ii = 0; ii < 5; ++ii)
    {
        tbody.append(tr_hours.clone().removeClass('template'));
    }
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