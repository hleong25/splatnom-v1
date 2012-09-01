var js_user = (function() {

init();

return {
    // empty
};

function init()
{
    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function() {
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

    $('input:submit').button();
}

})();
