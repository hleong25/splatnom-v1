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
    
},

addNewMenu: function(elem)
{
    var objThis = $(elem).parents('div.menu');
    
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
        
        .find('input:text')
            // user friendly... go to the first input
            .first()
            .focus()
    ;
},


removeNewMenu: function(elem)
{
    var objThis = $(elem).parents('div.menu');
    
    if (objThis.siblings('div.menu').length == 0)
    {
        // just reset it...
        objThis
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

            .find('input:text')
                // user friendly... go to the first input
                .first()
                .focus()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }
},

addNewMenuItem: function(elem)
{
    var objThis = $(elem).parent('div.menu_item');
    
    var clone_obj = objThis
        .clone()
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
    ;
},

removeNewMenuItem: function(elem)
{
    var objThis = $(elem).parent('div.menu_item');
    
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

                .end()
                
            .find('input:text')
                // user friendly... go to the first input
                .first()
                .focus()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }
}

};