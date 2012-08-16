var js_menu = (function() {

init();

return {
    // empty
};

function init()
{
    var form = $('form#searchit');

    form.find('button.search')
        .button({
            icons: {primary: 'ui-icon-search'}
        })
    ;

    form.find('.watermark').each(function(){
        var $this = $(this);
        $this.watermark($this.attr('placeholder'));
    });

    form.find('input[name="query"]').focus();
}

})();
