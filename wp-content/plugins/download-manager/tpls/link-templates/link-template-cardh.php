<!-- WPDM Link Template: Card: Horizontal -->
<div class="card mb-4">
    <div class="row no-gutters">
        <div class="col-md-5">
            <a href="[page_url]"><?php wpdm_thumb($ID, [500, 500], true, ['class' => 'rounded-left']) ?></a>
        </div>
        <div class="col-md-7">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <h3 class="card-title">[page_link]</h3>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        File Size <span class="badge">[file_size]</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Downloads <span class="badge">[download_count]</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Updated On <span class="badge">[update_date]</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
