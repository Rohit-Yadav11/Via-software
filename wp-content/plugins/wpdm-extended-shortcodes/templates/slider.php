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
    <div id="myCarousel" class="carousel slide carousel-fade" data-ride="carousel">
        <!-- Carousel items -->
        <div class="carousel-inner">
            <?php
            foreach ($ids as $ind => $id) {
                $package = get_post($id);
                ?>
                <div class="<?php if ($ind == 0) echo 'active'; ?> carousel-item">


                    <div class="mask flex-center">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-md-7 col-12 order-md-1 order-2 slider-contents">
                                    <h4><?php  echo $package->post_title; ?></h4>
                                    <p>
                                        <span><i class="fa fa-hdd mr-2"></i><?php echo WPDM()->package->get($id, 'package_size'); ?></span>
                                        <span><i class="far fa-arrow-alt-circle-down ml-3 mr-2"></i><?php echo WPDM()->package->get($id, 'download_count'); ?> Downloads</span>
                                        <span><i class="fas fa-smile-beam ml-3 mr-2"></i><?php echo WPDM()->package->get($id, 'view_count'); ?> Views</span>
                                    </p>
                                    <a class="btn btn-<?php echo isset($params['btnclass']) ? $params['btn-class'] : 'info' ?>" href="<?php echo get_permalink($package); ?>">More Details...</a>
                                </div>
                                <div class="col-md-5 col-12 order-md-2 order-1">
                                    <div class="featured-thumb">
                                        <?php echo wpdm_thumb($package, [400, 400], ['class' => 'mx-auto']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <?php
            }
            ?>
        </div>
        <a class="carousel-control-prev carousel-nav" href="#myCarousel" role="button" data-slide="prev"> <span class="carousel-control-prev-icon" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a>
        <a class="carousel-control-next carousel-nav" href="#myCarousel" role="button" data-slide="next"> <span class="carousel-control-next-icon" aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
    </div>
</div>
<style>
    #myCarousel .carousel-item .mask {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100%;
        background-attachment: fixed;
    }

    #myCarousel h4 {
        font-size: 22px;
        margin-bottom: 15px;
        color: <?php echo isset($params['txtcolor']) ? $params['txtcolor'] : '#ffffff' ?>;
        line-height: 100%;
        letter-spacing: 0.5px;
        font-weight: 900;
    }

    #myCarousel p {
        font-size: 18px;
        margin-bottom: 15px;
        color: <?php echo isset($params['txtcolor']) ? 'rgba('.wpdm_hex2rgb($params['txtcolor']).', 0.75)' : 'rgba(255, 255, 255, 0.75)' ?>;
    }

    #myCarousel .featured-thumb {
        padding: 48px;
    }
    #myCarousel .featured-thumb img{
        border-radius: 24px;
    }
    #myCarousel .slider-contents {
        padding-left: 48px;
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


    #myCarousel .carousel-item {
        height: 100%;
        min-height: 390px;
    }

    #myCarousel {
        position: relative;
        z-index: 1;
        background: linear-gradient(135deg, <?php echo isset($params['bgcolors']) ? $params['bgcolors'] : 'var(--color-primary), rgba(var(--color-info-rgb), 0.6), var(--color-primary)'; ?>);
        background-size: cover;
    }

    .carousel-control-next, .carousel-control-prev {
        height: 40px;
        width: 40px;
        padding: 12px;
        top: 50%;
        bottom: auto;
        transform: translateY(-50%);
        background-color: #f47735;
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

</style>
<script>
    jQuery(function ($) {
        $('#myCarousel').carousel({
            interval: 3000,
        })
    });
</script>
