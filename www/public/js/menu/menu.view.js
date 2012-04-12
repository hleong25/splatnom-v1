var js_menu = (function() {

init();

return {
    forkit: forkit
};

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

})();
