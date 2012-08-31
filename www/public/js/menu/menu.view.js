var js_menu = (function() {

init();

return {
    //forkit: forkit
};

function init()
{
    if (!Modernizr.input.placeholder)
    {
        $('.watermark').each(function(){
            var $this = $(this);
            $this.watermark($this.attr('placeholder'));
        });
    }

    $('.button').button();

    sticky_nav();

    show_section();
}

function sticky_nav()
{
    var isMobile = navigator.userAgent.match(/(iPhone|iPod|iPad|Android|BlackBerry)/);

    if (isMobile)
        return;

    var $window = $(window);
    var $nav = $('.navbar');
    var nav_top = $nav.offset().top;

    $window.scroll(function() {
        if ($window.scrollTop() > nav_top)
        {
            if(!$nav.hasClass('fixed'))
            {
                $nav.addClass('fixed');
            }
        }
        else
        {
            if($nav.hasClass('fixed'))
            {
                $nav.removeClass('fixed');
            }
        }
    });
}

function show_section()
{
    var $menu_sections = $('td.menu div.menu');

    $('a.nav_item').click(function(elem){
        var section_id = $(this).attr('href').substr(1);

        if (section_id == 0)
        {
            $menu_sections.show();
        }
        else
        {
            $menu_sections
                .hide()
                .each(function() {
                    var $this = $(this);
                    if ($this.hasClass(section_id))
                    {
                        $this.show();
                    }
                })
            ;
        }

        elem.preventDefault();
    });
}
})();

var js_del = (function() {
function init()
{
    $('div.group')
        .mouseover(function() {
            $(this).addClass('zover');
            //$(this).css('cursor', 'pointer');
        })
        .mouseout(function() {
            $(this).removeClass('zover');
            //$(this).css('cursor', 'auto');
        })
    ;

    $('a.button').button();

    stickyNavBar();
    scrollToLink();
}

function forkit(elem, link)
{
    var objPanel =  $(elem).parents('div.g_panel');

    $.getJSON(link)
        .success(function(data){forkit_success(objPanel, data)})
        .error(function(){alert('Failed to fork it!');})
    ;
}

function forkit_success(objThis, data)
{
    if (data.status === 'error')
    {
        alert(data.msg);
    }
    else if ((data.status === 'forkit') || (data.status === 'unforkit'))

    {
        objThis
            .find('div.forkit')
                .toggle();
        ;
    }
    else
    {
        alert('Unexpected result...');
    }
}

var $nav_pos;
var $nav_height;
var $sticky_nav;
function stickyNavBar()
{
    var navbar = $('div.navbar');
    var navbar_top = navbar.offset().top;
    $nav_pos = navbar_top;

    if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent))
    {
        $nav_height = navbar.height();
    }
    else
    {
        $nav_height = navbar.outerHeight(true);
    }

    $sticky_nav = $('div.stickynavbar');

    var $window = $(window);

    $window.on('scroll', function() {
        if ($window.scrollTop() > navbar_top) {
            $sticky_nav
                .outerWidth(navbar.outerWidth())
                .outerHeight(navbar.outerHeight())
                .show()
            ;
        } else {
            $sticky_nav.hide();
        }
    });
}

function scrollToLink()
{
    // IE - html
    // other - body
    var body = $('html,body');

    $('a[href^=#]').click(function(elem){
        var name = $(this).attr('href').substr(1);
        var pos = $('a[name='+name+']').offset();

        var pos_top = pos.top;

        if (pos_top > $nav_pos)
            pos_top -= $nav_height;

        //body.animate({scrollTop: pos.top});
        body.scrollTop(pos_top);
        elem.preventDefault();
    });
}

});
