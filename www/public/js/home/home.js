var js_home = {

onDocReady: function ()
{
    $('form#searchit').ajaxForm({
        target:         '#results',
        beforeSubmit:   js_home.funcPreSearch
    });

    js_home.loadSettings();

    $('form#searchit input[name="query"]').focus();
},

loadSettings: function ()
{
    $('form#searchit input')
        .filter('input[name="query"]')
            .val($.cookie('search_query'))
            .change()

        .end()

        .filter('input[name="location"]')
            .val($.cookie('search_loc'))
            .change()
    ;
},

saveSettings: function ()
{
    $.cookie('search_query', $('form#searchit input[name="query"]').val());
    $.cookie('search_loc', $('form#searchit input[name="location"]').val());
},

funcPreSearch: function ()
{
    js_home.saveSettings();
    return true;
}

};
