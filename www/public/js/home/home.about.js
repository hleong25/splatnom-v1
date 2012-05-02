var js_home = (function() {

init();

return {
    // empty
};

function init()
{
    tinyMCE.init({
        mode: 'exact',
        elements: 'editor',
        theme: 'advanced',
        plugins: 'save,style',

        theme_advanced_buttons1 : 'save,|,styleprops,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect',
        theme_advanced_buttons2 : '',
        theme_advanced_buttons3 : '',
        theme_advanced_buttons4 : '',
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true
    });
}

})();
