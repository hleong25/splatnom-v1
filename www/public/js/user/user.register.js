var js_user = (function() {

init();

return {
    // empty
};

function init()
{
    $('.watermark').each(function() {
        var $this = $(this);
        $this.watermark($this.attr('placeholder'));
    });

    //$('form#new_user input:first').focus();
    $('input:submit').button();
}

})();
