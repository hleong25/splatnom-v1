var js_admin = (function() {

init();

return {
    // empty
};

var chk_all;
var chk_menus;

function init()
{
    chk_menus = $('input.cssitem:checkbox'); // cache the checkboxes

    chk_all = $('input#chkAll')
        .show()
        .on('change', chkall)
    ;

    $('.button').button();
}

function chkall()
{
    chk_menus.prop('checked', chk_all.prop('checked'));
}

})();
