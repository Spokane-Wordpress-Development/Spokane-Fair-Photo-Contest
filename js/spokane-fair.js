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
                $('#sf_submit_entry_form').submit();
            }
        });

    });

})(jQuery);