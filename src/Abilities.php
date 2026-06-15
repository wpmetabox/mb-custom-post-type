<?php
namespace MBCPT;

use MetaBox\Support\Data;
use WP_Post_Type;

class Abilities {

	public function __construct() {
		if ( ! class_exists( 'WP_Ability' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', [ $this, 'register_category' ] );
		add_action( 'wp_abilities_api_init', [ $this, 'register_abilities' ] );
	}

	public function register_category(): void {
		if ( wp_has_ability_category( 'meta-box' ) ) {
			return;
		}

		wp_register_ability_category(
			'meta-box',
			[
				'label'       => __( 'Meta Box', 'mb-custom-post-type' ),
				'description' => __( 'Abilities for Meta Box data (post types, taxonomies, fields, etc.).', 'mb-custom-post-type' ),
			]
		);
	}

	public function register_abilities(): void {
		$post_types = Data::get_post_types();

		foreach ( $post_types as $slug => $post_type ) {
			if ( empty( $post_type->abilities ) ) {
				continue;
			}
			$this->register_post_type_abilities( $slug, $post_type );
		}
	}

	private function register_post_type_abilities( string $slug, WP_Post_Type $post_type ): void {
		$singular = $post_type->labels->singular_name ?? $slug;
		$label    = $post_type->labels->name ?? $slug;

		$this->register_get_ability( $slug, $singular, $label );
		$this->register_create_ability( $slug, $singular );
		$this->register_update_ability( $slug, $singular );
		$this->register_delete_ability( $slug, $singular );
		$this->register_get_post_type_ability( $slug, $singular );
	}

	private function register_get_ability( string $slug, string $singular, string $label ): void {
		wp_register_ability(
			"meta-box/get-post-{$slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $label ) ),
				'category'            => 'meta-box',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'Post ID to retrieve.', 'mb-custom-post-type' ),
						],
						'search'  => [
							'type'        => 'string',
							'description' => __( 'Search keyword.', 'mb-custom-post-type' ),
						],
						'status'  => [
							'type'        => 'string',
							'description' => __( 'Post status to filter by.', 'mb-custom-post-type' ),
							'enum'        => [ 'publish', 'draft', 'pending', 'private' ],
							'default'     => 'publish',
						],
						'limit'   => [
							'type'        => 'integer',
							'description' => __( 'Maximum number of posts to return (1-100).', 'mb-custom-post-type' ),
							'default'     => 10,
							'minimum'     => 1,
							'maximum'     => 100,
						],
						'orderby' => [
							'type'        => 'string',
							'description' => __( 'Field to order results by.', 'mb-custom-post-type' ),
							'enum'        => [ 'date', 'title', 'modified', 'ID' ],
							'default'     => 'date',
						],
						'order'   => [
							'type'        => 'string',
							'description' => __( 'Sort direction.', 'mb-custom-post-type' ),
							'enum'        => [ 'DESC', 'ASC' ],
							'default'     => 'DESC',
						],
					],
				],
				'output_schema'       => $this->post_output_schema(),
				'permission_callback' => static function () {
					return current_user_can( 'read' );
				},
				'execute_callback'    => function ( $input = [] ) use ( $slug ) {
					return $this->execute_get( $slug, $input );
				},
				'meta'                => [
					'mcp'          => [ 'public' => true ],
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
				],
			]
		);
	}

	private function register_get_post_type_ability( string $slug, string $singular ): void {
		wp_register_ability(
			"meta-box/get-post-type-{$slug}",
			[
				'label'               => sprintf( __( 'Get %s post type', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Get %s post type data.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [],
				],
				'output_schema'       => $this->post_type_output_schema(),
				'permission_callback' => static function () {
					return current_user_can( 'read' );
				},
				'execute_callback'    => function () use ( $slug ) {
					return $this->execute_get_post_type( $slug );
				},
				'meta'                => [
					'mcp'          => [ 'public' => true ],
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
				],
			]
		);
	}

	private function register_create_ability( string $slug, string $singular ): void {
		wp_register_ability(
			"meta-box/create-post-{$slug}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Create a new %s.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'title' ],
					'properties' => [
						'title'   => [
							'type'        => 'string',
							'description' => __( 'Post title.', 'mb-custom-post-type' ),
						],
						'content' => [
							'type'        => 'string',
							'description' => __( 'Post content (HTML or block markup).', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'excerpt' => [
							'type'        => 'string',
							'description' => __( 'Post excerpt.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'status'  => [
							'type'        => 'string',
							'description' => __( 'Post status.', 'mb-custom-post-type' ),
							'enum'        => [ 'draft', 'publish', 'pending', 'private' ],
							'default'     => 'draft',
						],
					],
				],
				'output_schema'       => $this->post_output_schema(),
				'permission_callback' => function () use ( $slug ) {
					$pto = get_post_type_object( $slug );
					return $pto && current_user_can( $pto->cap->create_posts );
				},
				'execute_callback'    => function ( $input = [] ) use ( $slug ) {
					return $this->execute_create( $slug, $input );
				},
				'meta'                => [
					'mcp'          => [ 'public' => true ],
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
					],
				],
			]
		);
	}

	private function register_update_ability( string $slug, string $singular ): void {
		wp_register_ability(
			"meta-box/update-post-{$slug}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Update an existing %s. Only provided fields are modified.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'Post ID to update.', 'mb-custom-post-type' ),
						],
						'title'   => [
							'type'        => 'string',
							'description' => __( 'New post title.', 'mb-custom-post-type' ),
						],
						'content' => [
							'type'        => 'string',
							'description' => __( 'New post content.', 'mb-custom-post-type' ),
						],
						'excerpt' => [
							'type'        => 'string',
							'description' => __( 'New post excerpt.', 'mb-custom-post-type' ),
						],
						'status'  => [
							'type'        => 'string',
							'description' => __( 'New post status.', 'mb-custom-post-type' ),
							'enum'        => [ 'draft', 'publish', 'pending', 'private' ],
						],
					],
				],
				'output_schema'       => $this->post_output_schema(),
				'permission_callback' => function ( $input = [] ) {
					$post_id = isset( $input['id'] ) ? (int) $input['id'] : 0;
					return $post_id > 0 && current_user_can( 'edit_post', $post_id );
				},
				'execute_callback'    => function ( $input = [] ) use ( $slug ) {
					return $this->execute_update( $slug, $input );
				},
				'meta'                => [
					'mcp'          => [ 'public' => true ],
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					],
				],
			]
		);
	}

	private function register_delete_ability( string $slug, string $singular ): void {
		wp_register_ability(
			"meta-box/delete-post-{$slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
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
							'description' => __( 'Permanently delete (true) or trash (false).', 'mb-custom-post-type' ),
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
				'permission_callback' => function ( $input = [] ) {
					$post_id = isset( $input['id'] ) ? (int) $input['id'] : 0;
					return $post_id > 0 && current_user_can( 'delete_post', $post_id );
				},
				'execute_callback'    => function ( $input = [] ) use ( $slug ) {
					return $this->execute_delete( $input );
				},
				'meta'                => [
					'mcp'          => [ 'public' => true ],
					'show_in_rest' => true,
					'annotations'  => [
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => false,
					],
				],
			]
		);
	}

	public function execute_get( string $slug, array $input ): array {
		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );
			if ( ! $post || $post->post_type !== $slug ) {
				return [];
			}
			return [ $this->format_post( $post ) ];
		}

		$args = [
			'post_type'      => $slug,
			'posts_per_page' => min( absint( $input['limit'] ?? 10 ), 100 ),
			'orderby'        => sanitize_key( $input['orderby'] ?? 'date' ),
			'order'          => strtoupper( $input['order'] ?? 'DESC' ) === 'ASC' ? 'ASC' : 'DESC',
		];

		$status = sanitize_key( $input['status'] ?? 'publish' );
		if ( in_array( $status, [ 'publish', 'draft', 'pending', 'private' ], true ) ) {
			$args['post_status'] = $status;
		}

		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}

		$query = new \WP_Query( $args );
		$posts = [];

		foreach ( $query->posts as $post ) {
			if ( current_user_can( 'read_post', $post->ID ) ) {
				$posts[] = $this->format_post( $post );
			}
		}

		return $posts;
	}

	public function execute_get_post_type( string $slug ): array {
		$post_type = get_post_type_object( $slug );

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
			'supports'            => array_keys( get_all_post_type_supports( $slug ) ),
			'taxonomies'          => get_object_taxonomies( $slug ),
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

	public function execute_create( string $slug, array $input ): array {
		$post_id = wp_insert_post( [
			'post_type'    => $slug,
			'post_title'   => sanitize_text_field( $input['title'] ),
			'post_content' => wp_kses_post( $input['content'] ?? '' ),
			'post_excerpt' => sanitize_textarea_field( $input['excerpt'] ?? '' ),
			'post_status'  => sanitize_key( $input['status'] ?? 'draft' ),
		], true );

		if ( is_wp_error( $post_id ) ) {
			return [];
		}

		return $this->format_post( get_post( $post_id ) );
	}

	public function execute_update( string $slug, array $input ): array {
		$post_id = (int) $input['id'];
		$post    = get_post( $post_id );

		if ( ! $post || $post->post_type !== $slug ) {
			return [];
		}

		$postarr = [ 'ID' => $post_id ];

		if ( isset( $input['title'] ) ) {
			$postarr['post_title'] = sanitize_text_field( $input['title'] );
		}
		if ( isset( $input['content'] ) ) {
			$postarr['post_content'] = wp_kses_post( $input['content'] );
		}
		if ( isset( $input['excerpt'] ) ) {
			$postarr['post_excerpt'] = sanitize_textarea_field( $input['excerpt'] );
		}
		if ( isset( $input['status'] ) ) {
			$status = sanitize_key( $input['status'] );
			if ( in_array( $status, [ 'draft', 'publish', 'pending', 'private' ], true ) ) {
				$postarr['post_status'] = $status;
			}
		}

		$result = wp_update_post( $postarr, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->format_post( get_post( $post_id ) );
	}

	public function execute_delete( array $input ): array {
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

	private function format_post( \WP_Post $post ): array {
		return [
			'id'        => $post->ID,
			'title'     => $post->post_title,
			'content'   => $post->post_content,
			'excerpt'   => $post->post_excerpt,
			'status'    => $post->post_status,
			'type'      => $post->post_type,
			'date'      => $post->post_date,
			'modified'  => $post->post_modified,
			'link'      => get_permalink( $post->ID ),
			'edit_link' => get_edit_post_link( $post->ID, 'raw' ),
		];
	}

	private function post_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'id'        => [ 'type' => 'integer' ],
				'title'     => [ 'type' => 'string' ],
				'content'   => [ 'type' => 'string' ],
				'excerpt'   => [ 'type' => 'string' ],
				'status'    => [ 'type' => 'string' ],
				'type'      => [ 'type' => 'string' ],
				'date'      => [ 'type' => 'string' ],
				'modified'  => [ 'type' => 'string' ],
				'link'      => [ 'type' => 'string' ],
				'edit_link' => [ 'type' => 'string' ],
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
				'has_archive'         => [ 'type' => [ 'boolean', 'string' ] ],
				'rewrite'             => [ 'type' => [ 'boolean', 'object' ] ],
				'query_var'           => [ 'type' => [ 'boolean', 'string' ] ],
				'can_export'          => [ 'type' => 'boolean' ],
				'delete_with_user'    => [ 'type' => 'boolean' ],
				'show_in_rest'        => [ 'type' => 'boolean' ],
				'rest_base'           => [ 'type' => [ 'boolean', 'string' ] ],
				'template'            => [ 'type' => 'array' ],
				'template_lock'       => [ 'type' => [ 'boolean', 'string' ] ],
			],
		];
	}
}
