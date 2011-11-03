var js_admin = (function() {

return {
    remove_row: remove_row
};

function remove_row(id, row_id)
{
    var confirm_remove = confirm("Are you sure you want to delete id '" + id + "'?");

    if (confirm_remove == false)
        return false;

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

})();
