var js_splatnom = (function() {

init();

return {
    // empty
};

function init()
{
    $('input#btnAddMore').click(btnAddMore_OnClick);

    $('input:button,input:submit,input:file').button();

    $.template('tmpl_add_img', $('script#tmpl_add_img'));
}

function btnAddMore_OnClick()
{
    var last_file = $('li.file:last');

    var new_dom = $.tmpl('tmpl_add_img')
        .insertAfter(last_file)
        .find('input.file')
            .button()
            .end()
    ;
}

})();
