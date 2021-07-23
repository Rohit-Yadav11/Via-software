<?php

/**
 * 
 * @return [template1.php => Template 1]
 */
if (!function_exists('get_wpdm_link_templates')) {
    function get_wpdm_link_templates()
    {
        $link_templates = WPDM()->packageTemplate->getTemplates('link');
        array_walk($link_templates, function (&$v, &$k) {
            $k = strrpos($k, ".") ? substr($k, 0, strrpos($k, ".")) : $k;
            $v = $k;
        });
        return $link_templates;
    }
}


/**
 * 
 * @return [slug => name]
 */
if (!function_exists('get_wpdmcategory_terms')) {
    function get_wpdmcategory_terms()
    {
        $wpdmcategory_terms = get_terms(['taxonomoy' => 'wpdmcategory']);
        foreach ($wpdmcategory_terms as $k => $t) {
            $wpdmcategory_terms[$t->slug] = $t->name;
            unset($wpdmcategory_terms[$k]);
        };
        return $wpdmcategory_terms;
    }
}
