var js_menu = (function() {

init();

return {
    // empty
};

function init()
{
    $('input#btnAddMore').click(btnAddMore_OnClick);

    $('form#frmNewMenu')
        .submit(form_OnSubmit)
        .find('input[name="name"]').focus()
    ;
}

function btnAddMore_OnClick()
{
    var objThis = $(this);
    objThis
        .siblings('div.new_img:last')
        .clone()
        .insertBefore(objThis)
    ;
}

function form_OnSubmit()
{
    return funcValidateForm();
}

function funcValidateForm()
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

})();
