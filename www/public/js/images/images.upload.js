var js_images = (function() {

init();

return {
    // empty
};

function init()
{
    $('input#btnAddMore').click(btnAddMore_OnClick);

    $('form#upload_photos')
        .submit(form_OnSubmit)
    ;

    $('input:button,input:submit,input:file').button();
}

function btnAddMore_OnClick()
{
    var objThis = $(this);
    objThis
        .siblings('input.file:last')
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
    var imgs =
        $('input.file')
            .filter(function(){
                return $(this).val() != '';
            })
            .length
    ;

    if (imgs === 0)
    {
        alert('Need at least one image');
        return false;
    }

    return true;
}

})();
