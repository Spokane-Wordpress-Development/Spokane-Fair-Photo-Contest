(function($){

    $(function(){

        $('#sf-score-import-button').click(function(e){

            e.preventDefault();

            var file_frame;

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Choose an Import File',
                button: {
                    text: 'Next Step'
                },
                multiple: false
            });

            // When an image is selected, run a callback.
            file_frame.on( 'select', function() {

                var selection = file_frame.state().get('selection');

                selection.map( function( attachment ) {

                    //console.log(attachment.attributes.filename);
                    window.location = 'admin.php?page=spokane_fair_submissions&import=' + attachment.attributes.id;

                });
            });

            // Finally, open the modal
            file_frame.open();
        });

        $('.sf-admin-delete-photo').click(function(e){

            e.preventDefault();
            var id = $(this).data('id');
            var photographer = $(this).data('photographer');
            var b = confirm('Are you sure you want to delete this submission?');
            if (b) {
                window.location = '?page=spokane_fair_photographers&action=view&id=' + photographer + '&delete_entry=' + id;
            }
        });

        $('.sf-admin-delete-order').click(function(e){

            e.preventDefault();
            var id = $(this).data('id');
            var photographer = $(this).data('photographer');
            var b = confirm('Are you sure you want to delete this order?');
            if (b) {
                window.location = '?page=spokane_fair_photographers&action=view&id=' + photographer + '&delete_order=' + id;
            }
        });

        $('#spokane-fair-category-bulk').click(function(e){

            e.preventDefault();
            var categories = [];
            var errors = [];

            $('.spokane_fair_category_code').each(function(){

                var id = $(this).data('id');
                var title = $('#title'+id).val();
                var code = $('#code'+id).val();

                if (code.length === 0) {
                    errors.push('Please enter a code for ID #' + id);
                } else if (title.length === 0) {
                    errors.push('Please enter a title for ID #' + id);
                } else {
                    categories.push({
                        id: id,
                        title: title,
                        code: code,
                        is_visible: $('#visible' + id).val()
                    });
                }
            });

            if (errors.length > 0) {
                alert(errors[0]);
            } else {

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'spokane_fair_category_bulk',
                        categories: JSON.stringify(categories)
                    },
                    success: function() {
                        location.href = '?page=spokane_fair_categories';
                    },
                    error: function() {
                        alert('There was an error. Please try again.');
                    }
                });
            }

        });

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

        $('#spokane-fair-entry-update').click(function(e){
            e.preventDefault();
            var id = $('#spokane_fair_entry_id').val();
            var is_finalist = $('#spokane_fair_is_finalist').val();
            var composition_score = $('#spokane_fair_composition_score').val();
            var impact_score = $('#spokane_fair_impact_score').val();
            var technical_score = $('#spokane_fair_technical_score').val();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'spokane_fair_entry_update',
                    id: id,
                    is_finalist: is_finalist,
                    composition_score: composition_score,
                    impact_score: impact_score,
                    technical_score: technical_score
                },
                success: function()
                {
                    location.href = '?page=spokane_fair_submissions';
                },
                error: function()
                {
                    alert('There was an error. Please try again.');
                }
            });
        });

    });

})(jQuery);