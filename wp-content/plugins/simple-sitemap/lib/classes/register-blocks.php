<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 *    Register blocks
 */
class Register_Blocks
{
    /**
     * Common root paths/directories.
     *
     * @var $module_roots
     */
    protected  $module_roots ;
    /**
     * Main class constructor.
     *
     * @param Array $module_roots Root plugin path/dir.
     */
    public function __construct( $module_roots )
    {
        $this->module_roots = $module_roots;
        add_filter(
            'block_categories_all',
            array( &$this, 'add_block_category' ),
            10,
            2
        );
        add_action( 'plugins_loaded', array( &$this, 'register_blocks' ) );
    }
    
    /**
     * Add custom block category.
     *
     * @param Array  $categories Current block categories.
     * @param Object $post Post object.
     * @return Array Result of array merge.
     */
    public function add_block_category( $categories, $post )
    {
        return array_merge( $categories, array( array(
            'slug'  => 'simple-sitemap',
            'title' => __( 'Simple Sitemap', 'simple-sitemap' ),
        ) ) );
    }
    
    /**
     * Register dynamic blocks.
     */
    public function register_blocks()
    {
        // Only register block if gutenberg enabled.
        
        if ( function_exists( 'register_block_type' ) ) {
            $simple_sitemap_block_attr = array(
                'render_tab'       => array(
                'type'    => 'boolean',
                'default' => false,
            ),
                'page_depth'       => array(
                'type'    => 'number',
                'default' => 0,
            ),
                'orderby'          => array(
                'type'    => 'string',
                'default' => 'title',
            ),
                'order'            => array(
                'type'    => 'string',
                'default' => 'asc',
            ),
                'show_excerpt'     => array(
                'type'    => 'boolean',
                'default' => false,
            ),
                'show_label'       => array(
                'type'    => 'boolean',
                'default' => true,
            ),
                'links'            => array(
                'type'    => 'boolean',
                'default' => true,
            ),
                'title_tag'        => array(
                'type'    => 'string',
                'default' => '',
            ),
                'post_type_tag'    => array(
                'type'    => 'string',
                'default' => 'h3',
            ),
                'excerpt_tag'      => array(
                'type'    => 'string',
                'default' => 'div',
            ),
                'container_tag'    => array(
                'type'    => 'string',
                'default' => 'ul',
            ),
                'block_post_types' => array(
                'type'    => 'string',
                'default' => '[{ "value": "page", "label": "Pages" }]',
            ),
                'gutenberg_block'  => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            );
            // Register the sitemap block and define the attributes.
            register_block_type( 'wpgoplugins/simple-sitemap-block', array(
                'attributes'      => $simple_sitemap_block_attr,
                'render_callback' => array( Simple_Sitemap_Shortcode::get_instance(), 'render_block' ),
            ) );
            $simple_sitemap_group_block_attr = array(
                'block_taxonomy'  => array(
                'type'    => 'string',
                'default' => 'category',
            ),
                'title_tag'       => array(
                'type'    => 'string',
                'default' => '',
            ),
                'show_excerpt'    => array(
                'type'    => 'boolean',
                'default' => false,
            ),
                'excerpt_tag'     => array(
                'type'    => 'string',
                'default' => 'div',
            ),
                'links'           => array(
                'type'    => 'boolean',
                'default' => true,
            ),
                'orderby'         => array(
                'type'    => 'string',
                'default' => 'title',
            ),
                'order'           => array(
                'type'    => 'string',
                'default' => 'asc',
            ),
                'post_type_tag'   => array(
                'type'    => 'string',
                'default' => 'h3',
            ),
                'show_label'      => array(
                'type'    => 'boolean',
                'default' => true,
            ),
                'page_depth'      => array(
                'type'    => 'number',
                'default' => 0,
            ),
                'container_tag'   => array(
                'type'    => 'string',
                'default' => 'ul',
            ),
                'block_post_type' => array(
                'type'    => 'string',
                'default' => 'post',
            ),
                'num_terms'       => array(
                'type'    => 'number',
                'default' => 0,
            ),
                'gutenberg_block' => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            );
            // Register the sitemap group block and define the attributes.
            register_block_type( 'wpgoplugins/simple-sitemap-group-block', array(
                'attributes'      => $simple_sitemap_group_block_attr,
                'render_callback' => array( Simple_Sitemap_Group_Shortcode::get_instance(), 'render_block' ),
            ) );
        }
    
    }

}
/* End class definition */