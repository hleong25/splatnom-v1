var js_menu = (function() {

var div_taggits = null;
var acTemplate = null;

init();

return {
    // empty
};

function init()
{
    div_taggits = $('div.taggits');
    acTemplate = $('div.autocomplete > div.template');
    customAutoComplete();

    $('input:submit').button();

    acTemplate.button({icons: {primary: 'ui-icon-close'}});

    $('div.taggits > div.tag_group:not(div.template)')
        .live('click', taggit_remove)
        .button({icons: {primary: 'ui-icon-close'}})
    ;
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
            'select': acSelect,
        })
    ;
}

function acSelect(event, ui)
{
    var item = ui.item;
    var label = '('+item.section+') '+item.label;

    acTemplate
        .clone()
        .appendTo(div_taggits)
        .removeClass('template')
        .find('input[name="sid[]"]')
            .val(item.sid)
            .end()
        .find('input[name="mid[]"]')
            .val(item.mid)
            .end()
        .find('span.label')
            .text(label)
            .end()
    ;
}

function taggit_remove(event)
{
    var srcItem = $(event.srcElement);
    if (!srcItem.hasClass('ui-icon-close'))
        return;

    var objThis = $(srcItem).parents('div.tag_group');

    objThis
        .hide()
        .find('input[name="add[]"]')
            .val('')
            .end()
    ;
}

})();
