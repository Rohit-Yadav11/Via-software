<?php


namespace WPDM\admin\menus;


use WPDM\libs\Crypt;
use WPDM\WordPressDownloadManager;

class Packages
{

    var $sanitize = array(
            'icon' => 'url',
            'version' => 'txt',
            'link_label' => 'txt',
            'package_size' => 'txt',
            'view_count' => 'int',
            'download_count' => 'int',
    );

    function __construct()
    {

        add_action('wp_ajax_wpdm_admin_upload_file', array($this, 'uploadFile'));
        add_action('save_post', array($this, 'savePackage'));

        add_action('manage_wpdmpro_posts_columns', array($this, 'columnsTH'));
        add_action('manage_wpdmpro_posts_custom_column', array($this, 'columnsTD'), 10, 2);

        add_filter( 'request', array($this, 'orderbyDownloads') );
        add_filter( 'manage_edit-wpdmpro_sortable_columns', array($this, 'sortableDownloads') );

        add_filter('post_row_actions', array($this, 'rowActions'), 10, 2);

        add_action('post_submitbox_misc_actions', array($this, 'downloadPeriod'));
        add_action('admin_footer', array($this, 'footerScripts'));

    }

    function savePackage($post)
    {
        if(!current_user_can('edit_post', $post)) return;
        if (get_post_type() != 'wpdmpro' || !isset($_POST['file'])) return;

        wpdm_check_license();

        // Deleted old zipped file
        $zipped = get_post_meta($post, "__wpdm_zipped_file", true);
        if($zipped!='' && file_exists($zipped)) { @unlink($zipped); }

        $cdata = get_post_custom($post);
        $donot_delete_meta = array('__wpdm_favs', '__wpdm_masterkey');
        foreach ($cdata as $k => $v) {
            $tk = str_replace("__wpdm_", "", $k);
            if (!isset($_POST['file'][$tk]) && $tk !== $k && !in_array($k, $donot_delete_meta))
                delete_post_meta($post, $k);

        }

        foreach ($_POST['file'] as $meta_key => $meta_value) {
            $key_name = "__wpdm_" . $meta_key;
            if($meta_key == 'package_dir' && $meta_value != '') {
                $meta_value = file_exists($meta_value) && get_post_meta($post->ID, $key_name, true) === $meta_value ? $meta_value : Crypt::decrypt($meta_value);
                $meta_value = realpath($meta_value);
                $meta_value = Crypt::encrypt($meta_value);
            }
            if($meta_key == 'package_size' && (double)$meta_value == 0) $meta_value = "";
            if($meta_key == 'files'){
                $meta_value = array_unique($meta_value);
                foreach ($meta_value as &$value){
                    $value = wpdm_escs($value);
                }
            } else
                $meta_value = is_array($meta_value)?wpdm_sanitize_array($meta_value):wpdm_escs($meta_value);
            update_post_meta($post, $key_name, $meta_value);
        }

        if(get_post_meta($post, '__wpdm_masterkey', true) == '')
            update_post_meta($post, '__wpdm_masterkey', uniqid());

        if (isset($_POST['reset_key']) && $_POST['reset_key'] == 1)
            update_post_meta($post, '__wpdm_masterkey', uniqid());

        if(isset($_REQUEST['reset_udl'])) delete_post_meta($post, '__wpdmx_user_download_count');
        do_action('wpdm_admin_update_package',$post, $_POST['file']);
    }


    /*
    function uploadFile1(){
        check_ajax_referer('wpdm_admin_upload_file');
        if(!current_user_can('upload_files')) die('-2');

        $ext = explode('.', $_FILES['package_file']['name']);
        $ext = end($ext);
        $ext = strtolower($ext);
        if(in_array($ext, array('php', 'js', 'html', 'py', 'pl', 'htaccess'))) die('-3');

        if(file_exists(UPLOAD_DIR.$_FILES['package_file']['name']) && get_option('__wpdm_overwrrite_file',0)==1){
            @unlink(UPLOAD_DIR.$_FILES['package_file']['name']);
        }
        if(file_exists(UPLOAD_DIR.$_FILES['package_file']['name']))
            $filename = time().'wpdm_'.$_FILES['package_file']['name'];
        else
            $filename = $_FILES['package_file']['name'];

        do_action("wpdm_before_upload_file", $_FILES['package_file']);

        $filename = esc_html($filename);

        if(get_option('__wpdm_sanitize_filename', 0) == 1)
            $filename = sanitize_file_name($filename);

        move_uploaded_file($_FILES['package_file']['tmp_name'],UPLOAD_DIR.$filename);

        $filename = apply_filters("wpdm_after_upload_file", $filename);

        echo "|||".$filename."|||";
        exit;
    }
    */

    function uploadFile(){
        check_ajax_referer(NONCE_KEY);
        if(!current_user_can('upload_files')) die('-2');

        $name = isset($_FILES['package_file']['name']) && !isset($_REQUEST["chunks"])?$_FILES['package_file']['name']:$_REQUEST['name'];

        $ext = explode('.', $name);
        $ext = end($ext);
        $ext = strtolower($ext);

        if(WPDM()->fileSystem->isBlocked($name)) die('-3');

        do_action("wpdm_before_upload_file", $_FILES['package_file']);

        @set_time_limit(0);

        if(!file_exists(UPLOAD_DIR)){
            WordPressDownloadManager::createDir();
        }

        if(file_exists(UPLOAD_DIR.$name) && get_option('__wpdm_overwrrite_file',0)==1){
            @unlink(UPLOAD_DIR.$name);
        }
        if(file_exists(UPLOAD_DIR.$name) && !isset($_REQUEST["chunks"]))
            $filename = time().'wpdm_'.$name;
        else
            $filename = $name;

        //$filename = esc_html($filename);

        if(get_option('__wpdm_sanitize_filename', 0) == 1)
            $filename = sanitize_file_name($filename);

        if(isset($_REQUEST["chunks"])) $this->chunkUploadFile(UPLOAD_DIR.$filename);
        else {
            move_uploaded_file($_FILES['package_file']['tmp_name'], UPLOAD_DIR . $filename);
            do_action("wpdm_after_upload_file", UPLOAD_DIR . $filename);
        }

        //$filename = apply_filters("wpdm_after_upload_file", $filename, UPLOAD_DIR);

        echo "|||".$filename."|||";
        exit;
    }


    function chunkUploadFile($destFilePath){

        if($destFilePath === '') return;
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
        $out = @fopen("{$destFilePath}.part", $chunk == 0 ? "wb" : "ab");

        if ($out) {
            // Read binary input stream and append it to temp file
            $in = @fopen($_FILES['package_file']['tmp_name'], "rb");

            if ($in) {
                while ($buff = fread($in, 4096))
                    fwrite($out, $buff);
            } else
                die('-3');

            @fclose($in);
            @fclose($out);

            @unlink($_FILES['package_file']['tmp_name']);
        } else {
            die('-3'.$destFilePath);
        }

        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off
            rename("{$destFilePath}.part", $destFilePath);
            do_action("wpdm_after_upload_file", $destFilePath);
        }
    }


    function columnsTH($defaults) {
        if(get_post_type()!='wpdmpro') return $defaults;
        $img['image'] = "<span class='wpdm-th-icon ttip' style='font-size: 0.8em'><i  style='font-size: 80%' class='far fa-image'></i></span>";
        wpdm_array_splice_assoc( $defaults, 1, 0, $img );
        $otf['download_count'] = "<span class='wpdm-th-icon ttip' style='font-size: 0.8em'><i  style='font-size: 80%' class='fas fa-arrow-down'></i></span>";
        $otf['wpdmshortcode'] = 'Short-code';
        wpdm_array_splice_assoc( $defaults, 3, 0, $otf );
        return $defaults;
    }


    function columnsTD($column_name, $post_ID) {
        if(get_post_type()!='wpdmpro') return;
        if ($column_name == 'download_count') {

            echo current_user_can(WPDM_ADMIN_CAP) || get_the_author_meta('ID') === get_current_user_id()?(int)get_post_meta($post_ID, '__wpdm_download_count', true):'&mdash;';

        }
        if ($column_name == 'wpdmshortcode') {

            echo "<input readonly=readonly class='wpdm-scode' onclick='this.select();' value=\"[wpdm_package id='$post_ID']\" />";

        }
        if ($column_name == 'image') {
            if(has_post_thumbnail($post_ID))
                echo get_the_post_thumbnail( $post_ID, 'thumbnail', array('class'=>'img60px') );
            else {
                $icon = get_post_meta($post_ID,'__wpdm_icon', true);
                if($icon!=''){
                    $icon = $icon;
                    echo "<img src='$icon' class='img60px' alt='Icon' />";
                }
            }
        }
    }


    function orderbyDownloads( $vars ) {

        if ( isset( $vars['orderby'] ) && 'download_count' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => '__wpdm_download_count',
                'orderby' => 'meta_value_num'
            ) );
        }

        return $vars;
    }

    function sortableDownloads( $columns ) {

        if(get_post_type()!='wpdmpro') return $columns;

        $columns['download_count'] = 'download_count';

        return $columns;
    }


    function rowActions($actions, $post)
    {
        if($post->post_type == 'wpdmpro' && current_user_can(WPDM_ADMIN_CAP)) {
            $actions['view_stats'] = '<a title="' . __( "Stats" , "download-manager" ) . '" href="edit.php?post_type=wpdmpro&page=wpdm-stats&pid=' . $post->ID . '" class="view_stats w3eden"><i class="fas fa-chart-pie color-blue"></i></a>';
            if($post->post_status == 'publish')
                $actions['download_link'] = '<a title="' . __( "Generate Download URL" , "download-manager" ) . '" href="#" class="gdl_action w3eden" data-mdlu="'.\WPDM\Package::getMasterDownloadURL($post->ID).'" data-toggle="modal" data-target="#gdluModal" data-pid="'.$post->ID.'"><i class="far fa-arrow-alt-circle-down color-purple"></i></a>';
            if($post->post_status == 'publish')
                $actions['email_download_link'] = '<a title="' . __( "Email Download Link" , "download-manager" ) . '" href="#" data-toggle="modal" data-target="#edlModal" data-pid="'.$post->ID.'" class="view_stats email_dllink w3eden"><i class="fa fa-paper-plane color-green"></i></a>';
        }
        return $actions;
    }

    function downloadPeriod() {

        if(get_post_type()!='wpdmpro') return;

        $xd = get_post_meta(get_the_ID(),'__wpdm_expire_date',true);
        $pd = get_post_meta(get_the_ID(),'__wpdm_publish_date',true);
        ?>
        <div class="w3eden">
        <div class="panel panel-default no-radius" style="margin: 10px">
            <div class="panel-heading no-radius" style="background-image: none;border-bottom: 1px solid #ddd !important"><?php _e( "Download Availability Period" , "download-manager" ); ?></div>
            <div class="panel-body dl-period">

                <div class="misc-pub-section curtime misc-pub-curtime">
                <span>
                <i class="fa fa-calendar-check-o text-success pull-right"></i><?php _e( "Download Available From:" , "download-manager" ); ?><Br/><input type="text" id="publish_date" autocomplete="off" size="30" value="<?php echo $pd; ?>" name="file[publish_date]" class="form-control input-sm">
                </span></div>
                    <div class="misc-pub-section curtime misc-pub-curtime">
                <span>
                <i class="fa fa-calendar-times-o text-danger pull-right"></i><?php _e( "Download Expire on:" , "download-manager" ); ?><br/><input type="text" id="expire_date" autocomplete="off" size="30" value="<?php echo $xd; ?>" name="file[expire_date]"  class="form-control input-sm">
                </span></div>
                </div>

            </div>
        </div>
        <script>
            jQuery(function(){
                jQuery('#expire_date,#publish_date').datetimepicker({dateFormat:"yy-mm-dd", timeFormat: "HH:mm"});
            });
        </script>
        <?php
    }

    function footerScripts(){
        global $pagenow;
        if(wpdm_query_var('post_type') === 'wpdmpro' && $pagenow === 'edit.php') {
            ?>

            <style>
                .w3eden #edlModal .modal-content,
                .w3eden #gdluModal .modal-content{
                    padding: 20px;
                    border-radius: 15px;
                }
                .w3eden #edlModal .modal-content .modal-header i,
                .w3eden #gdluModal .modal-content .modal-header i{
                    margin-right: 6px;
                }
                .w3eden #gdluModal .modal-content .modal-footer,
                .w3eden #gdluModal .modal-content .modal-header,
                .w3eden #edlModal .modal-content .modal-footer,
                .w3eden #edlModal .modal-content .modal-header{
                    border: 0;
                }
            </style>

            <div class="w3eden">
                <div class="modal fade" tabindex="-1" role="dialog" id="edlModal" style="display: none">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title"><i class="fa fa-paper-plane color-green"></i> <?php _e("Email Download Link", "download-manager"); ?></h4>
                            </div>
                            <div class="modal-body">
                                <form method="post" id="edlfrm">
                                    <input type="hidden" name="action" value="wpdm_email_package_link"/>
                                    <?php wp_nonce_field(NONCE_KEY, '__edlnonce'); ?>
                                    <input type="hidden" name="emldllink[pid]" id="edlpid" value=""/>
                                    <div class="form-group" id="edlemail_fg">
                                        <label><?php _e("Emails:", "download-manager"); ?><span
                                                    class="color-red">*</span> </label>
                                        <input type="text" required="required" class="form-control" id="edlemail"
                                               name="emldllink[email]"
                                               placeholder="<?php _e("Multiple emails separated by comma(,)", "download-manager"); ?>"/>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-5">
                                            <label>Usage Limit:</label>
                                            <div class="input-group">
                                                <input min="1" class="form-control" type="number"
                                                       name="emldllink[usage]" value="3">
                                                <div class="input-group-addon input-group-append">times</div>
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <label>Expire After:</label>
                                            <div class="row">
                                                <div class="col-md-6" style="padding-right: 0">
                                                <input min="1" step="1" class="form-control"
                                                       name="emldllink[expire]" type="number" value="60">
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="emldllink[expire_multiply]" class="form-control wpdm-custom-select" style="min-width: 100%;max-width: 100%">
                                                        <option value="60">Mins</option>
                                                        <option value="3600">Hours</option>
                                                        <option value="86400">Days</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label><?php _e("Subject:", "download-manager"); ?></label>
                                        <input type="text" class="form-control" id="edlsubject"
                                               name="emldllink[subject]"/>
                                    </div>

                                    <div class="form-group">
                                        <label><?php _e("Message:", "download-manager"); ?></label>

                                        <textarea id="edlmsg" name="emldllink[message]" class="form-control"></textarea>

                                    </div>
                                </form>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                        data-dismiss="modal"><?php _e("Close", "download-manager"); ?></button>
                                <button type="button" class="btn btn-primary" id="__wpdmseln"><i
                                            class="fa fa-paper-plane"></i>
                                    &nbsp;<?php _e("Send Now", "download-manager"); ?></button>
                            </div>
                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

                <div class="modal fade" tabindex="-1" role="dialog" id="gdluModal" style="display: none">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title"><i class="far fa-arrow-alt-circle-down color-purple"></i> <?php _e("Generate Download Link", "download-manager"); ?></h4>
                            </div>
                            <div class="modal-body">


                                <div class="panel panel-default">
                                    <div class="panel-heading"><?php _e("Master Download Link:", "download-manager"); ?></div>
                                    <div class="panel-body"><input readonly="readonly" onclick="this.select()"
                                                                   type="text" class="form-control color-purple"
                                                                   style="background: #fdfdfd;font-size: 10px;text-align: center;font-family: monospace;font-weight: bold;"
                                                                   id="mdl"/></div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading">Generate Temporary Download Link</div>
                                    <div class="panel-body">

                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Usage Limit:</label>
                                                <input min="1" class="form-control" id="ulimit" type="number" placeholder="<?php echo __( "Count", "download-manager" ) ?>" value="3">
                                            </div>
                                            <div class="col-md-5">
                                                <label>Expire After:</label>
                                                <div class="input-group">
                                                    <input id="exmisd" min="0.5" step="0.5" class="form-control"
                                                           type="number" value="600" style="width: 50%;display: inline-block;">
                                                    <select id="expire_multiply" class="form-control wpdm-custom-select" style="min-width: 50%;max-width: 50%;display: inline-block;margin-left: -1px">
                                                        <option value="60">Mins</option>
                                                        <option value="3600">Hours</option>
                                                        <option value="86400">Days</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label>&nbsp;</label><br/>
                                                <button id="gdlbtn" class="btn btn-secondary btn-block"
                                                        style="height: 34px" type="button">Generate
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="panel-footer">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?php echo __( "Direct download:", "download-manager" ); ?></div>
                                            <input type="text" id="tmpgdl" value="" class="form-control color-green"
                                                   readonly="readonly" onclick="this.select()"
                                                   style="background: #fdfdfd;font-size: 10px;text-align: center;font-family: monospace;font-weight: bold;"
                                                   placeholder="Click Generate Button">
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="input-group">
                                            <div class="input-group-addon"><?php echo __( "Download page:", "download-manager" ); ?></div>
                                            <input type="text" id="tmpgdlp" value="" class="form-control color-green"
                                                   readonly="readonly" onclick="this.select()"
                                                   style="background: #fdfdfd;font-size: 10px;text-align: center;font-family: monospace;font-weight: bold;"
                                                   placeholder="Click Generate Button">
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

            </div>
            <script>
                jQuery(function ($) {
                    $('.email_dllink').on('click', function () {
                        $('#edlpid').val($(this).attr('data-pid'));
                    });
                    $('body').on('click', '#__wpdmseln', function () {
                        if ($('#edlemail').val() == '') {
                            $('#edlemail_fg').addClass('has-error');
                            return false;
                        } else
                            $('#edlemail_fg').removeClass('has-error');
                        var __bl = $(this).html();
                        $(this).html("<i class='fa fa-sync fa-spin'></i> &nbsp;Sending...").attr('disabled', 'disabled');
                        $.post(ajaxurl, $('#edlfrm').serialize(), function (res) {
                            $('#__wpdmseln').html(__bl).removeAttr('disabled');
                        })
                    });

                    var tdlpid;
                    $('.gdl_action').on('click', function () {
                        tdlpid = $(this).attr('data-pid');
                        $('#mdl').val($(this).attr('data-mdlu'));
                        $('#tmpgdl').val('');
                        $('#tmpgdlp').val('');
                    });

                    $('#gdlbtn').on('click', function () {
                        $('#gdlbtn').html("<i class='fa fa-sync fa-spin'></i>");
                        $.post(ajaxurl, {
                            action: 'generate_tdl',
                            pid: tdlpid,
                            ulimit: $('#ulimit').val(),
                            exmisd: $('#exmisd').val(),
                            expire_multiply: $('#expire_multiply').val(),
                            __tdlnonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>'
                        }, function (res) {
                            $('#tmpgdl').val(res.download_url);
                            $('#tmpgdlp').val(res.download_page);
                            $('#gdlbtn').html("Generate");
                        });
                    });

                });
            </script>

            <?php
        }

        if($pagenow === 'themes.php' || $pagenow === 'theme-install.php'){
            if(!file_exists(ABSPATH.'/wp-content/themes/attire/')) {
                ?>
                <script>
                    jQuery(function ($) {
                        $('.page-title-action').after('<a href="<?php echo admin_url('/theme-install.php?search=attire'); ?>" class="hide-if-no-js page-title-action" style="border: 1px solid #0f9cdd;background: #13aef6;color: #ffffff;">Suggested Theme</a>');
                    });
                </script>
                <?php
            }
        }

    }




}
