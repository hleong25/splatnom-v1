var js_login = (function() {

init();

return {
    // empty
};

function init()
{
    $('form#login').find('.watermark').each(function() {
        var $this = $(this);
        $this.watermark($this.attr('placeholder'));
    });

    $('form#login input#username').focus();

    $('.button')
        .button()
        .on('click', onclick_button)
    ;
}

function onclick_button(event)
{
    var $this = $(this);
    var type = $this.attr('type');

    if (type == 'reset')
    {
        event.preventDefault();

        $('input.txt')
            .val('').focus()
            .filter('#username').focus().end()
        ;
    }
}

})();
