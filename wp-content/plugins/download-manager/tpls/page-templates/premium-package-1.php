<!-- WPDM Template: Premium Package #2 -->
<?php if(!defined("ABSPATH")) die(); ?>
<div class="row">
    <div class="col-md-7">
        <div class="thumbnail">[thumb_800x600]</div>
    </div>
    <div class="col-md-5">
        <ul class="list-group ml-0 mb-2">
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:version]">
                [txt=Version]
                <span class="badge">[version]</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:download_count]">
                [txt=Download]
                <span class="badge">[download_count]</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:file_size]">
                [txt=File Size]
                <span class="badge">[file_size]</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:file_count]">
                [txt=File Count]
                <span class="badge">[file_count]</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center [hide_empty:create_date]">
                [txt=Create Date]
                <span class="badge">[create_date]</span>
            </li>
            <li class="list-group-item  d-flex justify-content-between align-items-center [hide_empty:update_date]">
                [txt=Last Updated]
                <span class="badge">[update_date]</span>
            </li>

        </ul>
        [download_link_extended]
    </div>

<div class="col-md-12">
<br/>

        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="nav-item"><a class="nav-link active" href="#wpdmpp-product-desc" aria-controls="wpdmpp-product-desc" role="tab" data-toggle="tab">[txt=Description]</a></li>

            <li role="presentation" class="nav-item"><a class="nav-link" href="#wpdmpp-product-files" aria-controls="wpdmpp-product-files" role="tab" data-toggle="tab">[txt=Attached Files]</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="padding: 15px 0">
            <div role="tabcard" class="tab-pane active" id="wpdmpp-product-desc">[description]</div>

            <div role="tabcard" class="tab-pane" id="wpdmpp-product-files">[file_list]</div>
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


