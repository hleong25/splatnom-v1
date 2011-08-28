var js_menu = {
    
onDocReady: function()
{
    $('input#btnAddMore').click(js_menu.btnAddMore_OnClick);
    
    $('form#frmNewMenu')
        .submit(js_menu.form_OnSubmit)
        .find('input[name="name"]').focus()
    ;
},

btnAddMore_OnClick: function ()
{
    var objThis = $(this);
    objThis
        .siblings('div.new_img:last')
        .clone()
        .insertBefore(objThis)
    ;
},

form_OnSubmit: function ()
{
    return js_menu.funcValidateForm();
},

funcValidateForm: function ()
{
    if ($('input[name="name"]').val().trim().length === 0)
    {
        alert('Name is required');
        $('input[name="name"]').focus();
        return false;
    }
    
    var imgs = 
        $('div.new_img > input[type="file"]')
            .filter(function(){ 
                return $(this).val() != ''; 
            })
            .length
    ;
    
    if (imgs === 0)
    {
        alert('Need at least one menu');
        return false;
    }
    
    return true;
}

}