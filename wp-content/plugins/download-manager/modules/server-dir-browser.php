<?php
function wpdm_odir_tree(){
    if(!isset($_GET['task'])||$_GET['task']!='wpdm_odir_tree') return;
    if(!current_user_can('access_server_browser')) { echo "<ul><li>".__( "Not Allowed!" , "download-manager" )."</li></ul>"; die(); }
    $_POST['dir'] = isset($_POST['dir'])?urldecode($_POST['dir']):'';
    $root = current_user_can('manage_options') ? get_option('_wpdm_file_browser_root', ABSPATH) : UPLOAD_DIR;
    $path = rtrim($root . $_POST['dir'], '/');
    $path = realpath($path).'/';
    if(!strstr("_".$path, $root)) die("Invalid Path" );
    if( file_exists($path) ) {
        $files = scandir($path);
        natcasesort($files);
        if( count($files) > 2 ) { /* The 2 accounts for . and .. */
            echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
            // All dirs
            foreach( $files as $file ) {
                if( file_exists($path . $file) && $file != '.' && $file != '..' && is_dir($path . $file) ) {
                    $rel_path = str_replace($root, "", $path.$file);
                    echo "<li class=\"directory collapsed\"><a onclick=\"odirpath(this)\" class=\"odir\" href=\"#\" rel=\"" . htmlentities($rel_path) . "/\">" . htmlentities($file) . "</a></li>";
                }
            }
            echo "</ul>";
        }
    }
    die();
}

function wpdm_odir_grid(){
    if(!isset($_GET['task'])||$_GET['task']!='wpdm_odir_grid') return;
    if(!current_user_can('access_server_browser')) { echo "<ul><li>".__( "Not Allowed!" , "download-manager" )."</li></ul>"; die(); }
    $root = \WPDM\AssetManager::root();
    $current_dir = urldecode(wpdm_query_var('dir'));
    if(substr_count($current_dir, "../") > 0 ) {
        $dir_grid = "<div class='panel panel-danger'><div class='panel-heading'>".__( "Error", "download-manager" )."</div><div class='panel-body'>".__( "Invalid directory path!", "download-manager" )."</div></div>";
        $breadcrumb = "";
        wp_send_json(array('dir_grid' => $dir_grid, 'breadcrumb' => $breadcrumb));
        die();
    }
    $current_path = $root . trim($current_dir, '/').'/';
    ob_start();
    if( file_exists($current_path) ) {
	    $files = scandir($current_path);
	    natcasesort($files);
	    if( count($files) > 2 ) { /* The 2 accounts for . and .. */
		    // All dirs
		    foreach( $files as $file ) {
			    if( $file != '.' && $file != '..' && is_dir($current_path . $file) ) {
                    $abs_dir_path = $current_path.$file;
                    $rel_path = str_replace($root, "", $abs_dir_path);
				    echo "<div class=\"col-md-4\"><a class='dir txtellipsis' onclick=\"explore('". $current_dir .'/'. $file ."', this)\" class=\"odir\" href=\"#\" rel=\"" . \WPDM\libs\Crypt::encrypt($abs_dir_path) . "\"><i class=\"fa fa-folder-open\"></i> &nbsp;" . htmlentities($file) . "</a></div>";
			    }
		    }
	    }
    }
    $dir_grid = ob_get_clean();

    $dirp = explode("/", $current_dir);
    $breadcrumb = "";
    $proot = "";
    foreach ($dirp as $dir){
        $name = $dir == '' ? __( "Home", "download-manager" ) : $dir;
        $abs_path_current = realpath("{$root}/{$proot}/{$dir}");
        $breadcrumb .= "<li><a href='#' onclick=\"return explore('{$proot}/{$dir}')\" rel='".\WPDM\libs\Crypt::encrypt($abs_path_current)."'>".$name."</a></li>";
        if($dir !== '')
            $proot = $proot.'/'.$dir;
    }

    wp_send_json(array('dir_grid' => $dir_grid, 'breadcrumb' => $breadcrumb));

}

function wpdm_dir_browser(){
    if(wpdm_query_var('task') !== 'wpdm_dir_browser') return;
    if(!current_user_can('access_server_browser')) { echo "<ul><li>".__( "Not Allowed!" , "download-manager" )."</li></ul>"; die(); }
        $root = \WPDM\AssetManager::root();
        $dirs = \WPDM\libs\FileSystem::subDirs($root);
        ?>

        <style>

            #crumbs {
                text-align: left;
            }
            #crumbs h1 {
                padding: 0 0 30px;
                text-transform: uppercase;
                font-size: .9rem;
                font-weight: 600;
                letter-spacing: .01rem;
                color: #8093A7;
            }
            #crumbs ul {
                list-style: none;
                display: inline-table;
            }
            #crumbs ul li {
                display: inline-block;
                margin: 0;
            }
            #crumbs ul li a {
                display: block;
                float: left;
                height: 35px;
                background: #F3F5FA;
                text-align: center;
                padding: 5px 15px 5px 30px;
                position: relative;
                margin: 0 5px 0 0;
                line-height: 24px;
                font-family: monospace;
                font-size: 12px;
                text-decoration: none;
                color: #8093A7;
            }
            #crumbs ul li a:after {
                content: "";
                border-top: 18px solid transparent;
                border-bottom: 18px solid transparent;
                border-left: 18px solid #F3F5FA;
                position: absolute;
                right: -18px;
                top: 0;
                z-index: 1;
            }
            #crumbs ul li a:before {
                content: "";
                border-top: 18px solid transparent;
                border-bottom: 18px solid transparent;
                border-left: 18px solid #fff;
                position: absolute;
                left: 0;
                top: 0;
            }

            #crumbs ul li:first-child a {
                border-top-left-radius: 3px;
                border-bottom-left-radius: 3px;
            }

            #crumbs ul li:first-child a:before {
                display: none;
            }

            #crumbs ul li:last-child a {
                padding-right: 40px;
                border-top-right-radius: 3px;
                border-bottom-right-radius: 3px;
            }

            #crumbs ul li:last-child a:after {
                display: none;
            }

            #crumbs ul li a:hover {
                background: var(--color-primary);
                color: #fff;
                transition: none;
            }

            #crumbs ul li a:hover:after {
                border-left-color: var(--color-primary);
                color: #fff;
            }


            .dirs .col-md-4{

         }

         .dirs .dir{
         border-radius: 3px;
         margin-bottom: 15px;
         margin-top: 15px;
         display: block;
             font-size: 11px;
             text-decoration: none;
             color: #444;
             font-family: 'Courier', monospace;
         }
            #routs{
                margin: 0;
            }
         #dpath, #routs, #routs a{ font-family: 'Courier', monospace; }
        .dirs .fa{
            color: #00A99D;
        }
         .dirs .dir:hover{ text-decoration: none; }
            .dirs .col-md-4{
                border: 1px solid #eee;
                margin-left: -1px;
                margin-top: -1px;
            }
            #TB_ajaxContent{ min-width: calc(100% - 30px); height:100% !important; }
</style>
        <div class='w3eden'>
        <div class='row dirs'>
        <div class="col-md-12 selc">
            <input type="hidden" id="dpath" />
            <div class="input-group"><input placeholder="<?php echo __( "Select a directory", "download-manager" ); ?>" readonly="readonly" type="text" id="dpatho" class="form-control" style="border: 1px solid var(--color-primary) !important; background: #fff;margin-right: 5px !important;border-radius: 3px !important;">
                <div class="input-group-btn" style="margin: 0 !important">
                    &nbsp; <button  style="border-radius: 3px !important;" type="button" id="slctdir" class="btn btn-primary"><i class="fa fa-plus-circle" style="color: #fff"></i> <?php echo __( "Attach Dir", "download-manager" ) ?></button>
                </div>
            </div>
            <br/>


            <div id="crumbs">
                <ul id="routs">
                    <li><a href="#" onclick="return explore('')"><?php echo __( "Home", "download-manager" ) ?></a></li>
                </ul>
            </div>

        </div>
        </div>
        <div class="dirs" style="padding: 15px">
        <div class="row" id="dirs-body">
        <?php
        foreach ($dirs as $dir) {
            $dirname = trim($dir, '/');
            $dirname = explode('/', $dirname);
            $dirname = end($dirname);
            $current_dir = str_replace($root, "/", $dir);
            $current_dir = "/".ltrim($current_dir, "/");
            ?>

            <div class="col-md-4">
            <a class="dir txtellipsis" href="#" onclick="return explore('<?php echo $current_dir; ?>', this)" rel="<?php echo \WPDM\libs\Crypt::encrypt($dir); ?>">
                <i class="fa fa-folder-open"></i> &nbsp;<?php echo $dirname; ?>
            </a>
            </div>

            <?php
        } ?>
    </div></div></div>
    <script>

            function explore(dir, a) {
                jQuery(a).find('.fa-folder-open').removeClass('fa-folder-open').addClass('fa-spin fa-spinner');
                dir = dir.replace(/\/+$/,'');
                if(jQuery(a).attr('rel') !== undefined) {
                    jQuery('#dpath').val(jQuery(a).attr('rel'));
                    jQuery('#dpatho').val(dir);
                }
                jQuery.get('admin.php?task=wpdm_odir_grid&dir='+dir, function (response) {
                    jQuery('#dirs-body').html(response.dir_grid);
                    jQuery('#routs').html("<ul>"+response.breadcrumb+"</ul>");
                });

                return false;
            }

            jQuery('#slctdir').click(function(){
                jQuery('#srvdir').val(jQuery('#dpath').val());
                jQuery('#ddname').html(jQuery('#dpatho').val());
                jQuery($modal_id).modal('hide');
            });

    </script>
    <?php
    die();
}

function wpmp_dir_browser_metabox($post){
    $dir = get_post_meta($post->ID,'__wpdm_package_dir', true);
    $rel_path = \WPDM\libs\Crypt::decrypt($dir);
    $rel_path = str_replace(\WPDM\AssetManager::root(), "", $rel_path);
    ?>
    <div class="w3eden" style="padding: 10px">

        <input class="form-control" readonly="readonly" type="hidden" id="srvdir" value="<?php echo $dir; ?>" name="file[package_dir]" />
        <div id="ddname"><?php echo $dir ? "<b>root:</b>//".$rel_path: __( "Select Dir", "download-manager" ); ?></div>
        <a href="admin.php?post_type=wpdmpro&task=wpdm_dir_browser" data-width="800" title="Server Dir Browser" class="wpdm-modal-pop btn btn-block btn-secondary"><i class="fa fa-folder-open"></i> <?php echo __( "Browse Dir", "download-manager" ) ?></a>
    </div>
    <style>
        #ddname{
            position: relative;
            cursor: pointer;
            border-radius: 3px;
            border: 1px solid var(--color-primary);
            color: var(--color-primary);
            background: rgba(var(--color-primary-rgb), 0.06);
            margin-bottom: 10px;
            padding: 10px;
            font-family: monospace;
            font-size: 10px;
            transition: all ease-in-out 400ms;
        }
        #ddname:hover{
            border: 1px solid var(--color-danger);
            color: var(--color-danger);
            background: rgba(var(--color-danger-rgb), 0.06);
            transition: all ease-in-out 400ms;
        }
        #ddname:hover:before{
            content: "CLEAR";
            font-weight: 800;
            position: absolute;
            display: block !important;
            padding: 5px 10px;
            right: 5px;
            top: 5px;
            background: rgba(var(--color-danger-rgb), 0.7);
            color: #ffffff;
            z-index: 999999;
        }
    </style>
    <script>
        jQuery(function($){
            $('#ddname').on('click', function(){
                $('#srvdir').val("");
                $('#ddname').html("<?php _e( "Select Dir", "download-manager" ); ?>");
            });
        });
    </script>
    <?php
}

function wpdm_get_files($dir, $recur = true){
    $dir = rtrim($dir,"/")."/";
    if($dir == '/' || $dir == '') return array();
    if(!is_dir($dir)) return array();
    $tmpfiles = file_exists($dir)?array_diff( scandir( $dir ), Array( ".", ".." ) ):array();
    $files = array();
    foreach($tmpfiles as $file){
        if( is_dir($dir.$file) && $recur == true) $files = array_merge($files,wpdm_get_files($dir.$file, true));
        else
        $files[\WPDM\libs\Crypt::Encrypt($dir.$file)] = $dir.$file;
    }
    return $files;

}

function wpdm_fetch_dir(){
    if(wpdm_query_var('task') !== 'wpdm_fetch_dir' ) return;
    if(!current_user_can('access_server_browser')) return "<ul><li>".__( "Not Allowed!" , "download-manager" )."</li></ul>";
    if($_REQUEST['dir']=='')
    $dir = get_wpdm_meta((int)$_REQUEST['fid'],'package_dir');
    else
    $dir = $_REQUEST['dir'];
    $root = current_user_can('manage_options') ? get_option('_wpdm_file_browser_root', ABSPATH) : UPLOAD_DIR;
    $files = scandir($root.$dir);
    array_shift($files);
    array_shift($files);
    ?>
    <thead>
    <tr>
    <th style="width: 50px;">Action</th>
    <th>Filename</th>
    <th>Title</th>
    <th style="width: 130px;">Password</th>
    </tr>
    </thead>
    <?php
    foreach($files as $file_index=>$file){
        if(!is_dir($dir.$file)){
        ?>
        <tr  class="cfile">
        <td>
        <input class="fa" type="hidden" value="<?php echo $file; ?>" name="files[]">
        <img align="left" style="width: 16px;height: 16px" rel="del" src="<?php echo plugins_url('download-manager/images/delete.svg'); ?>">
        </td>
        <td><?php echo $dir.$file; ?></td>
        <td><input style="width:99%" type="text" name='wpdm_meta[fileinfo][<?php echo $dir.$file; ?>][title]' value="<?php echo $fileinfo[$dir.$file]['title'];?>"></td>
        <td><input size="10" type="text" id="indpass_<?php echo $file_index;?>" name='wpdm_meta[fileinfo][<?php echo $dir.$file; ?>][password]' value="<?php echo $fileinfo[$dir.$file]['password'];?>"> <img style="cursor: pointer;float: right;margin-top: -3px" class="genpass"  title='Generate Password' onclick="return generatepass('indpass_<?php echo $file_index;?>')" src="<?php echo plugins_url('download-manager/images/generate-pass.png'); ?>" alt="" /></td>
        </tr>
        <?php
    }}
    die();
}

function wpdm_sdb_init(){
    wpdm_dir_browser();
    wpdm_odir_grid();
    wpdm_odir_tree();
}

if(is_admin()){

    add_action("admin_init","wpdm_sdb_init");
    //add_action("init","wpdm_odir_grid");
    //add_action("init","wpdm_odir_tree");
    //add_action("init","wpdm_fetch_dir");
    add_action("add_new_file_sidebar","wpmp_dir_browser_metabox");
}
