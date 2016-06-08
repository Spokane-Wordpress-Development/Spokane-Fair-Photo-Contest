(function($){

    $(function(){

        $('#sf_submit_entry_add').click(function(e){

            e.preventDefault();

            var title = $('#sf_title').val();
            var file = $('#sf_file').val();
            var parts = file.split('.');

            if (title.length == 0){
                alert('Please enter a title');
            } else if (file.length == 0){
                alert('Please choose a file to upload');
            } else if (parts.length == 1){
                alert('Please choose file ending in .jpg')
            } else if (parts[parts.length-1].toUpperCase() !== 'JPG') {
                alert('Please choose file ending in .jpg')
            } else {
                $('#sf_submit_well').html('<strong>Please wait while your image is uploaded (may take a few moments) ...</strong>')
                $('#sf_submit_entry_form').submit();
            }
        });

        $('#sf_submit_entry_edit').click(function(e){

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
                $('#sf_submit_entry_form').submit();
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

    });

})(jQuery);