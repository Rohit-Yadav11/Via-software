<?php
/**
 * Base: wpdmpro
 * Developer: shahjada
 * Team: W3 Eden
 * Date: 30/5/20 13:44
 */
if(!defined("ABSPATH")) die();

get_header();

the_post();
?>
<div class="w3eden">

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="pt-3 pb-3">
                        <?php
                        the_content();
                        ?>
                    </div>
                </div>
            </div>
        </div>

</div>
<?php

get_footer();
