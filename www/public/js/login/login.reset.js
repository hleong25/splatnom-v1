var js_login = (function() {

init();

return {
    // empty
};

function init()
{
    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

    $('form input.txt:first').focus();

    $('input.button')
        .button()
    ;
}

})();
