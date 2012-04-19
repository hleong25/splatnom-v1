var js_menu = (function() {

var div_taggits = null;

init();

return {
    // empty
};

function init()
{
    $('input.save_taggits').button();

    $.template('tmpl_taggit', $('script#tmpl_taggit'));

    customAutoComplete();

    div_taggits = $('div.current_tags');

    setup_taggit.call($('div.tag_group'));
}

function customAutoComplete()
{
    $.widget('js_menu.myautocomplete', $.ui.autocomplete, {
        _renderMenu: function(ul, items) {
            var self = this;
            var currentSection = '';

            $.each(items, function(index, item) {
                if (item.section != currentSection) {
                    ul.append('<li class="ui-autocomplete-section">'+item.section+'</li>');
                    currentSection = item.section;
                }
                self._renderItem(ul, item);
            });
        }
    });

    $('input#tags')
        .myautocomplete({
            'source': menu_tags,
            //'minLength': 2,
            'delay': 100, // local data -- refresh fast!
            'select': taggitSelect,
        })
    ;
}

function taggitSelect(event, ui)
{
    var item = ui.item;

    var data = {
        label: '('+item.section+') '+item.label,
        sid: item.sid,
        mid: item.mid
    };

    setup_taggit.call(
        $.tmpl('tmpl_taggit', data).appendTo(div_taggits)
    );
}

function setup_taggit()
{
    $(this)
        .button({
            icons: {primary: 'ui-icon-close'}
        })
        .on('click', taggit_remove)
    ;
}

function taggit_remove(item)
{
    var srcItem = $(event.srcElement);

    if (!srcItem.hasClass('ui-icon-close'))
        return;

    var objThis = $(srcItem).parents('div.tag_group');

    if (objThis.hasClass('need_login'))
    {
        alert('You must be logged in to untaggit');
        return;
    }

    objThis
        .hide()
        .find('input[name="add[]"]')
            .val('')
            .end()
    ;
}

})();
