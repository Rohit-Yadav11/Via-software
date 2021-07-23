<?php
if(!defined("ABSPATH")) die("Shit happens!");
?>
<div class="w3eden">


    <div id="upload" style="margin-top: 10px">
        <div id="plupload-upload-ui" class="hide-if-no-js">
            <div id="drag-drop-area">
                <div class="drag-drop-inside" style="margin-top: 40px">
                    <p class="drag-drop-info"><?php _e('Drop files here'); ?></p>
                    <p>&mdash; <?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?> &mdash;</p>
                    <p class="drag-drop-buttons">
                        <button id="plupload-browse-button" type="button" class="btn wpdm-whatsapp btn-sm" ><i class="fa fa-folder-open"></i> <?php esc_attr_e('Select Files'); ?></button><br/>
                        <small>[ Max: <?php echo get_option('__wpdm_chunk_upload',0) == 1?'No Limit':(int)(wp_max_upload_size()/1048576).' MB'; ?> ]</small>
                    </p>
                </div>
            </div>
        </div>

        <?php

        $plupload_init = array(
            'runtimes'            => 'html5,silverlight,flash,html4',
            'browse_button'       => 'plupload-browse-button',
            'container'           => 'plupload-upload-ui',
            'drop_element'        => 'drag-drop-area',
            'file_data_name'      => 'package_file',
            'multiple_queues'     => true,
            'url'                 => admin_url('admin-ajax.php'),
            'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
            'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => '*')),
            'multipart'           => true,
            'urlstream_upload'    => true,

            // additional post data to send to our ajax hook
            'multipart_params'    => array(
                '_ajax_nonce'     => wp_create_nonce(NONCE_KEY),
                'package_id'      => get_the_ID(),
                'type'            => 'file_attachment',
                'action'          => 'wpdm_admin_upload_file',            // the ajax action name
            ),
        );

        if(get_option('__wpdm_chunk_upload',0) == 1){
            $plupload_init['chunk_size'] = get_option('__wpdm_chunk_size', 1024).'kb';
            $plupload_init['max_retries'] = 3;
        } else
            $plupload_init['max_file_size'] = wp_max_upload_size().'b';

        // we should probably not apply this filter, plugins may expect wp's media uploader...
        $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

        <script type="text/javascript">

            function wpdm_html_compile(html, dataset){
                return html.replace(/{{(.*?)}}/g,
                    function (...match) {
                        return dataset[match[1]];
                    });
            }

            function wpdm_attach_file(file)
            {
                var wpdm_file_item_template_html = jQuery('#wpdm-file-item-template').html();
                jQuery('#wpdm-attach-files').prepend(wpdm_html_compile(wpdm_file_item_template_html, file));

                var wpdm_file_info_template_html = jQuery('#wpdm-file-info-template').html();
                jQuery('#wpdm-file-info').prepend(wpdm_html_compile(wpdm_file_info_template_html,file));
            }

            jQuery(document).ready(function($){

                // create the uploader and pass the config from above
                var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                // checks if browser supports drag and drop upload, makes some css adjustments if necessary
                uploader.bind('Init', function(up){
                    var uploaddiv = jQuery('#plupload-upload-ui');

                    if(up.features.dragdrop){
                        uploaddiv.addClass('drag-drop');
                        jQuery('#drag-drop-area')
                            .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                            .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                    }else{
                        uploaddiv.removeClass('drag-drop');
                        jQuery('#drag-drop-area').unbind('.wp-uploader');
                    }
                });

                uploader.init();

                // a file was added in the queue
                uploader.bind('FilesAdded', function(up, files){
                    //var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);



                    plupload.each(files, function(file){
                        jQuery('#filelist').append(
                            '<div class="file" id="' + file.id + '"><b>' +

                            file.name.replace(/</ig, "&lt;") + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') ' +
                            '<div class="progress progress-success progress-striped active"><div class="bar fileprogress"></div></div></div>');
                    });

                    up.refresh();
                    up.start();
                });

                uploader.bind('UploadProgress', function(up, file) {

                    jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                    jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
                });


                // a file was uploaded
                uploader.bind('FileUploaded', function(up, file, response) {

                    // this is your ajax response, update the DOM with it or something...
                    //console.log(file);
                    //response
                    jQuery('#' + file.id ).remove();
                    var d = new Date();
                    var ID = d.getTime();
                    response = response.response;
                    console.log(response);
                    if(parseInt(response) === -3) {
                        WPDM.notify("<strong><?=__('Upload Error:', 'download-manager'); ?></strong><br/><?=__('File type is blocked for security reason!', 'download-manager'); ?>", "danger", "#upload");
                        return false;
                    }
                    response = response.split("|||");
                    response = response[1];
                    var nm = response;

                    var ext = response.split('.');
                    ext = ext[ext.length-1];
                    var icon = "<?php echo WPDM_BASE_URL; ?>file-type-icons/"+ext+".png";
                    var _file = {};
                    _file.filetitle = file.name;
                    _file.filepath = response;
                    _file.fileindex = ID;
                    _file.preview = icon;
                    wpdm_attach_file(_file);



                });

            });

        </script>
        <div id="filelist"></div>

        <div class="clear"></div>
    </div>


</div>
