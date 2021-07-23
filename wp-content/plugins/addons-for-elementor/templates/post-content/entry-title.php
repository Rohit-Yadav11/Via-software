<?php


use Elementor\Utils;

$title = the_title('<' . Utils::validate_html_tag($title_tag) . ' class="lae-post-title"><a href="' . get_permalink() . '" title="' . get_the_title() . '"
                                               rel="bookmark"' . $target . '>', '</a></' . Utils::validate_html_tag($title_tag) . '>', false);

/* If there's no post title, return a default title */
if (empty($title))
    $title = '<' . Utils::validate_html_tag($title_tag) . ' class="lae-post-title lae-no-entry-title"><a href="' . get_permalink() . '" rel="bookmark"' . $target . '>' . esc_html__('(Untitled)',
            'livemesh-el-addons') . '</a></' . Utils::validate_html_tag($title_tag) . '>';

echo wp_kses_post($title);