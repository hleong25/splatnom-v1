var js_menu = (function() {

var tagForm = null;
var acTemplate = null;

init();

return {
    taggit_remove: taggit_remove,
};

function init()
{
    $('input.save_taggits').button();

    customAutoComplete();

    tagForm = $('form.taggit');
    acTemplate = $('div.autocomplete > div.template');

    $('input#tags').focus();
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
        .appendTo(tagForm)
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

function taggit_remove(item)
{
    var objThis = $(item).parents('div.tag_group');

    objThis
        .hide()
        .find('input[name="add[]"]')
            .val('')
            .end()
    ;
}

})();
