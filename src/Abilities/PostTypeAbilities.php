<?php
namespace MBCPT\Abilities;

use WP_Post;
use WP_Post_Type;
use WP_REST_Posts_Controller;
use WP_REST_Request;

class PostTypeAbilities {

	private string $slug;
	private string $singular;
	private string $label;
	private WP_Post_Type $post_type;
	private array $settings;

	public function __construct( string $slug, WP_Post_Type $post_type, array $settings ) {
		$this->slug      = $slug;
		$this->post_type = $post_type;
		$this->singular  = $post_type->labels->singular_name ?? $slug;
		$this->label     = $post_type->labels->name ?? $slug;
		$this->settings  = $settings;
	}

	public function register(): void {
		if ( ! empty( $this->settings['abilities_get_data'] ) ) {
			$this->register_get_post_type_ability();
		}
		if ( ! empty( $this->settings['abilities_get'] ) ) {
			$this->register_get_ability();
		}
		if ( ! empty( $this->settings['abilities_create'] ) ) {
			$this->register_create_ability();
		}
		if ( ! empty( $this->settings['abilities_update'] ) ) {
			$this->register_update_ability();
		}
		if ( ! empty( $this->settings['abilities_delete'] ) ) {
			$this->register_delete_ability();
		}
	}

	private function register_get_ability(): void {
		wp_register_ability(
			"meta-box/get-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Search and list %s. Mirrors the WordPress REST API arguments for this post type.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->read );
				},
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'              => [
							'type'        => 'integer',
							'description' => __( 'Post ID to retrieve.', 'mb-custom-post-type' ),
						],
						'context'         => [
							'type'        => 'string',
							'description' => __( 'Scope under which the request is made (affects which fields are present in the response).', 'mb-custom-post-type' ),
							'enum'        => [ 'view', 'embed', 'edit' ],
							'default'     => 'view',
						],
						'page'            => [
							'type'        => 'integer',
							'description' => __( 'Current page of the collection.', 'mb-custom-post-type' ),
							'default'     => 1,
							'minimum'     => 1,
						],
						'per_page'        => [
							'type'        => 'integer',
							'description' => __( 'Maximum number of items to be returned in the result set (1-100).', 'mb-custom-post-type' ),
							'default'     => 10,
							'minimum'     => 1,
							'maximum'     => 100,
						],
						'search'          => [
							'type'        => 'string',
							'description' => __( 'Limit results to those matching a string.', 'mb-custom-post-type' ),
						],
						'search_columns'  => [
							'type'        => 'array',
							'description' => __( 'Array of column names to be searched.', 'mb-custom-post-type' ),
							'items'       => [
								'type' => 'string',
								'enum' => [ 'post_title', 'post_content', 'post_excerpt', 'post_author' ],
							],
							'default'     => [],
						],
						'after'           => [
							'type'        => 'string',
							'description' => __( 'Limit response to posts published after a given ISO8601-compliant date.', 'mb-custom-post-type' ),
						],
						'before'          => [
							'type'        => 'string',
							'description' => __( 'Limit response to posts published before a given ISO8601-compliant date.', 'mb-custom-post-type' ),
						],
						'modified_after'  => [
							'type'        => 'string',
							'description' => __( 'Limit response to posts modified after a given ISO8601-compliant date.', 'mb-custom-post-type' ),
						],
						'modified_before' => [
							'type'        => 'string',
							'description' => __( 'Limit response to posts modified before a given ISO8601-compliant date.', 'mb-custom-post-type' ),
						],
						'author'          => [
							'type'        => 'array',
							'description' => __( 'Limit result set to posts assigned to specific authors.', 'mb-custom-post-type' ),
							'items'       => [ 'type' => 'integer' ],
							'default'     => [],
						],
						'author_exclude'  => [
							'type'        => 'array',
							'description' => __( 'Ensure result set excludes posts assigned to specific authors.', 'mb-custom-post-type' ),
							'items'       => [ 'type' => 'integer' ],
							'default'     => [],
						],
						'exclude'         => [
							'type'        => 'array',
							'description' => __( 'Ensure result set excludes specific IDs.', 'mb-custom-post-type' ),
							'items'       => [ 'type' => 'integer' ],
							'default'     => [],
						],
						'include'         => [
							'type'        => 'array',
							'description' => __( 'Limit result set to specific IDs.', 'mb-custom-post-type' ),
							'items'       => [ 'type' => 'integer' ],
							'default'     => [],
						],
						'offset'          => [
							'type'        => 'integer',
							'description' => __( 'Offset the result set by a specific number of items.', 'mb-custom-post-type' ),
						],
						'order'           => [
							'type'        => 'string',
							'description' => __( 'Order sort attribute ascending or descending.', 'mb-custom-post-type' ),
							'enum'        => [ 'asc', 'desc' ],
							'default'     => 'desc',
						],
						'orderby'         => [
							'type'        => 'string',
							'description' => __( 'Sort collection by post attribute.', 'mb-custom-post-type' ),
							'enum'        => [ 'author', 'date', 'id', 'include', 'modified', 'parent', 'relevance', 'slug', 'include_slugs', 'title', 'menu_order' ],
							'default'     => 'date',
						],
						'slug'            => [
							'type'        => 'array',
							'description' => __( 'Limit result set to posts with one or more specific slugs.', 'mb-custom-post-type' ),
							'items'       => [ 'type' => 'string' ],
							'default'     => [],
						],
						'status'          => [
							'type'        => 'array',
							'description' => __( 'Limit result set to posts assigned one or more statuses.', 'mb-custom-post-type' ),
							'items'       => [
								'type' => 'string',
								'enum' => [ 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'any' ],
							],
							'default'     => [ 'publish' ],
						],
						'sticky'          => [
							'type'        => 'boolean',
							'description' => __( 'Limit result set to items that are sticky.', 'mb-custom-post-type' ),
						],
						'taxonomies'      => [
							'type'        => 'object',
							'description' => __( 'Limit result set to items with specific terms assigned in taxonomies. Each key is a taxonomy slug; the value is an array of term IDs, or an object with terms/operator/field matching the REST API shape.', 'mb-custom-post-type' ),
						],
						'meta_query'      => [
							'type'        => 'array',
							'description' => __( 'Limit result set by custom fields. Each entry supports key, value, compare, and type.', 'mb-custom-post-type' ),
							'items'       => [
								'type'       => 'object',
								'properties' => [
									'key'     => [ 'type' => 'string' ],
									'value'   => [],
									'compare' => [
										'type' => 'string',
										'enum' => [ '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS', 'NOT EXISTS', 'REGEXP', 'NOT REGEXP', 'RLIKE' ],
									],
									'type'    => [ 'type' => 'string' ],
								],
							],
							'default'     => [],
						],
					],
				],
				'output_schema'       => [
					'type'  => 'array',
					'items' => $this->post_output_schema(),
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
					return $this->execute_get( $input );
				},
			]
		);
	}

	private function register_get_post_type_ability(): void {
		wp_register_ability(
			"meta-box/get-post-type-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s post type', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Get %s post type data.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->read );
				},
				'input_schema'        => [
					'type' => 'object',
				],
				'output_schema'       => $this->post_type_output_schema(),
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
					return $this->execute_get_post_type();
				},
			]
		);
	}

	private function register_create_ability(): void {
		wp_register_ability(
			"meta-box/create-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Create a new %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->create_posts );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'title' ],
					'properties' => [
						'title'          => [
							'type'        => 'string',
							'description' => __( 'Post title.', 'mb-custom-post-type' ),
						],
						'content'        => [
							'type'        => 'string',
							'description' => __( 'Post content.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'excerpt'        => [
							'type'        => 'string',
							'description' => __( 'Post excerpt.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'status'         => [
							'type'        => 'string',
							'description' => __( 'Post status.', 'mb-custom-post-type' ),
							'enum'        => [ 'draft', 'publish', 'pending', 'private' ],
							'default'     => 'draft',
						],
						'slug'           => [
							'type'        => 'string',
							'description' => __( 'Post slug.', 'mb-custom-post-type' ),
						],

						'featured_media' => [
							'type'        => 'integer',
							'description' => __( 'Featured image media ID.', 'mb-custom-post-type' ),
						],
						'parent'         => [
							'type'        => 'integer',
							'description' => __( 'Parent post ID.', 'mb-custom-post-type' ),
							'default'     => 0,
						],
						'menu_order'     => [
							'type'        => 'integer',
							'description' => __( 'Menu order.', 'mb-custom-post-type' ),
							'default'     => 0,
						],
						'comment_status' => [
							'type'        => 'string',
							'description' => __( 'Comment status.', 'mb-custom-post-type' ),
							'enum'        => [ 'open', 'closed' ],
						],
						'ping_status'    => [
							'type'        => 'string',
							'description' => __( 'Ping status.', 'mb-custom-post-type' ),
							'enum'        => [ 'open', 'closed' ],
						],
						'template'       => [
							'type'        => 'string',
							'description' => __( 'Page template.', 'mb-custom-post-type' ),
						],
					],
				],
				'output_schema'       => $this->post_output_schema(),
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
					return $this->execute_create( $input );
				},
			]
		);
	}

	private function register_update_ability(): void {
		wp_register_ability(
			"meta-box/update-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Update an existing %s. Only provided fields are modified.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->edit_post );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'             => [
							'type'        => 'integer',
							'description' => __( 'Post ID to update.', 'mb-custom-post-type' ),
						],
						'title'          => [
							'type'        => 'string',
							'description' => __( 'New post title.', 'mb-custom-post-type' ),
						],
						'content'        => [
							'type'        => 'string',
							'description' => __( 'New post content.', 'mb-custom-post-type' ),
						],
						'excerpt'        => [
							'type'        => 'string',
							'description' => __( 'New post excerpt.', 'mb-custom-post-type' ),
						],
						'status'         => [
							'type'        => 'string',
							'description' => __( 'New post status.', 'mb-custom-post-type' ),
							'enum'        => [ 'draft', 'publish', 'pending', 'private' ],
						],
						'slug'           => [
							'type'        => 'string',
							'description' => __( 'New post slug.', 'mb-custom-post-type' ),
						],

						'featured_media' => [
							'type'        => 'integer',
							'description' => __( 'New featured image media ID.', 'mb-custom-post-type' ),
						],
						'parent'         => [
							'type'        => 'integer',
							'description' => __( 'New parent post ID.', 'mb-custom-post-type' ),
						],
						'menu_order'     => [
							'type'        => 'integer',
							'description' => __( 'New menu order.', 'mb-custom-post-type' ),
						],
						'comment_status' => [
							'type'        => 'string',
							'description' => __( 'New comment status.', 'mb-custom-post-type' ),
							'enum'        => [ 'open', 'closed' ],
						],
						'ping_status'    => [
							'type'        => 'string',
							'description' => __( 'New ping status.', 'mb-custom-post-type' ),
							'enum'        => [ 'open', 'closed' ],
						],
						'template'       => [
							'type'        => 'string',
							'description' => __( 'New page template.', 'mb-custom-post-type' ),
						],
					],
				],
				'output_schema'       => $this->post_output_schema(),
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
					return $this->execute_update( $input );
				},
			]
		);
	}

	private function register_delete_ability(): void {
		wp_register_ability(
			"meta-box/delete-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->delete_post );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'    => [
							'type'        => 'integer',
							'description' => __( 'Post ID to delete.', 'mb-custom-post-type' ),
						],
						'force' => [
							'type'        => 'boolean',
							'description' => __( 'Skip trash and delete permanently.', 'mb-custom-post-type' ),
							'default'     => false,
						],
					],
				],
				'output_schema'       => [
					'type'       => 'object',
					'properties' => [
						'deleted'  => [ 'type' => 'boolean' ],
						'previous' => [
							'type'       => 'object',
							'properties' => [
								'id'    => [ 'type' => 'integer' ],
								'title' => [ 'type' => 'string' ],
							],
						],
					],
				],
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
					return $this->execute_delete( $input );
				},
			]
		);
	}

	private function execute_get( array $input ): array {
		$context = $input['context'] ?? 'view';
		$rest    = rest_get_server();
		$base    = '/' . ( $this->post_type->rest_base ?: $this->slug );

		$controller = new WP_REST_Posts_Controller( $this->slug );

		if ( ! empty( $input['id'] ) ) {
			$request            = new WP_REST_Request( 'GET', $base . '/' . (int) $input['id'] );
			$request['context'] = $context;

			$response = $controller->get_item( $request );
			if ( is_wp_error( $response ) || 404 === $response->get_status() ) {
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

	private function map_input_to_rest_params( array $input ): array {
		$passthrough = [
			'context',
			'page',
			'per_page',
			'search',
			'search_columns',
			'after',
			'before',
			'modified_after',
			'modified_before',
			'author',
			'author_exclude',
			'exclude',
			'include',
			'offset',
			'order',
			'orderby',
			'slug',
			'status',
			'sticky',
			'meta_query',
		];

		$params = [];
		foreach ( $passthrough as $key ) {
			if ( array_key_exists( $key, $input ) ) {
				$params[ $key ] = $input[ $key ];
			}
		}

		if ( ! empty( $input['taxonomies'] ) && is_array( $input['taxonomies'] ) ) {
			foreach ( $input['taxonomies'] as $taxonomy => $value ) {
				$tax_object = get_taxonomy( $taxonomy );
				if ( ! $tax_object ) {
					continue;
				}
				$arg_name            = ! empty( $tax_object->rest_base ) ? $tax_object->rest_base : $taxonomy;
				$params[ $arg_name ] = $value;
			}
		}

		return $params;
	}


	private function execute_get_post_type(): array {
		$post_type = get_post_type_object( $this->slug );

		if ( ! $post_type ) {
			return [];
		}

		return [
			'name'                => $post_type->name,
			'label'               => $post_type->label,
			'labels'              => (array) $post_type->labels,
			'description'         => $post_type->description,
			'public'              => $post_type->public,
			'hierarchical'        => $post_type->hierarchical,
			'exclude_from_search' => $post_type->exclude_from_search,
			'publicly_queryable'  => $post_type->publicly_queryable,
			'embeddable'          => $post_type->embeddable,
			'show_ui'             => $post_type->show_ui,
			'show_in_menu'        => $post_type->show_in_menu,
			'show_in_nav_menus'   => $post_type->show_in_nav_menus,
			'show_in_admin_bar'   => $post_type->show_in_admin_bar,
			'menu_position'       => $post_type->menu_position,
			'menu_icon'           => $post_type->menu_icon ?? null,
			'capability_type'     => $post_type->capability_type,
			'capabilities'        => (array) $post_type->cap,
			'map_meta_cap'        => $post_type->map_meta_cap,
			'supports'            => array_keys( get_all_post_type_supports( $this->slug ) ),
			'taxonomies'          => get_object_taxonomies( $this->slug ),
			'has_archive'         => $post_type->has_archive,
			'rewrite'             => (array) $post_type->rewrite,
			'query_var'           => $post_type->query_var,
			'can_export'          => $post_type->can_export,
			'delete_with_user'    => $post_type->delete_with_user,
			'show_in_rest'        => $post_type->show_in_rest,
			'rest_base'           => $post_type->rest_base,
			'template'            => $post_type->template,
			'template_lock'       => $post_type->template_lock,
		];
	}

	private function execute_create( array $input ): array {
		$args = [
			'post_title'     => sanitize_text_field( $input['title'] ?? '' ),
			'post_content'   => wp_kses_post( $input['content'] ?? '' ),
			'post_excerpt'   => sanitize_textarea_field( $input['excerpt'] ?? '' ),
			'post_status'    => $input['status'] ?? 'draft',
			'post_type'      => $this->slug,
			'post_name'      => isset( $input['slug'] ) ? sanitize_title( $input['slug'] ) : '',

			'menu_order'     => isset( $input['menu_order'] ) ? (int) $input['menu_order'] : 0,
			'comment_status' => isset( $input['comment_status'] ) ? $input['comment_status'] : '',
			'ping_status'    => isset( $input['ping_status'] ) ? $input['ping_status'] : '',
			'post_parent'    => isset( $input['parent'] ) ? (int) $input['parent'] : 0,
			'template'       => isset( $input['template'] ) ? $input['template'] : '',
		];

		$post_id = wp_insert_post( $args, true );

		if ( is_wp_error( $post_id ) ) {
			return [];
		}

		if ( isset( $input['featured_media'] ) ) {
			set_post_thumbnail( $post_id, (int) $input['featured_media'] );
		}

		$post = get_post( $post_id );
		return $this->format_post( $post, $input['context'] ?? 'view' );
	}

	private function execute_update( array $input ): array {
		$post_id = (int) $input['id'];
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return [];
		}

		$args = [ 'ID' => $post_id ];
		if ( isset( $input['title'] ) ) {
			$args['post_title'] = sanitize_text_field( $input['title'] );
		}
		if ( isset( $input['content'] ) ) {
			$args['post_content'] = wp_kses_post( $input['content'] );
		}
		if ( isset( $input['excerpt'] ) ) {
			$args['post_excerpt'] = sanitize_textarea_field( $input['excerpt'] );
		}
		if ( isset( $input['status'] ) ) {
			$args['post_status'] = $input['status'];
		}
		if ( isset( $input['slug'] ) ) {
			$args['post_name'] = sanitize_title( $input['slug'] );
		}

		if ( isset( $input['menu_order'] ) ) {
			$args['menu_order'] = (int) $input['menu_order'];
		}
		if ( isset( $input['comment_status'] ) ) {
			$args['comment_status'] = $input['comment_status'];
		}
		if ( isset( $input['ping_status'] ) ) {
			$args['ping_status'] = $input['ping_status'];
		}
		if ( isset( $input['parent'] ) ) {
			$args['post_parent'] = (int) $input['parent'];
		}
		if ( isset( $input['template'] ) ) {
			$args['template'] = $input['template'];
		}

		$result = wp_update_post( $args, true );

		if ( is_wp_error( $result ) ) {
			return [];
		}

		if ( isset( $input['featured_media'] ) ) {
			set_post_thumbnail( $post_id, (int) $input['featured_media'] );
		}

		$post = get_post( $post_id );
		return $this->format_post( $post, $input['context'] ?? 'view' );
	}

	private function execute_delete( array $input ): array {
		$post_id = (int) $input['id'];
		$post    = get_post( $post_id );

		if ( ! $post ) {
			return [];
		}

		$previous = [
			'id'    => $post->ID,
			'title' => $post->post_title,
		];

		$force  = ! empty( $input['force'] );
		$result = wp_delete_post( $post_id, $force );

		if ( ! $result ) {
			return [];
		}

		return [
			'deleted'  => true,
			'previous' => $previous,
		];
	}

	private function format_post( WP_Post $post, string $context = 'view' ): array {
		$controller         = new WP_REST_Posts_Controller( $post->post_type );
		$request            = new WP_REST_Request( 'GET', '/' . ( $this->post_type->rest_base ?: $this->slug ) . '/' . $post->ID );
		$request['context'] = $context;

		$response = $controller->prepare_item_for_response( $post, $request );

		$data = rest_get_server()->response_to_data( $response, true );

		if ( 'edit' === $context ) {
			$data['edit_link'] = get_edit_post_link( $post->ID, 'raw' );
		}

		return $data;
	}

	private function post_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'id'                 => [ 'type' => 'integer' ],
				'date'               => [ 'type' => 'string' ],
				'date_gmt'           => [ 'type' => 'string' ],
				'guid'               => [
					'type'       => 'object',
					'properties' => [
						'rendered' => [ 'type' => 'string' ],
						'raw'      => [ 'type' => [ 'string', 'null' ] ],
					],
				],
				'modified'           => [ 'type' => 'string' ],
				'modified_gmt'       => [ 'type' => 'string' ],
				'slug'               => [ 'type' => 'string' ],
				'status'             => [ 'type' => 'string' ],
				'type'               => [ 'type' => 'string' ],
				'title'              => [
					'type'       => 'object',
					'properties' => [
						'rendered' => [ 'type' => 'string' ],
						'raw'      => [ 'type' => [ 'string', 'null' ] ],
					],
				],
				'content'            => [
					'type'       => 'object',
					'properties' => [
						'rendered'      => [ 'type' => 'string' ],
						'raw'           => [ 'type' => [ 'string', 'null' ] ],
						'protected'     => [ 'type' => 'boolean' ],
						'block_version' => [ 'type' => [ 'integer', 'null' ] ],
					],
				],
				'excerpt'            => [
					'type'       => 'object',
					'properties' => [
						'rendered'  => [ 'type' => 'string' ],
						'raw'       => [ 'type' => [ 'string', 'null' ] ],
						'protected' => [ 'type' => 'boolean' ],
					],
				],
				'author'             => [ 'type' => 'integer' ],
				'featured_media'     => [ 'type' => 'integer' ],
				'parent'             => [ 'type' => 'integer' ],
				'menu_order'         => [ 'type' => 'integer' ],
				'comment_status'     => [ 'type' => 'string' ],
				'ping_status'        => [ 'type' => 'string' ],
				'template'           => [ 'type' => 'string' ],
				'sticky'             => [ 'type' => 'boolean' ],
				'link'               => [ 'type' => 'string' ],
				'edit_link'          => [ 'type' => [ 'string', 'null' ] ],
				'permalink_template' => [ 'type' => [ 'string', 'null' ] ],
				'generated_slug'     => [ 'type' => [ 'string', 'null' ] ],
				'meta'               => [ 'type' => 'object' ],
				'class_list'         => [ 'type' => 'array' ],
			],
		];
	}

	private function post_type_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'name'                => [ 'type' => 'string' ],
				'label'               => [ 'type' => 'string' ],
				'labels'              => [ 'type' => 'object' ],
				'description'         => [ 'type' => 'string' ],
				'public'              => [ 'type' => 'boolean' ],
				'hierarchical'        => [ 'type' => 'boolean' ],
				'exclude_from_search' => [ 'type' => 'boolean' ],
				'publicly_queryable'  => [ 'type' => 'boolean' ],
				'embeddable'          => [ 'type' => 'boolean' ],
				'show_ui'             => [ 'type' => 'boolean' ],
				'show_in_menu'        => [ 'type' => [ 'boolean', 'string' ] ],
				'show_in_nav_menus'   => [ 'type' => 'boolean' ],
				'show_in_admin_bar'   => [ 'type' => 'boolean' ],
				'menu_position'       => [ 'type' => 'integer' ],
				'menu_icon'           => [ 'type' => 'string' ],
				'capability_type'     => [ 'type' => 'string' ],
				'capabilities'        => [ 'type' => 'object' ],
				'map_meta_cap'        => [ 'type' => 'boolean' ],
				'supports'            => [
					'type'  => 'array',
					'items' => [ 'type' => 'string' ],
				],
				'taxonomies'          => [
					'type'  => 'array',
					'items' => [ 'type' => 'string' ],
				],
				'can_export'          => [ 'type' => 'boolean' ],
				'delete_with_user'    => [ 'type' => 'boolean' ],
				'show_in_rest'        => [ 'type' => 'boolean' ],
				'rest_base'           => [ 'type' => 'string' ],
				'template'            => [ 'type' => 'array' ],
			],
		];
	}
}
