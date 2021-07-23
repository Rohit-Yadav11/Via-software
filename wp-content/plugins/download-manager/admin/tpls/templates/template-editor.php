<div class="wrap w3eden">
     <div class="panel panel-default" id="wpdm-wrapper-panel">
         <div class="panel-heading">
             <b><i class="fa fa-magic color-purple"></i> &nbsp; <a href="edit.php?post_type=wpdmpro&page=templates&_type=<?php echo wpdm_query_var('_type') ?>"><?php echo __( "Templates" , "download-manager" ); ?></a> </b>
             <div class="pull-right">
                 <a href="edit.php?post_type=wpdmpro&page=templates&_type=page&task=NewTemplate" class="btn btn-sm btn-<?php echo wpdm_query_var('_type')=='page'?'info disabled':'secondary'; ?>"><i class="fa fa-file"></i> <?php echo __( "Create Page Template" , "download-manager" ); ?></a>
                 <a href="edit.php?post_type=wpdmpro&page=templates&_type=link&task=NewTemplate" class="btn btn-sm btn-<?php echo wpdm_query_var('_type')=='link'?'info disabled':'secondary'; ?>"><i class="fa fa-link"></i> <?php echo __( "Create Link Template" , "download-manager" ); ?></a>
                 <?php if(wpdm_query_var('task') === 'NewTemplate' || wpdm_query_var('task') === 'EditTemplate'){ ?>
                 <button type="button" class="btn savetplbtn btn-sm btn-success"><?php echo __( "Save Template", "download-manager" ) ?></button>
                 <?php } ?>
             </div>
             <div style="clear: both"></div>
         </div>
         <ul id="tabs" class="nav nav-tabs nav-wrapper-tabs" style="padding: 60px 10px 0 10px;background: #f5f5f5">
             <li><a href="edit.php?post_type=wpdmpro&page=templates&_type=link" id="basic"><?php echo __( "Link Templates" , "download-manager" ); ?></a></li>
             <li><a href="edit.php?post_type=wpdmpro&page=templates&_type=page" id="basic"><?php echo __( "Page Templates" , "download-manager" ); ?></a></li>
             <li><a href="edit.php?post_type=wpdmpro&page=templates&_type=email" id="basic"><?php _e( "Email Templates" , "download-manager" ); ?></a></li>
             <li class="active"><a href="" id="basic"><?php echo __( "Template Editor" , "download-manager" ); ?></a></li>

         </ul>
         <div class="tab-content" style="padding-top: 15px;">



<div style="padding: 15px;">

<div style="margin-left:10px;float: left;width:66%">
<form action="" method="post" id="__template_form">
    <div class="wpdm-template-editor">

   <?php
    $default['link'] = '[thumb_50x50]  
<br style="clear:both"/>    
<b>[page_link]</b><br/>
<b>[download_count]</b> downloads';

$default['popup'] = '[thumb_400x200]
<fieldset class="pack_stats">
<legend><b>Package Statistics</b></legend>
<table class="table table-bordered">
<tr><td>Total Downloads:</td><td>[download_count]</td></tr>
<tr><td>Stock Limit:</td><td>[quota]</td></tr>
<tr><td>Total Files:</td><td>[file_count]</td></tr>
</table>
</fieldset>
<br style="clear:both"/>

[download_link]';

    $default['page'] = '[thumb_700x400]
<br style="clear:both"/>
[description]
<fieldset class="pack_stats">
<legend><b>Package Statistics</b></legend>
<table class="table table-bordered">
<tr><td>Total Downloads:</td><td>[download_count]</td></tr>
<tr><td>Stock Limit:</td><td>[quota]</td></tr>
<tr><td>Total Files:</td><td>[file_count]</td></tr>
</table>
</fieldset><br>
[download_link]';
    $ttype = isset($_GET['_type'])?esc_attr($_GET['_type']):'link';

    if(wpdm_query_var('clone') !== ''){

        $ltpldir = get_stylesheet_directory().'/download-manager/'.$ttype.'-templates/';
        if(!file_exists($ltpldir) || !file_exists($ltpldir.basename($_GET['clone'])))
            $ltpldir = WPDM_BASE_DIR.'tpls/'.$ttype.'-templates/';

        if(file_exists($ltpldir.basename(wpdm_query_var('clone')))) {
            $template = file_get_contents($ltpldir . wpdm_query_var('clone'));
            $regx = isset($_GET['_type']) && $_GET['_type'] == 'link' ? "/<\!\-\-[\s]*WPDM[\s]+Link[\s]+Template[\s]*:([^\-\->]+)\-\->/" : "/<\!\-\-[\s]*WPDM[\s]+Template[\s]*:([^\-\->]+)\-\->/";
            $type = ucfirst($_GET['_type']);
            $tpl['name'] = "New {$ttype} Template";
            $tpl['content'] = preg_replace($regx, "", $template);
        }
    } else {
        $tpl = WPDM()->packageTemplate->get(wpdm_query_var('tplid','txt'), $ttype);
        $tpl['content'] = isset($tpl['content'])?$tpl['content']:$default[$ttype];
    }

?>


                    <input type="hidden" name="action" value="wpdm_save_template" />
                    <input type="hidden" name="_type" value="<?php echo wpdm_query_var('_type') ?>" />
                    <input type="hidden" name="tplid_old" value="<?php echo wpdm_query_var('tplid') ?>" />
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-muted"><?php echo __( "Template Name" , "download-manager" ); ?>:</label>
                                <input type="text" style="width: 99%" name="tpl[name]" id="__tplname" required="required" placeholder="Enter an tmeplate name" x-moz-errormessage="Enter an tmeplate name" value="<?php echo isset($tpl['name'])?htmlspecialchars($tpl['name']):''; ?>" class="form-control input-lg">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="text-muted"><?php echo __( "Template ID" , "download-manager" ); ?>:</label>
                            <input type="text" style="width: 99%" name="tplid" id="__tplid" required="required" placeholder="Enter an unique ID" x-moz-errormessage="Enter an unique ID" value="<?php echo wpdm_query_var('tplid'); ?>" class="form-control input-lg">
                        </div>
                        </div>
                    </div>
                    <ul class="nav nav-tabs" style="margin-top: 10px; ">
                    <li class="active"><a href="#code" data-toggle="tab"><?php echo __( "Code" , "download-manager" ); ?></a></li>
                    <li><a href="#preview" data-toggle="tab"><?php echo __( "Preview" , "download-manager" ); ?></a></li>
                </ul>
                <div class="tab-content tpleditor">
                    <div class="tab-pane active" id="code">
                        <nav id="navbar-example" class="navbar navbar-default navbar-static" role="navigation">
                            <div class="container-fluid">

                                <div class="collapse navbar-collapse bs-example-js-navbar-collapse">
                                    <ul class="nav navbar-nav">
                                        <li class="dropdown">
                                            <a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Package Info <b class="caret"></b></a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop1">
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[title]">Title</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[description]">Description</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[excerpt_80]">Excerpt</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[page_link]">Page Link</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[page_url]">Page URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[page_url_qr]">QR Code For Page URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[categories]">Categories</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[tags]">Tags</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[preview]">Featured Image</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[author_name]">Author Name</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[avatar_url]">Author Avatar URL</a></li>
                                            </ul>
                                        </li>
                                        <li class="dropdown">
                                            <a href="#" id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown">Package Meta <b class="caret"></b></a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop2">
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[thumb_200x200]">Thumbnail</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[icon]">Icon</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[create_date]">Create Date</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[update_date]">Update Date</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[download_url]">Download URL</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[download_link]">Download Link</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[link_label]">Download Link Label</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[package_size]">Package Size</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[quota]">Stock Limit</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[file_list]">File List</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[file_list_extended]">Extended File List</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[version]">Version</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[audio_player]">Aidio Player</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[audio_player_single]">Aidio Player Single</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[pdf_thumb_800x500]">PDF Thumbnail</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[file_types]">File Types</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#[file_type_icons]">File Type Icons</a></li>
                                            </ul>
                                        </li>
                                        <?php do_action('wpdm_template_editor_menu'); ?>

                                        <li class="dropdown">
                                            <a href="#" id="drop3" role="button" class="dropdown-toggle" data-toggle="dropdown">UI Components <b class="caret"></b></a>
                                            <ul class="dropdown-menu" role="menu" aria-labelledby="drop3">
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#<div class='panel panel-default'>\n<div class='panel-heading'>[title]</div>\n<div class='panel-body'>[excerpt]</div>\n<div class='panel-footer'>[download_link]</div>\n</div>">Panel</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#<div class='card card-default'>\n<div class='card-header'>[title]</div>\n<div class='card-body'>[excerpt_100]</div>\n<div class='card-footer'>[download_link]</div>\n</div>">Card</a></li>
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#<div class='media'>\n<div class='pull-left'>[icon]</div>\n<div class='media-body'><h3>[title]</h3>\n[excerpt_100]<br/>\n[download_link]</div>\n</div>">Media</a></li>
                                            </ul>
                                        </li>
                                    </ul>

                                </div><!-- /.nav-collapse -->
                            </div><!-- /.container-fluid -->
                        </nav> <!-- /navbar-example -->
                        <div class="panel panel-default">
                            <div class="panel-heading" style="background: #f1f1f1;border-bottom: 1px solid #dddddd !important;box-shadow: none !important;"><?php _e("HTML", "download-manager");  ?></div>
                            <textarea spellcheck='false' style="border: 0;box-shadow:none !important;max-width: 100%;min-width: 100%;height:250px;font-family:'Courier', monospace" id="templateeditor" class="form-control" name="tpl[content]"><?php echo stripslashes(htmlspecialchars($tpl['content'])); ?></textarea>
                            <div class="panel-heading" style="background: #f1f1f1;border-bottom: 1px solid #dddddd !important;border-top: 1px solid #dddddd;border-radius: 0;box-shadow: none !important;"><?php _e("CSS", "download-manager");  ?></div>
                            <textarea spellcheck='false' style="border: 0;box-shadow:none !important;max-width: 100%;min-width: 100%;height:250px;font-family:'Courier', monospace" id="templatecss" class="form-control" name="tpl[css]"><?php echo isset($tpl['css'])?stripslashes(htmlspecialchars($tpl['css'])):''; ?></textarea>
                        </div>

                    </div>
                    <div class="tab-pane" id="preview">
                        <i class="fas fa-sun  fa-spin"></i> Loading Preview...
                    </div>
                </div>

                <div id="poststuff" class="postarea">
              <?php //the_editor(stripslashes($tpl['content']),'tpl[content]','content', true, true); ?>
</div>





        <button type="submit" id="savetplbtn" class="btn btn-lg btn-primary"><?php echo __( "Save Template" , "download-manager" ); ?></button>

    </div>
</form>


</div>









<div style="margin-left:10px;float: left;width:30%">
    <div class="panel panel-default">
        <div class="panel-heading"><?php echo __( "Template Variables" , "download-manager" ); ?></div>
                <div  style="height: 550px;overflow-y: auto;">
                   <table id="template_tags" class="table" style="margin-top: -1px">
                   <?php if($ttype=='link'){ ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[popup_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "download link open as popup" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[page_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "download link open as page" , "download-manager" ); ?></td></tr>
                   <?php } ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[page_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "Package details page url" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[title]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show package title" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[categories]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show categories" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[tags]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show tags" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[icon]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show icon if available" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[thumb_WxH]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show preview thumbnail with specified width and height if available,l eg: [thumb_700x400] will show 700px &times; 400px image preview " , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[thumb_url_WxH]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "returns preview thumbnail url with specified width and height if available,l eg: [thumb_url_700x400] will return 700px &times; 400px image preview url" , "download-manager" ); ?></td></tr>
                   <?php if($ttype!='link'){ ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[gallery_WxH]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show additional preview thumbnails in gallery format, each image height and with will be same as specified, eg: [gallery_50x30] will show image gallery of additional previews and each image size will be 50px &timesx40px" , "download-manager" ); ?></td></tr>
                   <!--<tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[slider-previews]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show previews in a slider type gallery" , "download-manager" ); ?></td></tr>                -->
                   <?php } ?>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[excerpt_chars]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show a short description of package from description, eg: [excerpt_200] will show short description with first 200 chars of description" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[description]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "package description" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[download_count]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "download counter" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[download_url]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "download url" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[download_link]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "direct link to download using download link label" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[quota]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "number of downloads to expire download quota" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[file_list]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show list of all files in a package" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[version]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show package version" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[create_date]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show package create date" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[update_date]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "show package update date" , "download-manager" ); ?></td></tr>
                   <tr><td><input type="text" readonly="readonly" class="form-control"  onclick="this.select()" value="[audio_player]" style="font-size:10px;width: 120px;text-align: center;"></td><td>- <?php echo __( "Show mp3 player with your page or link template." , "download-manager" ); ?></td></tr>
                   <?php do_action("wpdm_template_tag_row"); ?>
                   </table>
                </div>

        </div>
</div>

<script>

    jQuery.fn.extend({
        insertAtCaret: function(myValue){
            myValue = myValue.replace(/\\n/ig, "\n");
            return this.each(function(i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                }
                else if (this.selectionStart || this.selectionStart == '0') {
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }
            });
        }
    });

    jQuery(function($){
        $('a[href="#preview"]').on('shown.bs.tab', function (e) {
            //e.target // activated tab
            //e.relatedTarget // previous tab
            $('#preview').html('<i class="fas fa-sun  fa-spin"></i> Loading Preview...');
            $.post(ajaxurl,{action:'template_preview',template:$('#templateeditor').val(),css:$('#templatecss').val()},function(res){
                $('#preview').html(res);
            });


        });

        <?php if(wpdm_query_var('task') !== 'EditTemplate'){ ?>
        $('#__tplname').on('keyup', function () {
            var __tplid = $(this).val().replace(/[^a-z0-9]/gi,'_');
            $('#__tplid').val(__tplid.toLowerCase());
        });
        <?php } ?>

        $('.dropdown-menu a').click(function(e){
            e.preventDefault();
            var tag = $(this).attr('href').replace('#','');
            $('#templateeditor').insertAtCaret(tag);
        });

        $('#template_tags .form-control').on('select', function(){
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
        });
        $('.savetplbtn').on('click', function (e) {
            $('#savetplbtn').trigger('click');
        });
        $('#__template_form').on('submit', function (e) {
            e.preventDefault();
            WPDM.blockUI('#__template_form');
            $('#__template_form').ajaxSubmit({
                url: ajaxurl,
                success: function (res) {
                    WPDM.unblockUI('#__template_form');
                    if(res.success === true)
                        WPDM.notify("<strong><i class='fa fa-check-double'></i> <?php echo __( "Done", "download-manager" ) ?></strong><br/><?php echo __( "Template is saved successfully!", "download-manager" ) ?>", "success", "top-right", 7000);
                    else
                        WPDM.notify("<strong><i class='fa fa-times-circle'></i> <?php echo __( "Error", "download-manager" ) ?></strong><br/><?php echo __( "Template ID is missing!", "download-manager" ) ?>", "error", "top-right", 7000);
                }
            });
        });

    });

</script>



<div style="clear: both"></div>


</div>
</div>
</div>
</div>


<style>
    #template_tags .form-control{
        background: #fafafa;
    }
</style>
