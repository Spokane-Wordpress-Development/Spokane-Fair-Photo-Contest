var ok_to_submit = false;

(function($){

    $(function(){

        $('#sf_submit_entry_add').click(function(e){

            if (!ok_to_submit) {

                e.preventDefault();

                var title = $('#sf_title').val();
                var file = $('#sf_file').val();
                var parts = file.split('.');

                if (title.length == 0) {
                    alert('Please enter a title');
                } else if (file.length == 0) {
                    alert('Please choose a file to upload');
                } else if (parts.length == 1) {
                    alert('Please choose file ending in .jpg')
                } else if (parts[parts.length - 1].toUpperCase() !== 'JPG') {
                    alert('Please choose file ending in .jpg')
                } else {
                    ok_to_submit = TRUE;
                    $('#sf_submit_well').html('<strong>Please wait while your image is uploaded (may take a few moments) ...</strong>');
                    $(this).trigger('click');
                }
            }
        });

        $('#sf_submit_entry_edit').click(function(e){

            if (!ok_to_submit) {

                e.preventDefault();

                var title = $('#sf_title').val();
                var file = $('#sf_file').val();
                var parts = file.split('.');

                if (title.length == 0){
                    alert('Please enter a title');
                } else if (file.length > 0 && parts.length == 1){
                    alert('Please choose file ending in .jpg')
                } else if (file.length > 0 && parts[parts.length-1].toUpperCase() !== 'JPG') {
                    alert('Please choose file ending in .jpg')
                } else {
                    ok_to_submit = TRUE;
                    $(this).trigger('click');
                }
            }
        });

        $('#sf_submit_entry_delete').click(function(e){
           
            e.preventDefault();
            
            var b = confirm('Are you sure you want to delete this entry?');
            if (b) {
                $('#spokane_fair_delete_field').val('1');
                $('#sf_submit_entry_form').submit();
            }
        });

        $('.spokane-fair-image').click(function(e){

            e.preventDefault();
            var src = $(this).data('image');
            tb_show( '', src );
        });

        $('.sf-delete-order').click(function(e){
            e.preventDefault();
            var id = $(this).data('id');
            var b = confirm('If you made a mistake, you are allowed to delete any unpaid order. Would you like to delete this order?');
            if (b) {
                window.location = '?action=delete-order&id=' + id;
            }
        });

    });

})(jQuery);