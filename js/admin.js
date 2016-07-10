(function($){

    $(function(){

        $('#spokane-fair-category-add').click(function(e){

            e.preventDefault();
            var code = $('#spokane_fair_category_code').val();
            var title = $('#spokane_fair_category_title').val();
            var is_visible = $('#spokane_fair_category_is_visible').val();

            if (code.length == 0) {
                alert('Please enter a code');
            } else if (title.length == 0) {
                alert('Please enter a title');
            } else {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'spokane_fair_category_add',
                        code: code,
                        title: title,
                        is_visible: is_visible
                    },
                    success: function()
                    {
                        location.href = '?page=spokane_fair_categories';
                    },
                    error: function()
                    {
                        alert('There was an error. Please try again.');
                    }
                });
            }
        });

        $('#spokane-fair-category-update').click(function(e){

            e.preventDefault();
            var id = $('#spokane_fair_category_id').val();
            var code = $('#spokane_fair_category_code').val();
            var title = $('#spokane_fair_category_title').val();
            var is_visible = $('#spokane_fair_category_is_visible').val();

            if (code.length == 0) {
                alert('Please enter a code');
            } else if (title.length == 0) {
                alert('Please enter a title');
            } else {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'spokane_fair_category_update',
                        id: id,
                        code: code,
                        title: title,
                        is_visible: is_visible
                    },
                    success: function()
                    {
                        location.href = '?page=spokane_fair_categories';
                    },
                    error: function()
                    {
                        alert('There was an error. Please try again.');
                    }
                });
            }
        });

        $('#spokane-fair-category-delete').click(function(e){

            e.preventDefault();
            var id = $('#spokane_fair_category_id').val();

            var b = confirm('Are you sure you want to delete this category?');
            if (b) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'spokane_fair_category_delete',
                        id: id
                    },
                    success: function()
                    {
                        location.href = '?page=spokane_fair_categories';
                    },
                    error: function()
                    {
                        alert('There was an error. Please try again.');
                    }
                });
            }
        });

        $('#delete-all-fair-entries').find('button').click(function(e){
            e.preventDefault();
            var what = $(this).data('what');
            var which = $(this).data('which');
            var nonce = $(this).data('nonce');
            var b = confirm('Are you sure you want to delete all ' + what + '?');
            if (b) {
                var B = confirm('Are you really sure? This is your last chance to change your mind.');
                if (B) {
                    window.location = 'admin.php?page=spokane_fair_photos&spokane_fair_photos_delete=' + which + '&nonce=' + nonce;
                }
            }
        });

    });

})(jQuery);