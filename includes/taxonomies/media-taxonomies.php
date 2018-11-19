<?php

namespace feed_them_gallery;
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/*
Plugin Name: Media Taxonomies
Plugin URI: http://horttcore.de
Description: Taxononmies for media files
Version: 1.3.0
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/


/**
 * Media_Taxonomies
 */
class Media_Taxonomies {
    /** Refers to a single instance of this class. */
    private static $instance = null;


    /**
     * Constructor
     *
     * @access public
     * @since v0.9
     * @author Ralf Hortt
     */
    public function __construct() {

        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_assets'));
        add_action('admin_head', array($this, 'admin_head'));
        add_filter('attachment_fields_to_edit', array($this, 'attachment_fields_to_edit'), 10, 2);
        add_action('init', array($this, 'register_taxonomy'));
        add_filter('manage_edit-attachment_sortable_columns', array($this, 'manage_edit_attachment_sortable_columns'));
        add_action('pre_get_posts', array($this, 'pre_get_posts'), 0, 1);
        add_action('restrict_manage_posts', array($this, 'restrict_manage_posts'));
        add_action('wp_ajax_save-media-terms', array($this, 'save_media_terms'), 0, 1);
        add_action('wp_ajax_add-media-term', array($this, 'add_media_term'), 0, 1);
        load_plugin_textdomain('media-taxonomies', false, dirname(plugin_basename(__FILE__)) . '/languages/');

        register_taxonomy_for_object_type('category', 'attachment');
        add_post_type_support('attachment', 'category');

    } // end __construct


    /**
     * Add media term
     * ajax callback
     *
     * @since v.1.3
     * @author Ralf Hortt
     **/
    public function add_media_term() {
        $response = array();
        $attachment_id = intval($_REQUEST['attachment_id']);
        $taxonomy = get_taxonomy(sanitize_text_field($_REQUEST['taxonomy']));
        $parent = (intval($_REQUEST['parent']) > 0) ? intval($_REQUEST['parent']) : 0;

        // Check if term already exists
        $term = get_term_by('name', sanitize_text_field($_REQUEST['term']), $taxonomy->name);

        // No, so lets add it
        if (!$term) :
            $term = wp_insert_term(sanitize_text_field($_REQUEST['term']), $taxonomy->name, array('parent' => $parent));
            $term = get_term_by('id', $term['term_id'], $taxonomy->name);
        endif;

        // Connect attachment with term
        wp_set_object_terms($attachment_id, $term->term_id, $taxonomy->name, TRUE);

        $attachment_terms = wp_get_object_terms($attachment_id, $taxonomy->name, array(
            'fields' => 'ids'
        ));

        ob_start();
        wp_terms_checklist(0, array(
            'selected_cats' => $attachment_terms,
            'taxonomy' => $taxonomy->name,
            'checked_ontop' => FALSE
        ));
        $checklist = ob_get_contents();
        ob_end_clean();

        $response['checkboxes'] = $checklist;
        $response['selectbox'] = wp_dropdown_categories(array(
            'taxonomy' => $taxonomy->name,
            'class' => 'parent-' . $taxonomy->name,
            'id' => 'parent-' . $taxonomy->name,
            'name' => 'parent-' . $taxonomy->name,
            'show_option_none' => '- ' . $taxonomy->labels->parent_item . ' -',
            'hide_empty' => FALSE,
            'echo' => FALSE,
        ));

        die(json_encode($response));
    }


    /**
     * Enqueue admin scripts and styles
     *
     * @access public
     * @since v0.9
     * @author Ralf Hortt
     */
    public function admin_enqueue_assets() {

        wp_enqueue_script('media-taxonomies', plugins_url('media-taxonomies.js', __FILE__), array('jquery'));
        wp_enqueue_style('media-taxonomies', plugins_url('media-taxonomies.css', __FILE__));

    } // end admin_enqueue_assets


    /**
     * Add taxonomy information
     *
     * @access public
     * @since v0.9
     * @author Ralf Hortt
     */
    public function admin_head() {

        register_taxonomy_for_object_type('category', 'attachment');
        add_post_type_support('attachment', 'category');

        $taxonomies = apply_filters('media-taxonomies', get_object_taxonomies('attachment', 'objects', 'ft_gallery'));

        if (!$taxonomies)
            return;

        $attachment_taxonomies = $attachment_terms = array();

        foreach ($taxonomies as $taxonomyname => $taxonomy) :

            $terms = get_terms($taxonomy->name, array(
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => true,
            ));

            if (!$terms)
                break;

            $attachment_taxonomies[$taxonomy->name] = $taxonomy->labels->name;
            $attachment_terms[$taxonomy->name][] = array('id' => 0, 'label' => __('All') . ' ' . $taxonomy->labels->name, 'slug' => '');

            foreach ($terms as $term)
                $attachment_terms[$taxonomy->name][] = array('id' => $term->term_id, 'label' => $term->name, 'slug' => $term->slug);

        endforeach;


        ?>
        <script type="text/javascript">
            var mediaTaxonomies = <?php echo json_encode($attachment_taxonomies) ?>,
                mediaTerms = <?php echo json_encode($attachment_terms) ?>;
        </script>
        <?php

    } // end admin_head


    /**
     * Add taxonomy checkboxes
     *
     * @access public
     * @param array $fields Fields
     * @param obj $post Post obj
     * @return array Fields
     * @since v0.9
     * @author Ralf Hortt
     */
    public function attachment_fields_to_edit($fields, $post) {

        $screen = get_current_screen();

        if (isset($screen->id) && 'attachment' == $screen->id)
            return $fields;

        $taxonomies = apply_filters('media-taxonomies', get_object_taxonomies('attachment', 'objects', 'ft_gallery'));

        if (!$taxonomies)
            return $fields;

        foreach ($taxonomies as $taxonomyname => $taxonomy) :

            $fields[$taxonomyname] = array(
                'label' => $taxonomy->labels->singular_name,
                'input' => 'html',
                'html' => $this->terms_checkboxes($taxonomy, $post->ID),
                // 'value' => '',
                // 'helps' => '',
                'show_in_edit' => true,
            );

        endforeach;

        return $fields;

    } // end attachment_fields_to_edit


    /**
     * Creates or returns an instance of this class.
     *
     * @access public
     * @return A single instance of this class.
     * @since v0.9
     */
    public static function get_instance() {

        if (null == self::$instance)
            self::$instance = new self;

        return self::$instance;

    } // end get_instance;


    /**
     *
     * Filter attachments in modal box
     *
     * @access public
     * @since v0.9.1
     * @author Ralf Hortt
     */
    public function pre_get_posts($query) {
        if (!isset($query->query_vars['post_type']) || 'attachment' != $query->query_vars['post_type'])
            return;

        $taxonomies = apply_filters('media-taxonomies', get_object_taxonomies('attachment', 'objects', 'ft_gallery'));

        if (!$taxonomies)
            return;

        foreach ($taxonomies as $taxonomyname => $taxonomy) :

            if (isset($_REQUEST['query'][$taxonomyname]) && $_REQUEST['query'][$taxonomyname]['term_slug'])     :
                $query->set($taxonomyname, $_REQUEST['query'][$taxonomyname]['term_slug']);
            elseif (isset($_REQUEST[$taxonomyname]) && is_numeric($_REQUEST[$taxonomyname]) && 0 != intval($_REQUEST[$taxonomyname])) :
                $term = get_term_by('id', $_REQUEST[$taxonomyname], $taxonomyname);
                if (is_object($term))
                    set_query_var($taxonomyname, $term->slug);
            endif;

        endforeach;

    } // end pre_get_posts


    /**
     *
     * Register taxonomy
     *
     * @access public
     * @since v0.9
     * @author Ralf Hortt
     */
    public function register_taxonomy() {

        /*register_taxonomy('media-category', array('attachment', 'ft_gallery'), array(
            'hierarchical' => TRUE,
            'labels' => array(
                'name' => _x('Categories', 'taxonomy general name'),
                'singular_name' => _x('Category', 'taxonomy singular name'),
                'search_items' => __('Search Categories'),
                'all_items' => __('All Categories'),
                'parent_item' => __('Parent Category'),
                'parent_item_colon' => __('Parent Category:'),
                'edit_item' => __('Edit Category'),
                'update_item' => __('Update Category'),
                'add_new_item' => __('Add New Category'),
                'new_item_name' => __('New Category Name'),
                'menu_name' => __('Categories'),
            ),
            'show_ui' => TRUE,
            'query_var' => TRUE,
            'rewrite' => array('slug' => _x('media-category', 'Category Slug', 'media-taxonomies')),
            'show_admin_column' => TRUE,
            'update_count_callback' => '_update_generic_term_count',
        ));*/

        register_taxonomy('media-tag', array('attachment'), array(
            'hierarchical' => FALSE,
            'labels' => array(
                'name' => _x('Tags', 'taxonomy general name'),
                'singular_name' => _x('Tag', 'taxonomy singular name'),
                'search_items' => __('Search Tags'),
                'all_items' => __('All Tags'),
                'parent_item' => __('Parent Tag'),
                'parent_item_colon' => __('Parent Tag:'),
                'edit_item' => __('Edit Tag'),
                'update_item' => __('Update Tag'),
                'add_new_item' => __('Add New Tag'),
                'new_item_name' => __('New Tag Name'),
                'menu_name' => __('Tags'),
            ),
            'show_ui' => TRUE,
            'query_var' => TRUE,
            'rewrite' => array('slug' => _x('media-tag', 'Tag Slug', 'media-taxonomies')),
            'show_admin_column' => TRUE,
            'update_count_callback' => '_update_generic_term_count',
        ));

    } // end register_taxonomy;


    /**
     * Add custom filters in attachment listing
     *
     * @access public
     * @since v0.9
     * @author Ralf Hortt
     **/
    public function restrict_manage_posts() {

        global $wp_query;

        $taxonomies = apply_filters('media-taxonomies', get_object_taxonomies('attachment', 'objects'));

        if (!$taxonomies)
            return;

        foreach ($taxonomies as $taxonomyname => $taxonomy) :

            wp_dropdown_categories(array(
                'show_option_all' => sprintf(_x('View all %s', '%1$s = plural, %2$s = singular', 'media-taxonomies'), $taxonomy->labels->name, $taxonomy->labels->singular_name),
                'taxonomy' => $taxonomyname,
                'name' => $taxonomyname,
                'orderby' => 'name',
                'selected' => (isset($wp_query->query[$taxonomyname]) ? $wp_query->query[$taxonomyname] : ''),
                'hierarchical' => FALSE,
                'hide_empty' => TRUE,
                'hide_if_empty' => TRUE,
            ));

        endforeach;

    } // end restrict_manage_posts


    /**
     * Save media terms
     *
     * @todo security nonce
     * @since v0.9
     * @author Ralf Hortt
     */
    public function save_media_terms() {

        $post_id = intval($_REQUEST['attachment_id']);

        if (!current_user_can('edit_post', $post_id))
            die();

        $term_ids = array_map('intval', $_REQUEST['term_ids']);

        $response = wp_set_post_terms($post_id, $term_ids, sanitize_text_field($_REQUEST['taxonomy']));
        wp_update_term_count_now($term_ids, sanitize_text_field($_REQUEST['taxonomy']));

    } // end save_media_terms


    /**
     * Create a terms box
     *
     * @access protected
     * @param obj $taxonomy Taxonomy
     * @return str HTML output
     * @since v0.9
     * @author Ralf Hortt
     */
    public function terms_checkboxes($taxonomy, $post_id) {

        if (!is_object($taxonomy)) :

            $taxonomy = get_taxonomy($taxonomy);

        endif;

        $terms = get_terms($taxonomy->name, array(
            'hide_empty' => FALSE,
        ));

        $attachment_terms = wp_get_object_terms($post_id, $taxonomy->name, array(
            'fields' => 'ids'
        ));

        ob_start();

        // Post object.
        $post = null;

        // Tags meta box arguments.
        $box = array(
            'id' => '',
            'title' => '',
            'args' => array(
                'taxonomy' => $taxonomy->name
            )
        );

        $defaults = array( 'taxonomy' => 'post_tag' );
        if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
            $args = array();
        } else {
            $args = $box['args'];
        }
        $r = wp_parse_args( $args, $defaults );
        $tax_name = esc_attr( $r['taxonomy'] );
        $taxonomy = get_taxonomy( $r['taxonomy'] );
        $user_can_assign_terms = current_user_can( $taxonomy->cap->assign_terms );
        $comma = _x( ',', 'tag delimiter' );
        $terms_to_edit = get_terms_to_edit( $post->ID, $tax_name );
        if ( ! is_string( $terms_to_edit ) ) {
            $terms_to_edit = '';
        }
        ?>
        <div class="tagsdiv" id="<?php echo $tax_name; ?>">
            <div class="jaxtag">
                <div class="nojs-tags hide-if-js">
                    <label for="tax-input-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_or_remove_items; ?></label>
                    <p><textarea name="<?php echo "tax_input[$tax_name]"; ?>" rows="3" cols="20" class="the-tags" id="tax-input-<?php echo $tax_name; ?>" <?php disabled( ! $user_can_assign_terms ); ?> aria-describedby="new-tag-<?php echo $tax_name; ?>-desc"><?php echo str_replace( ',', $comma . ' ', $terms_to_edit ); // textarea_escaped by esc_attr() ?></textarea></p>
                </div>
                <?php if ( $user_can_assign_terms ) : ?>
                    <div class="ajaxtag hide-if-no-js">
                        <label class="screen-reader-text" for="new-tag-<?php echo $tax_name; ?>"><?php echo $taxonomy->labels->add_new_item; ?></label>
                        <p><input data-wp-taxonomy="<?php echo $tax_name; ?>" type="text" id="new-tag-<?php echo $tax_name; ?>" name="newtag[<?php echo $tax_name; ?>]" class="newtag form-input-tip" size="16" autocomplete="off" aria-describedby="new-tag-<?php echo $tax_name; ?>-desc" value="" />
                            <input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" /></p>
                    </div>
                    <p class="howto" id="new-tag-<?php echo $tax_name; ?>-desc"><?php echo $taxonomy->labels->separate_items_with_commas; ?></p>
                <?php elseif ( empty( $terms_to_edit ) ): ?>
                    <p><?php echo $taxonomy->labels->no_terms; ?></p>
                <?php endif; ?>
            </div>
            <ul class="tagchecklist" role="list"></ul>
        </div>
        <?php if ( $user_can_assign_terms ) : ?>
            <p class="hide-if-no-js"><button type="button" class="button-link tagcloud-link" id="link-<?php echo $tax_name; ?>" aria-expanded="false"><?php echo $taxonomy->labels->choose_from_most_used; ?></button></p>
        <?php endif; ?>

        <?php

        $output = ob_get_contents();
        ob_end_clean();

        return apply_filters('media-checkboxes', $output, $taxonomy, $terms);

    } // end terms_checkboxes


}