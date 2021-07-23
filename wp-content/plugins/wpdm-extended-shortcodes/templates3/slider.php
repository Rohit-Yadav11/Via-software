<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 31/5/20 03:53
 */
if(!defined("ABSPATH")) die();
?>
<div class="w3eden">
    <div id="myCarousel" class="carousel slide">
        <!-- Carousel items -->
        <div class="carousel-inner">
            <?php
            foreach ($ids as $inx => $id) {
                $package = get_post($id);
                ?>
                <div class="<?php if ($inx == 0) echo 'active'; ?> item">
                    <?php wpdm_thumb($package, array(400, 400), true,['class' => 'cars-img']); ?>

                    <div class="carousel-caption">
                        <div class="media">
                            <div class="media-body">
                                <h4><?php echo $package->post_title; ?></h4>

                                <p>
                                    <i class="fa fa-hdd mr-2"></i><?php echo WPDM()->package->get($id, 'package_size'); ?>
                                    <i class="far fa-arrow-alt-circle-down ml-3 mr-2"></i><?php echo WPDM()->package->get($id, 'download_count'); ?> Downloads
                                    <i class="fas fa-smile-beam ml-3 mr-2"></i><?php echo WPDM()->package->get($id, 'view_count'); ?> Views
                                </p>
                                <a class="btn btn-<?php echo isset($params['btnclass']) ? $params['btn-class'] : 'info' ?>" href="<?php echo get_permalink($package); ?>">More Details...</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <a class="carousel-control left" href="#myCarousel" data-slide="prev"><span
                class="fa fa-chevron-left"></span></a>
        <a class="carousel-control right" href="#myCarousel" data-slide="next"><span
                class="fa fa-chevron-right"></span></a>
    </div>
</div>
<style>

    #myCarousel h4 {
        font-size: 22px;
        margin-bottom: 15px;
        color: <?php echo isset($params['txtcolor']) ? $params['txtcolor'] : '#ffffff' ?> !important;
        line-height: 100%;
        letter-spacing: 0.5px;
        text-shadow: none;
        font-weight: 900;
    }

    #myCarousel p {
        font-size: 18px;
        margin-bottom: 15px;
        text-shadow: none;
        color: <?php echo isset($params['txtcolor']) ? 'rgba('.wpdm_hex2rgb($params['txtcolor']).', 0.75)' : 'rgba(255, 255, 255, 0.75)' ?>;
    }

    #myCarousel .featured-thumb {
        padding: 48px;
    }
    #myCarousel .cars-img{
        padding: 48px;
        border-radius: 72px;
        right: 0 !important;
        position: absolute;
        top: 0 !important;
        display: block;
        height: 100%;
    }

    #myCarousel .slider-contents {
        padding-left: 48px;
    }

    #myCarousel .featured-thumb img {
        max-width: 100%;
        border-radius: 24px;
    }

    #myCarousel .carousel-nav{
        top: 50%;
        width: 64px;
        height: 64px;
        padding: 0;
        line-height: 64px;
        background: rgba(0,0,0,0.3);
        text-align: center;
        display: none;
    }
    #myCarousel:hover .carousel-nav{
        display: block;
    }

    #myCarousel .carousel-item h4 {
        -webkit-animation-name: fadeInLeft;
        animation-name: fadeInLeft;
    }

    #myCarousel .carousel-item p {
        -webkit-animation-name: slideInRight;
        animation-name: slideInRight;
    }

    #myCarousel .carousel-item a.btn{
        padding: 12px 24px !important;
    }
    #myCarousel .carousel-item a {
        -webkit-animation-name: fadeInUp;
        animation-name: fadeInUp;
    }

    #myCarousel .carousel-item .mask img {
        -webkit-animation-name: slideInRight;
        animation-name: slideInRight;
        display: block;
        height: auto;
        max-width: 100%;
    }

    #myCarousel h4, #myCarousel p, #myCarousel a, #myCarousel .carousel-item .mask img {
        -webkit-animation-duration: 1s;
        animation-duration: 1.2s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    #myCarousel .container {
        max-width: 1430px;
    }

    #myCarousel .carousel-item {
        height: 100%;
        min-height: 390px;
    }

    #myCarousel {
        position: relative;
        z-index: 1;
        background: linear-gradient(135deg, <?php echo isset($params['bgcolors']) ? $params['bgcolors'] : 'var(--color-primary), rgba(var(--color-info-rgb), 0.6), var(--color-primary)'; ?>);
        background-size: cover;
        margin-bottom: 30px;
        height: 400px;
    }
    #myCarousel .carousel-inner,
    #myCarousel .item{
        height: 100%;
        width: 100%;
    }

    #myCarousel .carousel-control {
        position: absolute;
        opacity: 0;
        height: 72px;
        width: 72px;
        top: 50%;
        bottom: auto;
        transform: translateY(-50%);
        background-color: rgba(0,0,0,0.1);
        line-height: 72px;
        padding: 0;
    }
    #myCarousel:hover .carousel-control{
        opacity: 1;
    }

    .ml-3{
        margin-left: 15px;
    }
    .mr-2{
        margin-right: 6px;
    }

    .carousel-item {
        position: relative;
        display: none;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        width: 100%;
        transition: -webkit-transform .6s ease;
        transition: transform .6s ease;
        transition: transform .6s ease, -webkit-transform .6s ease;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-perspective: 1000px;
        perspective: 1000px;
    }

    .carousel-fade .carousel-item {
        opacity: 0;
        -webkit-transition-duration: .6s;
        transition-duration: .6s;
        -webkit-transition-property: opacity;
        transition-property: opacity
    }

    .carousel-fade .carousel-item-next.carousel-item-left, .carousel-fade .carousel-item-prev.carousel-item-right, .carousel-fade .carousel-item.active {
        opacity: 1
    }

    .carousel-fade .carousel-item-left.active, .carousel-fade .carousel-item-right.active {
        opacity: 0
    }

    .carousel-fade .carousel-item-left.active, .carousel-fade .carousel-item-next, .carousel-fade .carousel-item-prev, .carousel-fade .carousel-item-prev.active, .carousel-fade .carousel-item.active {
        -webkit-transform: translateX(0);
        -ms-transform: translateX(0);
        transform: translateX(0)
    }

    @supports (transform-style:preserve-3d) {
        .carousel-fade .carousel-item-left.active, .carousel-fade .carousel-item-next, .carousel-fade .carousel-item-prev, .carousel-fade .carousel-item-prev.active, .carousel-fade .carousel-item.active {
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0)
        }
    }

    .carousel-fade .carousel-item-left.active, .carousel-fade .carousel-item-next, .carousel-fade .carousel-item-prev, .carousel-fade .carousel-item-prev.active, .carousel-fade .carousel-item.active {
        -webkit-transform: translate3d(0, 0, 0);
        transform: translate3d(0, 0, 0);
    }


    @-webkit-keyframes fadeInLeft {
        from {
            opacity: 0;
            -webkit-transform: translate3d(-100%, 0, 0);
            transform: translate3d(-100%, 0, 0);
        }

        to {
            opacity: 1;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            -webkit-transform: translate3d(-100%, 0, 0);
            transform: translate3d(-100%, 0, 0);
        }

        to {
            opacity: 1;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    .fadeInLeft {
        -webkit-animation-name: fadeInLeft;
        animation-name: fadeInLeft;
    }

    @-webkit-keyframes fadeInUp {
        from {
            opacity: 0;
            -webkit-transform: translate3d(0, 100%, 0);
            transform: translate3d(0, 100%, 0);
        }

        to {
            opacity: 1;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            -webkit-transform: translate3d(0, 100%, 0);
            transform: translate3d(0, 100%, 0);
        }

        to {
            opacity: 1;
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    .fadeInUp {
        -webkit-animation-name: fadeInUp;
        animation-name: fadeInUp;
    }

    @-webkit-keyframes slideInRight {
        from {
            -webkit-transform: translate3d(100%, 0, 0);
            transform: translate3d(100%, 0, 0);
            visibility: visible;
        }

        to {
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    @keyframes slideInRight {
        from {
            -webkit-transform: translate3d(100%, 0, 0);
            transform: translate3d(100%, 0, 0);
            visibility: visible;
        }

        to {
            -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
        }
    }

    .slideInRight {
        -webkit-animation-name: slideInRight;
        animation-name: slideInRight;
    }
    .carousel-inner {
        border-radius: 6px !important;
    }


    #myCarousel .carousel-caption {
        background: transparent !important;
        border-radius: 0 !important;
        bottom: 0 !important;
        color: #FFFFFF;
        padding-top: 20px;
        position: absolute;
        text-align: left !important;
        width: 45%;
        top: 100px;
        padding-left: 0 !important;
        left: 48px !important;
        z-index: 10;
    }

    .btn-bordered {
        background: transparent !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.7) !important;
        border-radius: 5px !important;
    }
</style>
<script>
    jQuery(function ($) {
        $('.carousel').carousel();
    });
</script>
