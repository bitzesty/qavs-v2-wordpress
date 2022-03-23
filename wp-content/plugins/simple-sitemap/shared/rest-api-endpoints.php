<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Register custom REST API endpoints
 */
class Custom_Sitemap_Endpoints {

	/**
	 * Common root paths/directories.
	 *
	 * @var $module_roots
	 */
	protected $module_roots;

	/**
	 * Main class constructor.
	 *
	 * @param Array $module_roots Root plugin path/dir.
	 */
	public function __construct( $module_roots ) {

		$this->module_roots       = $module_roots;
		$this->rest_api_namespace = 'simple-sitemap/v1';

		add_action( 'rest_api_init', array( &$this, 'register_endpoints' ) );
	}

	/**
	 * Register REST API
	 */
	public function register_endpoints() {

		// Get public CPT.
		register_rest_route(
			$this->rest_api_namespace,
			'/post-types',
			array(
				'methods'             => 'GET',
				'callback'            => array( &$this, 'get_post_types' ),
				'permission_callback' => array( &$this, 'check_post_permissions' ),
			)
		);

		// Get registered taxonomies for specified post type.
		register_rest_route(
			$this->rest_api_namespace,
			'/post-type-taxonomies/(?P<type>[a-zA-Z0-9-_]+)', // allowed chars [a-z] [A-Z] [0-9] [-_].
			array(
				'methods'             => 'GET',
				'callback'            => array( &$this, 'get_post_type_taxonomies' ),
				'permission_callback' => array( &$this, 'check_post_permissions' ),
			)
		);
	}

	/**
	 * Get public post types.
	 */
	public function get_post_types() {

		$post_type_args        = array(
			'public' => true,
		);
		$registered_post_types = get_post_types( $post_type_args );

		// Remove 'attachment' (media) from list of post types.
		if ( in_array( 'attachment', $registered_post_types ) ) {
			unset( $registered_post_types['attachment'] );
		}

		$sitemap_post_types = array();
		foreach ( $registered_post_types as $key => $value ) {
			$sitemap_post_types[ $key ] = get_post_type_object( $key )->label;
		}

		return $sitemap_post_types;
	}

	/**
	 * Get taxonomies for specific post type.
	 *
	 * @param WP_REST_Request $request Request object passed in from the REST endpoint.
	 */
	public function get_post_type_taxonomies( $request ) {

		$post_type            = $request->get_param( 'type' );
		$post_type_taxonomies = get_object_taxonomies( $post_type );

		// If empty array no taxonomies return empty.
		if ( empty( $post_type_taxonomies ) ) {
			return array();
		}

		// Remove 'post_format' from list of taxonomies.
		if ( ( $key = array_search( 'post_format', $post_type_taxonomies ) ) !== false ) {
			unset( $post_type_taxonomies[ $key ] );
		}

		// Format into array.
		$taxonomies = array();
		foreach ( $post_type_taxonomies as $post_type_taxonomy ) {
			$tax                          = get_taxonomy( $post_type_taxonomy );
				$taxonomies[ $tax->name ] = $tax->label;
		}

		return $taxonomies;
	}

	/**
	 * Check post permissions.
	 */
	public function check_post_permissions() {

		// Restrict endpoint to only users who have the edit_posts capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
				return new WP_Error( 'rest_forbidden', esc_html__( 'No permissions to view post data.', 'simple-sitemap' ), array( 'status' => 401 ) );
		}

		return true;
	}

} /* End class definition. */
