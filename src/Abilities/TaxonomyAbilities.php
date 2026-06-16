<?php
namespace MBCPT\Abilities;

use WP_Taxonomy;
use WP_REST_Request;
use WP_REST_Terms_Controller;

class TaxonomyAbilities {

	private string $slug;
	private string $singular;
	private string $label;
	private WP_Taxonomy $taxonomy;
	private array $settings;

	public function __construct( string $slug, WP_Taxonomy $taxonomy, array $settings ) {
		$this->slug     = $slug;
		$this->taxonomy = $taxonomy;
		$this->singular = $taxonomy->labels->singular_name ?? $slug;
		$this->label    = $taxonomy->labels->name ?? $slug;
		$this->settings = $settings;
	}

	public function register(): void {
		if ( ! empty( $this->settings['abilities_get_data'] ) ) {
			$this->register_get_taxonomy_ability();
		}
		if ( ! empty( $this->settings['abilities_get'] ) ) {
			$this->register_get_term_ability();
		}
		if ( ! empty( $this->settings['abilities_create'] ) ) {
			$this->register_create_term_ability();
		}
		if ( ! empty( $this->settings['abilities_update'] ) ) {
			$this->register_update_term_ability();
		}
		if ( ! empty( $this->settings['abilities_delete'] ) ) {
			$this->register_delete_term_ability();
		}
	}

	private function register_get_term_ability(): void {
		wp_register_ability(
			"meta-box/get-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->taxonomy->cap->assign_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'         => [
							'type'        => 'integer',
							'description' => __( 'Term ID to retrieve.', 'mb-custom-post-type' ),
						],
						'context'    => [
							'type'        => 'string',
							'description' => __( 'Scope under which the request is made.', 'mb-custom-post-type' ),
							'enum'        => [ 'view', 'embed', 'edit' ],
							'default'     => 'view',
						],
						'search'     => [
							'type'        => 'string',
							'description' => __( 'Search keyword.', 'mb-custom-post-type' ),
						],
						'parent'     => [
							'type'        => 'integer',
							'description' => __( 'Limit result set to terms assigned to a specific parent ID.', 'mb-custom-post-type' ),
						],
						'per_page'   => [
							'type'        => 'integer',
							'description' => __( 'Maximum number of terms to return (1-100).', 'mb-custom-post-type' ),
							'default'     => 10,
							'minimum'     => 1,
							'maximum'     => 100,
						],
						'page'       => [
							'type'        => 'integer',
							'description' => __( 'Current page of the collection.', 'mb-custom-post-type' ),
							'default'     => 1,
							'minimum'     => 1,
						],
						'orderby'    => [
							'type'        => 'string',
							'description' => __( 'Sort collection by term attribute.', 'mb-custom-post-type' ),
							'enum'        => [ 'id', 'include', 'name', 'slug', 'include_slugs', 'term_group', 'description', 'count' ],
							'default'     => 'name',
						],
						'order'      => [
							'type'        => 'string',
							'description' => __( 'Order sort attribute ascending or descending.', 'mb-custom-post-type' ),
							'enum'        => [ 'asc', 'desc' ],
							'default'     => 'asc',
						],
						'hide_empty' => [
							'type'        => 'boolean',
							'description' => __( 'Hide terms with no posts.', 'mb-custom-post-type' ),
							'default'     => false,
						],
					],
				],
				'output_schema'       => [
					'type'  => 'array',
					'items' => $this->term_output_schema(),
				],
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [
						'public' => true,
					],
				],
				'execute_callback'    => function ( array $input ): array {
					return $this->execute_get_terms( $input );
				},
			]
		);
	}

	private function register_create_term_ability(): void {
		wp_register_ability(
			"meta-box/create-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Create a new %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->taxonomy->cap->manage_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'name' ],
					'properties' => [
						'name'        => [
							'type'        => 'string',
							'description' => __( 'Term name.', 'mb-custom-post-type' ),
						],
						'slug'        => [
							'type'        => 'string',
							'description' => __( 'Term slug.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'description' => [
							'type'        => 'string',
							'description' => __( 'Term description.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'parent'      => [
							'type'        => 'integer',
							'description' => __( 'Parent term ID.', 'mb-custom-post-type' ),
						],
						'meta'        => [
							'type'        => 'object',
							'description' => __( 'Term meta values keyed by meta key. Only meta keys registered with show_in_rest are persisted.', 'mb-custom-post-type' ),
						],
					],
				],
				'output_schema'       => $this->term_output_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
					],
					'mcp'         => [
						'public' => true,
					],
				],
				'execute_callback'    => function ( array $input ): array {
					return $this->execute_create_term( $input );
				},
			]
		);
	}

	private function register_update_term_ability(): void {
		wp_register_ability(
			"meta-box/update-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Update an existing %s. Only provided fields are modified.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->taxonomy->cap->edit_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'          => [
							'type'        => 'integer',
							'description' => __( 'Term ID to update.', 'mb-custom-post-type' ),
						],
						'name'        => [
							'type'        => 'string',
							'description' => __( 'New term name.', 'mb-custom-post-type' ),
						],
						'slug'        => [
							'type'        => 'string',
							'description' => __( 'New term slug.', 'mb-custom-post-type' ),
						],
						'description' => [
							'type'        => 'string',
							'description' => __( 'New term description.', 'mb-custom-post-type' ),
						],
						'parent'      => [
							'type'        => 'integer',
							'description' => __( 'New parent term ID.', 'mb-custom-post-type' ),
						],
						'meta'        => [
							'type'        => 'object',
							'description' => __( 'New term meta values keyed by meta key. Only meta keys registered with show_in_rest are persisted.', 'mb-custom-post-type' ),
						],
					],
				],
				'output_schema'       => $this->term_output_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [
						'public' => true,
					],
				],
				'execute_callback'    => function ( array $input ): array {
					return $this->execute_update_term( $input );
				},
			]
		);
	}

	private function register_delete_term_ability(): void {
		wp_register_ability(
			"meta-box/delete-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->taxonomy->cap->delete_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'    => [
							'type'        => 'integer',
							'description' => __( 'Term ID to delete.', 'mb-custom-post-type' ),
						],
						'force' => [
							'type'        => 'boolean',
							'description' => __( 'Required to be true, as terms do not support trashing.', 'mb-custom-post-type' ),
							'default'     => false,
						],
					],
				],
				'output_schema'       => $this->term_output_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					],
					'mcp'         => [
						'public' => true,
					],
				],
				'execute_callback'    => function ( array $input ): array {
					return $this->execute_delete_term( $input );
				},
			]
		);
	}

	private function register_get_taxonomy_ability(): void {
		wp_register_ability(
			"meta-box/get-taxonomy-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s taxonomy', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Get %s taxonomy data.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->taxonomy->cap->assign_terms );
				},
				'input_schema'        => [
					'type' => 'object',
				],
				'output_schema'       => $this->taxonomy_output_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [
						'public' => true,
					],
				],
				'execute_callback'    => function (): array {
					return $this->execute_get_taxonomy();
				},
			]
		);
	}

	private function execute_get_terms( array $input ): array {
		$context    = $input['context'] ?? 'view';
		$rest       = rest_get_server();
		$base       = '/' . ( $this->taxonomy->rest_base ?: $this->slug );
		$controller = new WP_REST_Terms_Controller( $this->slug );

		if ( ! empty( $input['id'] ) ) {
			$request            = new WP_REST_Request( 'GET', $base . '/' . (int) $input['id'] );
			$request['context'] = $context;

			$response = $controller->get_item( $request );
			if ( is_wp_error( $response ) ) {
				return [];
			}
			return [ $rest->response_to_data( $response, true ) ];
		}

		$request = new WP_REST_Request( 'GET', $base );
		$request->set_query_params( $this->map_input_to_rest_params( $input ) );
		$request['context'] = $context;

		$response = $controller->get_items( $request );
		if ( is_wp_error( $response ) ) {
			return [];
		}

		return $rest->response_to_data( $response, true );
	}

	private const REST_PASSTHROUGH = [
		'context',
		'page',
		'per_page',
		'search',
		'exclude',
		'include',
		'orderby',
		'order',
		'hide_empty',
		'parent',
		'parent_exclude',
		'post',
		'slug',
		'name',
		'description',
		'meta',
	];

	private function map_input_to_rest_params( array $input ): array {
		$params = [];
		foreach ( self::REST_PASSTHROUGH as $key ) {
			if ( array_key_exists( $key, $input ) ) {
				$params[ $key ] = $input[ $key ];
			}
		}

		return $params;
	}

	private function execute_create_term( array $input ): array {
		$context    = $input['context'] ?? 'view';
		$controller = new WP_REST_Terms_Controller( $this->slug );
		$base       = '/' . ( $this->taxonomy->rest_base ?: $this->slug );

		$request = new WP_REST_Request( 'POST', $base );
		$request->set_body_params( $this->map_input_to_rest_params( $input ) );
		$request['context'] = $context;

		$response = $controller->create_item( $request );
		if ( is_wp_error( $response ) ) {
			return [];
		}

		return rest_get_server()->response_to_data( $response, true );
	}

	private function execute_update_term( array $input ): array {
		$context    = $input['context'] ?? 'view';
		$controller = new WP_REST_Terms_Controller( $this->slug );
		$base       = '/' . ( $this->taxonomy->rest_base ?: $this->slug );

		$request = new WP_REST_Request( 'PUT', $base . '/' . (int) $input['id'] );
		$request->set_body_params( $this->map_input_to_rest_params( $input ) );
		$request['context'] = $context;

		$response = $controller->update_item( $request );
		if ( is_wp_error( $response ) ) {
			return [];
		}

		return rest_get_server()->response_to_data( $response, true );
	}

	private function execute_delete_term( array $input ): array {
		$context    = $input['context'] ?? 'view';
		$controller = new WP_REST_Terms_Controller( $this->slug );
		$base       = '/' . ( $this->taxonomy->rest_base ?: $this->slug );

		$request            = new WP_REST_Request( 'DELETE', $base . '/' . (int) $input['id'] );
		$request['context'] = $context;
		$request['force']   = ! empty( $input['force'] );

		$response = $controller->delete_item( $request );
		if ( is_wp_error( $response ) ) {
			return [];
		}

		return rest_get_server()->response_to_data( $response, true );
	}

	private function execute_get_taxonomy(): array {
		$taxonomy = $this->taxonomy;

		return [
			'name'               => $taxonomy->name,
			'label'              => $taxonomy->label,
			'labels'             => (array) $taxonomy->labels,
			'description'        => $taxonomy->description,
			'public'             => $taxonomy->public,
			'hierarchical'       => $taxonomy->hierarchical,
			'publicly_queryable' => $taxonomy->publicly_queryable,
			'show_ui'            => $taxonomy->show_ui,
			'show_in_menu'       => $taxonomy->show_in_menu,
			'show_in_nav_menus'  => $taxonomy->show_in_nav_menus,
			'show_tagcloud'      => $taxonomy->show_tagcloud,
			'show_in_rest'       => $taxonomy->show_in_rest,
			'rest_base'          => $taxonomy->rest_base,
			'rest_namespace'     => $taxonomy->rest_namespace,
			'sort'               => $taxonomy->sort,
			'args'               => (array) $taxonomy->args,
			'object_type'        => $taxonomy->object_type,
			'rewrite'            => (array) $taxonomy->rewrite,
			'query_var'          => $taxonomy->query_var,
			'cap'                => (array) $taxonomy->cap,
		];
	}

	private function term_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'id'          => [ 'type' => 'integer' ],
				'name'        => [ 'type' => 'string' ],
				'slug'        => [ 'type' => 'string' ],
				'description' => [ 'type' => 'string' ],
				'parent'      => [ 'type' => 'integer' ],
				'count'       => [ 'type' => 'integer' ],
				'taxonomy'    => [ 'type' => 'string' ],
				'link'        => [ 'type' => 'string' ],
				'meta'        => [ 'type' => 'object' ],
			],
		];
	}

	private function taxonomy_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'name'               => [ 'type' => 'string' ],
				'label'              => [ 'type' => 'string' ],
				'labels'             => [ 'type' => 'object' ],
				'description'        => [ 'type' => 'string' ],
				'public'             => [ 'type' => 'boolean' ],
				'hierarchical'       => [ 'type' => 'boolean' ],
				'publicly_queryable' => [ 'type' => 'boolean' ],
				'show_ui'            => [ 'type' => 'boolean' ],
				'show_in_menu'       => [ 'type' => [ 'boolean', 'string' ] ],
				'show_in_nav_menus'  => [ 'type' => 'boolean' ],
				'show_tagcloud'      => [ 'type' => 'boolean' ],
				'show_in_rest'       => [ 'type' => 'boolean' ],
				'rest_base'          => [ 'type' => 'string' ],
				'rest_namespace'     => [ 'type' => 'string' ],
				'sort'               => [ 'type' => 'boolean' ],
				'args'               => [ 'type' => 'object' ],
				'object_type'        => [
					'type'  => 'array',
					'items' => [ 'type' => 'string' ],
				],
				'rewrite'            => [ 'type' => [ 'boolean', 'object' ] ],
				'query_var'          => [ 'type' => [ 'boolean', 'string' ] ],
				'cap'                => [ 'type' => 'object' ],
			],
		];
	}
}
