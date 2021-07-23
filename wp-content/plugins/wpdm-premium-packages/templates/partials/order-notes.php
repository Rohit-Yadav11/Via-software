<?php
/**
 * Order notes Template
 *
 * This template can be overridden by copying it to yourtheme/download-manager/partials/order-notes.php.
 *
 * @version     1.1.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if((int)get_wpdmpp_option('disable_order_notes', 0) === 0) {
    ?>
    <div id="all-notes">
        <?php
        $order_notes = maybe_unserialize($order->order_notes);
        if (isset($order_notes['messages'])) {
            foreach ($order_notes['messages'] as $time => $order_note) {
                $copy = array();
                if (isset($order_note['admin'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Admin &nbsp; ';
                if (isset($order_note['seller'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Seller &nbsp; ';
                if (isset($order_note['customer'])) $copy[] = '<input type=checkbox checked=checked disabled=disabled /> Customer &nbsp; ';
                $copy = implode("", $copy);
                ?>

                <div class="card card-default dashboard-panel mt-3">
                    <div class="card-body">
                        <?php echo strip_tags(wpautop(stripslashes_deep($order_note['note'])), "<a><strong><b><img><br>"); ?>
                    </div>
                    <?php if (isset($order_note['file']) && is_array($order_note['file'])) { ?>
                        <div class="card-footer text-right">
                            <?php
                            foreach ($order_note['file'] as $id => $file) {
                                $aid = \WPDM\libs\Crypt::Encrypt($order->order_id . "|||" . $time . "|||" . $file); ?>
                                <a href="<?php echo home_url("/?oid=" . $order->order_id . "&_atcdl=" . $aid); ?>"
                                   style="margin-left: 10px"><i class="fa fa-paperclip"></i> <?php echo $file; ?>
                                </a> &nbsp;
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="card-footer text-right">
                        <small><em><i class="fas fa-pencil-alt"></i><?php echo $order_note['by']; ?>&nbsp;<i
                                        class="fa fa-clock-o"></i><?php echo date(get_option('date_format') . " h:i", $time); ?>
                            </em></small>
                        <div class="pull-left">
                            <small><em><?php if ($copy != '') echo "Copy sent to " . $copy; ?></em></small></div>
                    </div>
                </div>
            <?php }
        }
        ?>
    </div>
    <form method="post" id="post-order-note">
        <input type="hidden" name="execute" value="AddNote"/>
        <input type="hidden" name="order_id" value="<?php echo $order->order_id; ?>"/>
        <div class="card card-default dashboard-panel mt-3">
            <textarea required id="order-note" name="note" class="form-control"
                      style="border: 0;box-shadow: none;min-height: 90px;max-width: 100%;min-width: 100%;padding: 10px"></textarea>

            <div id="wpdm-upload-ui" class="card-footer image-selector-panel">
                <div id="filelist" class="pull-right"></div>
                <div id="wpdm-drag-drop-area">

                    <button id="wpdm-browse-button" style="text-transform: unset;letter-spacing: 1px" type="button"
                            class="btn btn-xs btn-info"><i
                                class="fas fa-file-upload"></i> <?php _e("Select File", "download-manager"); ?></button>
                    <div class="progress" id="wmprogressbar"
                         style="width: 111px;height: 20px !important;border-radius: 2px !important;margin: 0;position: relative;background: #0d406799;display: none;box-shadow: none">
                        <div id="wmprogress" class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"
                             style="width: 0%;line-height: 20px;background-color: #007bff"></div>
                        <div class="fetfont"
                             style="font-size:8px;position: absolute;line-height: 20px;height: 20px;width: 100%;z-index: 999;text-align: center;color: #ffffff;letter-spacing: 1px">
                            UPLOADING... <span id="wmloaded">0</span>%
                        </div>
                    </div>


                    <?php

                    $plupload_init = array(
                        'runtimes' => 'html5,silverlight,flash,html4',
                        'browse_button' => 'wpdm-browse-button',
                        'container' => 'wpdm-upload-ui',
                        'drop_element' => 'wpdm-drag-drop-area',
                        'file_data_name' => 'attach_file',
                        'multiple_queues' => false,
                        'url' => admin_url('admin-ajax.php'),
                        'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),
                        'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                        'filters' => array(array('title' => __('Allowed Files'), 'extensions' => get_option('__wpdm_allowed_file_types', 'png,pdf,jpg,txt'))),
                        'multipart' => true,
                        'urlstream_upload' => true,


                        'multipart_params' => array(
                            '_ajax_nonce' => wp_create_nonce(NONCE_KEY),
                            'action' => 'wpdm_frontend_file_upload',
                            'section' => 'wpdm_order_note',
                        ),
                    );

                    $plupload_init['max_file_size'] = wp_max_upload_size() . 'b';


                    $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

                    <script type="text/javascript">


                        jQuery(function ($) {


                            var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                            uploader.bind('Init', function (up) {
                                var uploaddiv = $('#wpdm-upload-ui');

                                if (up.features.dragdrop) {
                                    uploaddiv.addClass('drag-drop');
                                    $('#drag-drop-area')
                                        .bind('dragover.wp-uploader', function () {
                                            uploaddiv.addClass('drag-over');
                                        })
                                        .bind('dragleave.wp-uploader, drop.wp-uploader', function () {
                                            uploaddiv.removeClass('drag-over');
                                        });

                                } else {
                                    uploaddiv.removeClass('drag-drop');
                                    $('#drag-drop-area').unbind('.wp-uploader');
                                }
                            });

                            uploader.init();

                            uploader.bind('Error', function (uploader, error) {
                                WPDM.bootAlert('Error', error.message, 400);
                                $('#wmprogressbar').hide();
                                $('#wpdm-browse-button').show();
                            });


                            uploader.bind('FilesAdded', function (up, files) {
                                /*var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10); */

                                $('#wpdm-browse-button').hide();
                                $('#wmprogressbar').show();

                                plupload.each(files, function (file) {
                                    $('#wmprogress').css('width', file.percent + "%");
                                    $('#wmloaded').html(file.percent);
                                    jQuery('#filelist').append(
                                        '<div class="file pull-left" id="' + file.id + '"><b>' +
                                        file.name + '</b> (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') </div>');
                                });


                                up.refresh();
                                up.start();
                            });

                            uploader.bind('UploadProgress', function (up, file) {
                                $('#wmprogress').css('width', file.percent + "%");
                                $('#wmloaded').html(file.percent);
                                jQuery('#' + file.id + " .fileprogress").width(file.percent + "%");
                                jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));
                            });


                            uploader.bind('FileUploaded', function (up, file, data) {
                                console.log(data);
                                data = data.response;
                                data = data.split("|||");
                                console.log(data);
                                $('#wmprogressbar').hide();
                                $('#wpdm-browse-button').show();

                                jQuery('#' + file.id).remove();
                                var d = new Date();
                                var ID = d.getTime();
                                var filename = data[1];
                                var fileinfo = "<span id='file_" + ID + "' class='atcf' ><a href='#' rel='#file_" + ID + "' class='del-file text-danger'><i class='fa fa-times'></i></a> &nbsp; <input type='hidden' name='file[]' value='" + filename + "' />" + filename + "</span>";
                                jQuery('#filelist').prepend(fileinfo);


                            });


                        });


                    </script>


                    <div class="clear"></div>

                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6 col-xs-12 text-left">
                        <label class="m-0"><?php _e('Send to:', 'wpdm-premium-packages'); ?></label>&nbsp;
                        <label class="m-0"><input type="checkbox" name="admin"
                                      value="1"> <?php _e('Site Admin', 'wpdm-premium-packages'); ?></label>&nbsp;
                        <label class="m-0"><input type="checkbox" name="seller"
                                      value="1"> <?php _e('Seller', 'wpdm-premium-packages'); ?></label>&nbsp;
                        <label class="m-0"><input type="checkbox" name="customer"
                                      value="1"> <?php _e('Customer', 'wpdm-premium-packages'); ?></label>
                    </div>
                    <div class="col-md-6 col-xs-12 text-right">
                        <button class="btn btn-primary btn-sm" id="add-note-button" type="submit">
                            <i class="fa fa-plus-circle"></i> <?php _e('Add Note', 'wpdm-premium-packages'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        jQuery(function ($) {
            $('#post-order-note').submit(function () {
                $('#add-note-button').html('<i class="fa fa-spinner fa-spin"></i> <?php _e('Adding...', 'wpdm-premium-packages'); ?>');
                $(this).ajaxSubmit({
                    url: '<?php echo admin_url('/admin-ajax.php?action=wpdmpp_async_request'); ?>',
                    success: function (res) {
                        $('#add-note-button').html('<i class="fa fa-plus-circle"></i> <?php _e('Add Note', 'wpdm-premium-packages'); ?>');
                        $('#filelist').html("");
                        if (res !== 'error') {
                            $('#all-notes').append(res);
                            $("#order-note").val("");
                        } else
                            alert('Error!');
                    }
                });
                return false;
            });
        });
    </script>
    <?php
}