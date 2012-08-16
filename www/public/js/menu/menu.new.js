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
        .find('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        })
    ;

    $('input:button').button();
    $('input:submit').button();
    $('input:file').button();
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
        $('div.new_img > input[type="file"]')
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
