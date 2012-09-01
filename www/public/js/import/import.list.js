var js_import = (function() {

init();

return {
    // empty
};

var chk_all;
var chk_menus;

function init()
{
    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

    chk_menus = $('input.menu_id:checkbox'); // cache the checkboxes

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
