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
    ;

    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

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

    var links =
        $('.site_menu')
            .filter(function(){
                return $(this).val() != '';
            })
            .length
    ;

    if (imgs === 0 && links === 0)
    {
        alert('Need at least one menu');
        return false;
    }

    return true;
}

})();
