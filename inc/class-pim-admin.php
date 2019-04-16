<?php

namespace PIM;
/**
 * Class PIM_Admin
 */
class PIM_Admin
{

    public static $fields;
    public static $item_object;

    public function __construct() {
        add_action('admin_head-nav-menus.php', array($this, 'add_recent_posts_area'));
        add_filter('wp_setup_nav_menu_item', array($this, 'setup_nav_menu_item'));

        self::$fields = array(
            '_pim_menu_item_posts_number' => array(
               'label'             => __( 'Number of posts', 'domain' ),
               'element'           => 'input',
               'sanitize_callback' => 'sanitize_text_field',
               'attrs'               => array(
                    'type' => 'number',
               )
             )
            );

        self::$item_object = 'recent_posts';

        add_filter( 'wp_edit_nav_menu_walker', array( $this, 'edit_nav_menu_walker' ) );
        add_action( 'wp_update_nav_menu_item', array( $this, 'update_nav_menu_item' ), 10, 3 );


    }

    /**
     * Add recent post area to the menu settings sidebar
     *
     */

    function add_recent_posts_area() {
        add_meta_box('pim_recent_posts', 'Recent Posts', array($this, 'content'), 'nav-menus', 'side', 'default');
    }


    /**
     * Content of recent post area
     *
     */

    function content() {
        $post_types = get_post_types(array('show_in_nav_menus' => true), 'object');

        unset($post_types['page']);

        if ($post_types) {
            foreach ($post_types as $post_type) {
                $post_type->classes = array();
                $post_type->type = $post_type->name;
                $post_type->object_id = $post_type->name;
                $post_type->title = $post_type->labels->name;
                $post_type->object = self::$item_object;

                $post_type->menu_item_parent = null;
                $post_type->url = null;
                $post_type->target = null;
                $post_type->attr_title = null;
                $post_type->xfn = null;
                $post_type->db_id = null;
                $post_type->description = null;
            }
            $walker = new \Walker_Nav_Menu_Checklist(array());
            ?>
            <div id="pim-post-type" class="posttypediv">
                <div id="tabs-panel-pim" class="tabs-panel tabs-panel-active">
                    <ul id="pim-list" class="categorychecklist form-no-clear">
                        <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $post_types), 0, (object)array('walker' => $walker)); ?>
                    </ul>
                </div>
            </div>
            <p class="button-controls">
                    <span class="add-to-menu">
                        <input type="submit" class="button-secondary submit-add-to-menu"
                               value="<?php esc_attr_e('Add to Menu'); ?>" name="add-pim-menu-item"
                               id="submit-pim-post-type"/>
                    </span>
            </p>
            <?php
        }
    }

    /**
     * Setup nav menu item
     *
     */

    function setup_nav_menu_item($item) {
        if (isset($item->object)) {
            if ($item->object == self::$item_object) {
                $item->type_label = 'Recent Posts';
                
            }
        }
        return $item;
    }

    /**
     * Replaces default menu editor walker with custom one.
     *
     * @return void.
     */
    function edit_nav_menu_walker( $walker ) {
        $walker = 'PIM_Menu_Walker_Edit';
        require_once dirname( __FILE__ ) . '/Menu_Walker_Edit.php';
        return $walker;
    }
    /**
     * Update postmeta for the menu items.
     *
     * @return  void
     */
    function update_nav_menu_item( $menu_id, $menu_item_id, $args ) {
        $request = stripslashes_deep( $_POST );
        $item = get_post($menu_item_id);

        foreach ( self::$fields as $key => $field ) {
            if ( ! isset( $field['sanitize_callback'] ) ) {
                $field['sanitize_callback'] = 'sanitize_text_field';
            }
            if ( isset( $request[ $key ] ) && isset( $request[ $key ][ $menu_item_id ] ) ) {
                update_post_meta( $menu_item_id, $key, call_user_func( $field['sanitize_callback'], $request[ $key ][ $menu_item_id ] ) );
            } else {
                delete_post_meta( $menu_item_id, $key );
            }
        }
    }
}
