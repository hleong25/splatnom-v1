var js_menu = (function() {

init();

return {
    //forkit: forkit
};

var $body;

function init()
{
    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

    $('.button')
        .button({
            icons: {primary: 'ui-icon-search'}
        })
    ;

    $body = $('html,body');

    show_section();
}

function show_section()
{
    var $menu_sections = $('ul.menu');

    $('a.nav_item').click(function(elem){
        var section_id = $(this).attr('href').substr(1);

        if (section_id == 0)
        {
            $menu_sections.show();
        }
        else
        {
            $menu_sections
                .each(function() {
                    var $this = $(this);
                    if ($this.hasClass(section_id))
                    {
                        $this.show();

                        $body.scrollTop($this.offset().top);
                    }
                    else
                    {
                        $this.hide();
                    }
                })
            ;
        }

        elem.preventDefault();
    });
}
})();
