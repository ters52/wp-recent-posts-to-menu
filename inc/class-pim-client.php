<?php
namespace PIM;

/**
 * Class Frontend
 */
class PIM_Client
{
    public function __construct() {
        add_filter('wp_get_nav_menu_items', array($this, 'get_nav_menu_items'));
    }

    /**
     * Add additional submenus for recent_posts item type
     *
     */

    function get_nav_menu_items($items) {

        $child_items = array();
        $menu_order = count($items); 


        foreach ($items as $key => $item) {

            if ($item->post_type == 'nav_menu_item') {

                if ($item->object == 'recent_posts') {

                    $posts_number = get_post_meta($item->ID, '_pim_menu_item_posts_number', true);

                    $category_ten_last_posts = array(
                        'posts_per_page' => ($posts_number > 1) ? $posts_number : 1,
                        'post_type'      => $item->type,
                        'orderby'        => 'date',
                        'order'          => 'DESC'
                    );
                    $posts = get_posts( $category_ten_last_posts );

                    foreach( $posts as $post ){
                        // Add sub menu item
                        $post->menu_item_parent = $item->ID;
                        $post->post_type        = 'nav_menu_item';
                        $post->object           = 'recent_posts';
                        $post->type             = 'recent_posts';
                        $post->menu_order       = ++ $menu_order;
                        $post->title            = $post->post_title;
                        $post->url              = get_permalink( $post->ID );
                        $child_items[]          = $post;
                    }
                    
                    
                
                }

            }
        }
        return array_merge($items, $child_items);
    }
}
