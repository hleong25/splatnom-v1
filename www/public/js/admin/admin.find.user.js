var js_admin = (function() {

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

    $('form#search_user input[name="query_user"]').focus();
}

})();
