// delete article
$(document).ready(
    $('.delete-object').on('click', function() {
        var id = $(this).attr('delete-id');
        if(confirm('Are you sure ?')) {
            $.post('delete_article.php', {
                object_id: id
            }, function(data) {
                location.reload();
            }).fail(function() {
                alert('Unable to delete.');
            });
        }
    })
);