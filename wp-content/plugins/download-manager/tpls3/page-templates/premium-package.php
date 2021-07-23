<!-- WPDM Template: Premium Package -->
<?php if(!defined("ABSPATH")) die(); ?>
<div class="row">
    <div class="col-md-9">
        [thumb_900x540]
    </div>
    <div class="col-md-3">
        [download_link_extended]
    </div>

<div class="col-md-12">
<br/>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#wpdmpp-product-desc" aria-controls="wpdmpp-product-desc" role="tab" data-toggle="tab">[txt=Description]</a></li>
            <li role="presentation"><a href="#wpdmpp-product-info" aria-controls="wpdmpp-product-info" role="tab" data-toggle="tab">[txt=Package Info]</a></li>
            <li role="presentation"><a href="#wpdmpp-product-files" aria-controls="wpdmpp-product-files" role="tab" data-toggle="tab">[txt=Attached Files]</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="padding: 15px 0">
            <div role="tabpanel" class="tab-pane active" id="wpdmpp-product-desc">[description]</div>
            <div role="tabpanel" class="tab-pane" id="wpdmpp-product-info">
                <ul class="list-group">
                    <li class="list-group-item [hide_empty:version]">
                        <span class="badge">[version]</span>
                        [txt=Version]
                    </li>
                    <li class="list-group-item [hide_empty:download_count]">
                        <span class="badge">[download_count]</span>
                        [txt=Download]
                    </li>
                    <li class="list-group-item [hide_empty:file_size]">
                        <span class="badge">[file_size]</span>
                        [txt=File Size]
                    </li>
                    <li class="list-group-item [hide_empty:file_count]">
                        <span class="badge">[file_count]</span>
                        [txt=File Count]
                    </li>
                    <li class="list-group-item [hide_empty:create_date]">
                        <span class="badge">[create_date]</span>
                        [txt=Create Date]
                    </li>
                    <li class="list-group-item [hide_empty:update_date]">
                        <span class="badge">[update_date]</span>
                        [txt=Last Updated]
                    </li>

                </ul>
            </div>
            <div role="tabpanel" class="tab-pane" id="wpdmpp-product-files">[file_list]</div>
        </div>


</div>


</div>
<script>
    jQuery(function ($) {
        try {
            $('.nav-tabs').tabs();
        }catch (e){

        }
    });
</script>


