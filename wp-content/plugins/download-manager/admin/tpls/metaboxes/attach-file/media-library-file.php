<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="w3eden">
    <button type="button" class="btn btn-primary btn-block" id="attachml"><?php echo __( "Select from media library", "download-manager" ); ?></button>
    <script>


        jQuery(function ($) {
            var file_frame;
            $('body').on('click', '#attachml' , function( event ){
                event.preventDefault();
                if ( file_frame ) {
                    file_frame.open();
                    return;
                }
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: $( this ).data( 'uploader_title' ),
                    button: {
                        text: $( this ).data( 'uploader_button_text' )
                    },
                    multiple: true
                });
                file_frame.on( 'select', function() {
                    var attachments = file_frame.state().get('selection').toJSON();
                    $(attachments).each(function (index, attachment) {

                        var ext = attachment.filename.split('.');
                        ext = ext[ext.length-1];

                        if(ext.length === 1 || ext === attachment.filename || ext.length > 4 || ext === '' ) ext = '_blank';

                        var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/"+ext.toLowerCase()+".png";

                        var _file = {};
                        _file.filetitle = attachment.title;
                        _file.filepath = attachment.url;
                        _file.fileindex = attachment.id;
                        _file.preview = icon;
                        wpdm_attach_file(_file);

                    });

                });
                file_frame.open();
            });
        });
    </script>
</div>
