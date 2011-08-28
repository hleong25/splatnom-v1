var js_menu = {
    
onDocReady: function()
{
    js_menu.init();
},

init: function()
{
    $('input.btnItemAddMore').click(js_menu.btnItemAddMore_OnClick);
    $('input.btnItemRemove').click(js_menu.btnItemRemove_OnClick);
},

btnItemAddMore_OnClick: function ()
{
    var objThis = $(this).parent();
    
    objThis
        .clone(true)
        .insertAfter(objThis)
        
        // reset the watermark
        .find('input.jq_watermark')
            .val('')
            .attr('data-jq-watermark', '')
            .watermark()
    ;
},

btnItemRemove_OnClick: function ()
{
    var objThis = $(this).parent();
    
    if (objThis.siblings('div.item').length == 0)
    {
        // just reset it...
        objThis
            .find(':input')
            .not(':button')
                .val('')
                .attr('data-jq-watermark', '')
                .watermark()
        ;
    }
    else
    {
        // remove it...
        objThis.remove();
    }
    
}

}