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
            icons: {primary: 'ui-icon-search'},
        })
    ;

    form.find('input[name="query"]').focus();
}

})();
