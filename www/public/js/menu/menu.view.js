var js_menu = (function() {

init();

return {
    // none
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
        .each(function(){
            var $this = $(this);
            var $icons = new Object();

            if ($this.hasClass('search'))
            {
                $icons.primary = 'ui-icon-search';
            }
            else if ($this.hasClass('export'))
            {
                $icons.primary = 'ui-icon-document';
            }
            else if ($this.hasClass('editmenu'))
            {
                $icons.primary = 'ui-icon-pencil';
            }
            else if ($this.hasClass('addimg'))
            {
                $icons.primary = 'ui-icon-plus';
            }

            $this.button({icons: $icons});
        })
    ;

    $body = $('html,body');

    show_section();

    setup_forkit();
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

function setup_forkit()
{
    var links_forkit = $('a.forkit');

    links_forkit.click(function(event){
        event.preventDefault();

        var $this = $(this);
        var $this_after = $this.siblings('a.forkit');

        $.getJSON(this.href)
            .error(function(){alert('Failed to fork it!');})
            .success(function(data){
                if (data.status === 'error')
                {
                    alert(data.msg);
                }
                else if ((data.status === 'forkit') || (data.status === 'unforkit'))
                {
                    $this.toggle();
                    $this_after.toggle();
                }
                else
                {
                    alert('Unexpected result...');
                }
            })
        ;

    });
}

})();
