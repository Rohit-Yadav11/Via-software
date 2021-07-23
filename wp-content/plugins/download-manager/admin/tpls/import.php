<div class="wrap w3eden">
    <div class="panel panel-default"  id="wpdm-wrapper-panel">
        <div class="panel-heading">

            <b><i class="fa fa-file-import color-purple"></i> &nbsp; <?php echo __( "Import & Export" , "download-manager" ); ?></b>

        </div>
        <div class="panel-body">
<script type="text/javascript" src="<?php echo plugins_url().'/download-manager/assets/js/jqueryFileTree.js';?>"></script>
<link rel="stylesheet" href="<?php echo plugins_url().'/download-manager/assets/css/jqueryFileTree.css';?>" />
<link rel="stylesheet" href="<?php echo plugins_url('/download-manager/assets/css/chosen.css'); ?>" />
<script language="JavaScript" src="<?php echo plugins_url('/download-manager/assets/js/chosen.jquery.min.js'); ?>"></script>
<style type="text/css">.jqueryFileTree li{line-height: 20px;}</style>
<div style="margin-top: 50px">
<div class="row">
<div class="col-md-3">

    <div class="panel panel-default">
        <div class="panel-heading"><b><i class="fas fa-file-import"></i> <?php echo __( "Import form CSV File" , "download-manager" ); ?></b></div>
    <div class="panel-body">


        <div id="wpdm-upload-ui" class="text-center image-selector-panel">
            <div id="wpdm-drag-drop-area">

                <button id="wpdm-browse-button" style="text-transform: unset;letter-spacing: 1px" type="button" class="btn btn-lg btn-info btn-block"><i class="fas fa-file-csv"></i> <?php _e("Select CSV File", "download-manager");  ?></button>
                <div class="progress" id="wmprogressbar" style="height: 43px !important;border-radius: 3px !important;margin: 0;position: relative;background: #0d406799;display: none;box-shadow: none">
                    <div id="wmprogress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;line-height: 43px;background-color: #007bff"></div>
                    <div class="fetfont" style="font-size:9px;position: absolute;line-height: 43px;height: 43px;width: 100%;z-index: 999;text-align: center;color: #ffffff;font-weight: 800;letter-spacing: 1px">UPLOADING... <span id="wmloaded">0</span>%</div>
                </div>



                <?php

                $plupload_init = array(
                    'runtimes'            => 'html5,silverlight,flash,html4',
                    'browse_button'       => 'wpdm-browse-button',
                    'container'           => 'wpdm-upload-ui',
                    'drop_element'        => 'wpdm-drag-drop-area',
                    'file_data_name'      => 'csv_file',
                    'multiple_queues'     => false,
                    'url'                 => admin_url('admin-ajax.php'),
                    'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
                    'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
                    'filters'             => array(array('title' => __('Allowed Files'), 'extensions' => 'csv')),
                    'multipart'           => true,
                    'urlstream_upload'    => true,

                    // additional post data to send to our ajax hook
                    'multipart_params'    => array(
                        '_csv_nonce' => wp_create_nonce(NONCE_KEY),
                        'action'      => 'wpdm_upload_csv_file',            // the ajax action name
                    ),
                );

                $plupload_init['max_file_size'] = wp_max_upload_size().'b';

                // we should probably not apply this filter, plugins may expect wp's media uploader...
                $plupload_init = apply_filters('plupload_init', $plupload_init); ?>

                <script type="text/javascript">

                    function Import_CSV_File(){
                        var $ = jQuery;
                        $.get(ajaxurl, {action: 'wpdm_import_csv_file', _csvimport_nonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>'}, function (res) {
                            $('#wmprogress').css('width', res.progress+"%");
                            $('#wmloaded').html(res.progress);
                            $('#importedcount').html(res.imported);
                            if(res.continue)
                                Import_CSV_File();
                            else if(res.continue === false) {
                                $('#wmprogressbar .fetfont').html('COMPLETED!');
                                $('#importstat').html("<i class='fa fa-check-double color-green'></i> <?php _e("Import Completed", "download-manager"); ?>");
                            }
                            else
                                $('#wmprogressbar .fetfont').html('ERROR!');
                        });
                    }

                    jQuery(function($){


                        var uploader = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

                        uploader.bind('Init', function(up){
                            var uploaddiv = $('#wpdm-upload-ui');

                            if(up.features.dragdrop){
                                uploaddiv.addClass('drag-drop');
                                $('#drag-drop-area')
                                    .bind('dragover.wp-uploader', function(){ uploaddiv.addClass('drag-over'); })
                                    .bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv.removeClass('drag-over'); });

                            }else{
                                uploaddiv.removeClass('drag-drop');
                                $('#drag-drop-area').unbind('.wp-uploader');
                            }
                        });

                        uploader.init();

                        uploader.bind('Error', function(uploader, error){
                            WPDM.bootAlert('Error', error.message, 400);
                            $('#wmprogressbar').hide();
                            $('#wpdm-browse-button').show();
                        });


                        uploader.bind('FilesAdded', function(up, files){
                            /*var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10); */

                            $('#wpdm-browse-button').hide(); /*attr('disabled', 'disabled'); */
                            $('#wmprogressbar').show();

                            plupload.each(files, function(file){
                                $('#wmprogress').css('width', file.percent+"%");
                                $('#wmloaded').html(file.percent);
                                /*jQuery('#wpdm-browse-button').hide(); //.html('<span id="' + file.id + '"><i class="fas fa-sun fa-spin"></i> Uploading (<span>' + plupload.formatSize(0) + '</span>/' + plupload.formatSize(file.size) + ') </span>');*/
                            });

                            up.refresh();
                            up.start();
                        });

                        uploader.bind('UploadProgress', function(up, file) {
                            /*jQuery('#' + file.id + " span").html(plupload.formatSize(parseInt(file.size * file.percent / 100)));*/
                            $('#wmprogress').css('width', file.percent+"%");
                            $('#wmloaded').html(file.percent);
                        });


                        uploader.bind('FileUploaded', function(up, file, data) {
                            data = JSON.parse(data.response);
                            $('#importstatus').html("<span class='pull-right' id='importstat'><i class='color-green far fa-spin fa-sun'></i> <?php _e("Importing: ", "download-manager"); ?> <span id='importedcount' class='color-green' style='display:inline-block;width: 30px;text-align:right'>0</span></span><span><i class='fa fa-bars color-blue'></i> <?php _e("Packages Found: ", "download-manager"); ?>"+data.entries+ "</span>");

                            $('#wmprogress').css('width', "1%").addClass('progress-bar-success');
                            $('#wmprogressbar .fetfont').html('IMPORTING... <span id="wmloaded">1</span>%');
                            $('#wmloaded').html('1');

                            /* start importing uploaded csv file */
                            Import_CSV_File();

                        });

                        $('#xportfile').on('click', function () {
                            $(this).hide();
                            $('#wxprogressbar').show();
                            $.get(ajaxurl, {action: 'wpdm_export_packages', _csvexport_nonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>', format: $('.xformat:checked').val()}, function (data) {
                                $('#wxstats').html("<span class='pull-right' id='importstat'><i class='color-green far fa-spin fa-sun'></i> <?php _e("Preparing: ", "download-manager"); ?> <span id='exportcount' class='color-green' style='display:inline-block;width: 30px;text-align:right'>"+data.exported+"</span></span><span><i class='fa fa-bars color-blue'></i> <?php _e("Packages Found", "download-manager"); ?>: "+data.entries+ "</span>");
                                $('#wxloaded').html(data.progress);
                                WPDM_Create_Export_File(data.key);
                            })
                        });

                    });

                    function WPDM_Create_Export_File(key) {
                        var $ = jQuery;
                        $.get(ajaxurl, {action: 'wpdm_export_packages', _csvexport_nonce: '<?php echo wp_create_nonce(NONCE_KEY); ?>', _key: key, format: $('.xformat:checked').val()}, function (res) {
                            $('#wxprogress').css('width', res.progress+"%");
                            $('#wxloaded').html(res.progress);
                            $('#exportcount').html(res.exported);
                            if(res.continue)
                                WPDM_Create_Export_File(key);
                            else if(res.continue === false) {
                                $('#wxprogressbar .fetfont').html('COMPLETED!');
                                $('#xportprogress').html('<a class="btn btn-lg btn-success btn-block" href="'+res.exportfile+'"><?php _e('Download', 'download-manager'); ?></a>');
                                $('#wxstats').html("<i class='fa fa-check-double color-green'></i> <?php _e("Export file is ready", "download-manager"); ?>");
                            }
                            else
                                $('#wxprogressbar .fetfont').html('ERROR!');
                        });
                    }

                </script>
                <div id="filelist"></div>

                <div class="clear"></div>

            </div>
        </div>


    </div>
        <div class="panel-footer" id="importstatus">
             Download sample csv file: <a href="<?php echo plugins_url('/download-manager/sample.csv'); ?>">sample.csv</a>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading"><b><i class="fas fa-file-export"></i> <?php echo __( "Export Packages" , "download-manager" ); ?></b></div>
        <div class="panel-body" id="wxstats">
            <label style="margin-right: 10px"><input type="radio" class="xformat" checked="checked" name="format" value="csv"> CSV</label>
            <label style="margin-right: 10px"><input type="radio" class="xformat" name="format" value="json"> JSON</label>
            <label><input type="radio" class="xformat" name="format" value="xml"> XML</label>
        </div>
        <div class="panel-footer" id="xportprogress">
            <button type="button" id="xportfile" class="btn btn-primary btn-lg btn-block"><?php echo __( "Create Export File" , "download-manager" ); ?></button>

            <div class="progress" id="wxprogressbar" style="height: 43px !important;border-radius: 3px !important;margin: 0;position: relative;background: #0d406799;display: none;box-shadow: none">
                <div id="wxprogress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;line-height: 43px;background-color: #007bff"></div>
                <div class="fetfont" style="font-size:12px;position: absolute;line-height: 43px;height: 43px;width: 100%;z-index: 999;text-align: center;color: #ffffff;font-weight: 600;letter-spacing: 1px">Processing... <span id="wxloaded">0</span>%</div>
            </div>

        </div>
    </div>

<div class="panel panel-default">
    <div class="panel-heading"><b><i class="fas fa-folder-open"></i> <?php echo __( "Select Dir" , "download-manager" ); ?></b></div>
    <div class="panel-body">
        <div id="dtree" style="height: 350px;overflow: auto;"></div>
    </div>
    <div id="path" class="panel-footer">
        <form method="post">
            <div class="input-group">
            <input type="text" class="form-control" name="wpdm_importdir" value="<?php echo get_option('wpdm_importdir'); ?>" id="pathd" size="50" />
                <span class="input-group-btn">
            <input type="submit" id="slctdir" value="Browse Files" class="btn btn-secondary">
                    </span>
                </div>
        </form>
    </div>
</div>





</div>
<div class="col-md-9">
    <?php
    /*$package = new XMLReader();
    if (!$package->open('/Applications/MAMP/htdocs/wpdmpro/wp-content/plugins/download-manager/wpdm-export.xml'))
    {
        die("Failed to open 'data.xml'");
    }
    while($package->read())
    {
        $node = $package->expand();
        wpdmprecho($node);
    }
    $package->close();*/
    $wpdmimported = isset($_COOKIE['wpdmimported'])?explode(",", $_COOKIE['wpdmimported']):array();
    ?>
<form action="" method="post">

<div class="panel panel-default">
  <table class="table table-hover">
    <thead>
    <tr>
        <td colspan="6">
            <select name="cats" id="cats" multiple="multiple" style="width:400px;max-width: 40%;" data-placeholder="Assign Categories">
                <?php $terms = get_terms('wpdmcategory','hide_empty=0');
                foreach($terms as $term){
                    echo "<option value='{$term->term_id}'>{$term->name}</option>";
                }
                ?>
            </select>
            <select name="access" id="access" style="width:400px;max-width: 40%;" multiple="multiple" data-placeholder="Allow Access to Role(s)">
                <?php


                ?>

                <option value="guest" selected="selected"> All Visitors</option>
                <?php
                global $wp_roles;
                $roles = array_reverse($wp_roles->role_names);
                foreach( $roles as $role => $name ) {




                    ?>
                    <option value="<?php echo $role; ?>" > <?php echo $name; ?></option>
                <?php } ?>
            </select>

            &nbsp;<input type="button" id="idel" value="Import Selected Files" class="btn btn-primary" ></td>
    </tr>
      <tr>
        <th width="20" class="check-column"><input type="checkbox" class="multicheck"></th>
        <th >File name</th>
        <th >Title</th>
        <th >Description</th>
        <th width=100>Password</th>
        <th width=100>Size</th>

      </tr>
    </thead>
    <tfoot>
      <tr>
        <th width="20" class="check-column"><input type="checkbox" class="multicheck"></th>
        <th >File name</th>
        <th >Title</th>
        <th >Description</th>
        <th  width=100>Password</th>
        <th width=100>Size</th>

      </tr>
    </tfoot>
    <tbody id="the-list" class="list:post">
      <?php
	$k = 0;
    $limit = 50;
    $total = isset($fileinfo)?count($fileinfo):0;
    $p = isset($_GET['paged'])?$_GET['paged']:1;
	$s = ($p-1)*$limit;
    $max = $s+$limit;
    if($max>$total) $max = $total;
	for($index=$s; $index<$max; $index++): $value = $fileinfo[$index]; $tmptitle = ucwords(str_replace(array("-","_",".")," ",$value['name'])); ?>
      <tr for="file-<?php echo $index; ?>" valign="top" class="importfilelist" id="<?php echo $index; ?>">
        <th class=" check-column" style="border-right: 1px solid #dbdbdb;padding-bottom: 0px;">
            <input type="hidden" id="path_<?php echo $index; ?>" name="files[]" value="<?php echo WPDM()->fileSystem->absPath(get_option('wpdm_importdir').$value['name']); ?>">
            <input type="checkbox" rel="<?php echo $index; ?>" id="file-<?php echo $index; ?>" class="checkbox dim" value="<?php echo $value['name'] ?>"></th>
        <td><label for="file-<?php echo $index; ?>"><strong><?php echo $value['name'] ?></strong></label> <?php if(in_array($value['name'],$wpdmimported)) echo '<span style="margin-left:10px;background:#E2FFE5;color:#000;font-size:11px;font-family:\'Courier New\';padding:2px 7px;">imported</span>'; ?></td>
        <td><input size="20" class="form-control input-sm" type="text" id="title<?php echo $index; ?>" name="file[<?php echo $index; ?>][title]" value="<?php echo $value['name']; ?>"></td>
        <td><input size="40" class="form-control input-sm" type="text" id="desc<?php echo $index; ?>" name="file[<?php echo $index; ?>][description]"></td>
        <td><input size="10" class="form-control input-sm" type="text" id="password<?php echo $index; ?>" name="file[<?php echo $index; ?>][password]"></td>
        <td>
		<?php echo number_format(@filesize(WPDM()->fileSystem->absPath(get_option('wpdm_importdir').$value['name']))/(1024*1024),4); ?> MB
		</td>

      </tr>
      <?php

	  $k++;
	  endfor; ?>




    </tbody>
  </table>
    <div class="panel-footer">
        <input type="button" id="idel1" value="Import Selected Files" class="btn btn-primary" >
    </div>
</div>
  <?php

$page_links = paginate_links( array(
    'base' => add_query_arg( 'paged', '%#%' ),
    'format' => '',
    'prev_text' => __('&laquo;'),
    'next_text' => __('&raquo;'),
    'total' => ceil($total/$limit),
    'current' => $p
));


?>

<div id="ajax-response"></div>

<div class="tablenav">

<?php if ( $page_links ) { ?>
<div class="tablenav-pages"><?php $page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
    number_format_i18n( ( $p - 1 ) * $limit + 1 ),
    number_format_i18n( min( $p * $limit, $total ) ),
    number_format_i18n( $total ),
    $page_links
); echo $page_links_text; ?></div>
<?php } ?>


</div>



</form>
</div></div></div>
</div>
</div>
    </div>



<script type="text/javascript">

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }



     function dimport(id,file, abspath){
       var wpdmimported = [];
       jQuery('#'+id).fadeTo('slow', 0.4);
       jQuery.post(ajaxurl,{action:'wpdm_dimport', filepath: abspath,fname:file, title:jQuery('#title'+id).val(),password:jQuery('#password'+id).val(),access:jQuery('#access').val(),description:jQuery('#desc'+id).val(),category:jQuery('#cats').val()},function(res){
          jQuery('#'+id).fadeOut().remove();
          wpdmimported = getCookie('wpdmimported');
          wpdmimported = wpdmimported + "," + file;
          setCookie('wpdmimported',wpdmimported,360);
       })
     }

    function odirpath(a){
        jQuery('#pathd').val(a.rel);
    }


    jQuery(function($) {

        $('select').chosen({});

        $('#dtree').fileTree({
            root: '',
            script: 'admin.php?task=wpdm_odir_tree',
            expandSpeed: 1000,
            folderEvent: 'click',
            collapseSpeed: 1000,
            multiFolder: false
        }, function(file) {
            var sfilename = file.split('/');
            var filename = sfilename[sfilename.length-1];
            $('#serverfiles').append('<li><label><input checked=checked type="checkbox" value="'+file+'" name="imports[]" class="role"> &nbsp; '+filename+'</label></li>');
            tb_remove();
        });

        $('#TB_ajaxContent').css('width','630px').css('height','90%');

        $('#slctdir').click(function(){
            $('#srvdir').val($('#pathd').val());
            tb_remove();
        });

        $('#idel,#idel1').click(function(){
            $('.dim').each(function(){
                if(this.checked)
                    dimport($(this).attr('rel'),$(this).val(), $('#path_'+$(this).attr('rel')).val());
            });
        });

    });


</script>
