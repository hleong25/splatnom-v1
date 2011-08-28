var js_admin = {

onDocReady: function ()
{
    // empty
},

remove_row: function(id, row_id)
{
    $.post(
        '/ws/purge_pending_menu',
        {'id': id},
        function(json)
        {
            if (json.status)
            {
                // it's good...
                $('tr#' + row_id).remove();
            }
            else
            {
                // fail...
                var str = 'Failed to purge menu. Error: ' + json.msg;
                alert(str);
            }
        },
        'json'
    );
        
    return false;
}

};