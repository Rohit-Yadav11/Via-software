<?php

namespace WPDM\Module;

class PopoverPreview
{
    function __construct()
    {
        //add_filter("wdm_before_fetch_template", [$this, 'render']);
        add_filter("wpdm_after_prepare_package_data", [$this, 'render'], 10, 2);
        add_filter("wp_footer", [$this, 'footer']);
        add_shortcode("popover_preview", [$this, 'shortcode']);
    }

    function shortcode($params = [])
    {
        ob_start();
        if(!is_array($params) || !isset($params['id'])) return  '';
        $package = get_post($params['id'], ARRAY_A);
        ?>
        <div class='wpdm-popover' id="popover-<?= $package['ID'] ?>">
            <div class='wpdm-hover-card' id='popover-<?= $package['ID'] ?>' style="width: <?= wpdm_valueof($params, 'width', '500px'); ?>">
                <?php echo WPDM()->shortCode->package($params); ?>
            </div>
            <a href='#' data-show-on-hover='#popover-<?= $package['ID'] ?>'><?= wpdm_valueof($params, 'label', $package['post_title']); ?></a>
        </div>
        <?php
        $content = ob_get_clean();
        return $content;
    }

    function render($package, $template_type = 'link')
    {

        ob_start();
        ?>
        <div class='wpdm-popover' style='position: relative;display: block;width: 100%'>
            <div class='card wpdm-hover-card hover-preview' id='popover-<?= $package['ID'] ?>' style="width: 500px;">
                <div class='card-body'>
                    <div class="media">
                        <?php wpdm_thumb($package['ID'], [300, 300], true, ['class' => 'mr-4']); ?>
                        <div class="media-body">
                            <h3><?= $package['post_title'] ?></h3>
                            <div class="packinfo">
                                <div><i class="fa fa-calendar"></i> Updated
                                    on <?= get_the_modified_date(get_option('date_format'), $package['ID']); ?></div>
                                <div><i class="fab fa-dropbox"></i> <?= $package['package_size']; ?></div>
                                <div><i class="far fa-arrow-alt-circle-down"></i> <?= $package['download_count']; ?>
                                    downloads
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <?= $package['download_link']; ?>
                </div>
            </div>
            <strong class="d-block"><a href='#' data-show-on-hover='#popover-<?= $package['ID'] ?>'><?= $package['post_title'] ?></a></strong>
        </div>
        <?php
        $package['popover_preview'] = ob_get_clean();
        return $package;
    }

    function footer()
    {
        ?>
        <style>

            .wpdm-popover {
                transition: all ease-in-out 400ms;
                position: relative;display: inline-block;
            }

            .wpdm-popover .wpdm-hover-card {
                position: absolute;
                left: 0;
                bottom: 50px;
                width: 100%;
                transition: all ease-in-out 400ms;
                margin-bottom: 28px;
                opacity: 0;
                z-index: -999999;
            }

            .wpdm-popover:hover .wpdm-hover-card {
                transition: all ease-in-out 400ms;
                opacity: 1;
                z-index: 999999;
                bottom: 0px;
            }

            .wpdm-popover .wpdm-hover-card.hover-preview img {
                width: 104px;
                border-radius: 3px;
            }

            .wpdm-popover .card .card-footer{
                background: rgba(0,0,0,0.02);
            }

            .packinfo {
                margin-top: 10px;
                font-weight: 400;
                font-size: 14px;
            }
        </style>
        <script>
            jQuery(function ($) {
                $('a[data-show-on-hover]').on('hover', function () {
                    $($(this).data('show-on-hover')).fadeIn();
                });
            });
        </script>
        <?php
    }
}

new PopoverPreview();