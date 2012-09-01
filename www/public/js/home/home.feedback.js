var js_home = (function() {

init();

return {
    // empty
};

function init()
{
    $('input:submit').button();

    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

    $('.edit').each(function() {
        var $this = $(this);

        if ($this.val().length == 0)
        {
            $this.focus();
            return false;
        }
    });
}

})();
