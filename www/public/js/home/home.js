var js_home = (function() {

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

    $('div.metadata a.menu')
        .button()
        .find('.edit').on('click', menu_edit).end()
        .find('.purge').on('click', menu_purge).end()
    ;
}

function menu_edit(event)
{
    // do nothing
}

function menu_purge(event)
{
    event.preventDefault();

    var $this = $(this);
    var link = $this.attr('href');

    // execute the link
    $.get(link);

    // remove the row from the table
    $this.parents('tr').remove();
}

})();
