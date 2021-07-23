<?php
/**
 * User: shahnuralam
 * Date: 6/25/18
 * Time: 12:26 AM
 */
if (!defined('ABSPATH')) die();
?>

<div class="w3eden wpdm-authors" id="wpdm-authors<?php echo isset($params['sid'])?"-{$params['sid']}":""; ?>">
    <?php if(isset($params['title']) && $params['title'] != '') echo "<h2 class='section-title'>{$params['title']}</h2>"; ?>
    <?php $this->listAuthors($params); ?>
</div>

<style>
    .panel-author{
        margin-bottom: 30px;
    }
    img.img-circle{
        border-radius: 500px !important;
    }
    .author-name{
        margin: 5px 0;
        padding: 0;
        line-height: 30px;
    }
    .author-name a{
        font-size: 12pt;
    }
</style>
<script>

</script>