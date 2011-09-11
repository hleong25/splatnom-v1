var js_menu = {

onDocReady: function()
{
    // empty
},

purgeMenu: function(purgeUrl)
{
    var bPurge = confirm('Are you sure you want to purge this menu?');

    if (!bPurge)
    {
        return false;
    }

    location.href = purgeUrl;
}

}
