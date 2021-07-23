<?php


namespace WPDM;


class Query
{
    private $post_type = "wpdmpro";
    public $params = [];
    public $result;
    public $packages = [];
    public $count = 0;

    function __construct()
    {

    }

    /**
     * @param int $items_per_page
     */
    function items_per_page($items_per_page = 10)
    {
        $this->params['posts_per_page'] = $items_per_page;
        return $this;
    }

    /**
     * @param string $order_field
     * @param string $order
     */
    function sort($order_field = 'date', $order = 'DESC')
    {

        $order_fields = ['__wpdm_download_count', '__wpdm_view_count', '__wpdm_package_size_b'];
        if (!in_array("__wpdm_" . $order_field, $order_fields)) {
            $this->params['orderby'] = $order_field;
            $this->params['order'] = $order;
        } else {
            $this->params['orderby'] = 'meta_value_num';
            $this->params['meta_key'] = "__wpdm_" . $order_field;
            $this->params['order'] = $order;
        }
        return $this;

    }

    /**
     * @param $taxonomy
     * @param $terms
     * @param string $field
     * @param string $operator
     * @param false $include_children
     */
    function taxonomy($taxonomy, $terms, $field = 'slug', $operator = 'IN', $include_children = false)
    {
        if(!isset($this->params['tax_query']) || !is_array($this->params['tax_query'])) $this->params['tax_query'] = [];
        if(!is_array($terms)) $terms = explode(",", $terms);
        $this->params['tax_query'][] = [
            'taxonomy' => $taxonomy,
            'field' => $field,
            'terms' => $terms,
            'include_children' => $include_children,
            'operator' => $operator
        ];
        return $this;
    }

    /**
     * @param string $relation
     */
    function taxonomy_relation($relation = 'OR') {
        $this->params['tax_query']['relation'] = $relation;
        return $this;
    }

    /**
     * @param null $categories
     * @param string $field
     * @param string $operator
     * @param false $include_children
     */
    function categories($categories = null, $field = 'slug', $operator = 'IN', $include_children = false)
    {
        if($categories)
        {
            $this->taxonomy('wpdmcategory', $categories, $field, $operator, (int)$include_children);
        }
        return $this;
    }

    /**
     * Exclude categories
     * @param null $categories
     * @param string $field
     */
    function xcats($categories = null, $field = 'slug')
    {
        if($categories)
        {
            $this->taxonomy('wpdmcategory', $categories, $field, 'NOT IN');
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     * @param string $operator
     */
    function tags($tags = null, $field = 'slug', $operator = 'IN')
    {
        if($tags)
        {
            $this->taxonomy('wpdmtag', $tags, $field, $operator);
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     */
    function tag__and($tags = null, $field = 'slug')
    {
        if($tags)
        {
            $this->taxonomy('wpdmtag', $tags, $field, 'AND');
        }
        return $this;
    }

    /**
     * @param null $tags
     */
    function tag_slug__and($tags = null)
    {
        if($tags)
        {
            $this->taxonomy('wpdmtag', $tags, 'slug', 'AND');
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     */
    function tag__in($tags = null, $field = 'slug')
    {
        if($tags)
        {
            $this->taxonomy('wpdmtag', $tags, $field, 'IN');
        }
        return $this;
    }

    /**
     * @param null $tags
     */
    function tag_slug__in($tags = null)
    {
        if($tags)
        {
            $this->taxonomy('wpdmtag', $tags, 'slug', 'IN');
        }
        return $this;
    }

    /**
     * @param null $tags
     * @param string $field
     */
    function tag__not_in($tags = null, $field = 'slug')
    {
        if($tags)
        {
            $this->taxonomy('wpdmtag', $tags, $field, 'NOT IN');
        }
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @param string $compare
     */
    function meta($key, $value, $compare = 'LIKE')
    {
        if(!isset($this->params['meta_query']) || !is_array($this->params['meta_query'])) $this->params['meta_query'] = [];
        $this->params['meta_query'][] = [
            'key' => $key,
            'value' => $value,
            'compare' => $compare
        ];
        return $this;
    }

    /**
     * @param string $relation
     */
    function meta_relation($relation = 'OR') {
        $this->params['meta_query']['relation'] = $relation;
        return $this;
    }

    /**
     * @param $field
     * @param $value
     */
    function filter($field, $value)
    {
        $this->params[$field] = $value;
        return $this;
    }

    /**
     * @param $keyword
     * @return $this
     */
    function s($keyword)
    {
        $this->params['s'] = $keyword;
        return $this;
    }

    /**
     * @param $keyword
     * @return $this
     */
    function search($keyword)
    {
        $this->params['s'] = $keyword;
        return $this;
    }

    /**
     * @param $paged
     * @return $this
     */
    function paged($paged)
    {
        $this->params['paged'] = $paged;
        return $this;
    }

    /**
     * @param $author_id
     * @return $this
     */
    function author($author_id)
    {
        $this->params['author'] = $author_id;
        return $this;
    }

    /**
     * @param $author_id
     * @return $this
     */
    function post_status($status)
    {
        $this->params['post_status'] = $status;
        return $this;
    }

    /**
     * @param $author_name
     * @return $this
     */
    function author_name($author_name)
    {
        $this->params['author_name'] = $author_name;
        return $this;
    }

    /**
     * @param $author__not_in
     * @return $this
     */
    function author__not_in($author__not_in)
    {
        $this->params['author__not_in'] = $author__not_in;
        return $this;
    }

    /**
     * @return $this
     */
    function process()
    {
        if(count($this->params['tax_query']) <= 1 && isset($this->params['tax_query']['relation'])) unset($this->params['tax_query']['relation']);
        $this->params = apply_filters('wpdm_packages_query_params', $this->params);
        $this->params['post_type'] = $this->post_type;
        //wpdmdd($this->params);
        $this->result = new \WP_Query($this->params);
        $this->packages = $this->result->get_posts();
        $this->count = $this->result->found_posts;
        wp_reset_postdata();
        return $this;
    }

    /**
     * @return array
     */
    function packages()
    {
        return $this->packages;
    }


}