<?php
namespace MBCPT\Abilities;

use WP_Post_Type;

class PostTypeAbilities {

	private string $slug;
	private string $singular;
	private string $label;
	private WP_Post_Type $post_type;

	public function register( string $slug, WP_Post_Type $post_type, array $settings ): void {
		$this->slug      = $slug;
		$this->post_type = $post_type;
		$this->singular  = $post_type->labels->singular_name ?? $slug;
		$this->label     = $post_type->labels->name ?? $slug;

		if ( ! empty( $settings['abilities_get_data'] ) ) {
			$this->register_get_post_type_ability();
		}
		if ( ! empty( $settings['abilities_get'] ) ) {
			$this->register_get_ability();
		}
		if ( ! empty( $settings['abilities_create'] ) ) {
			$this->register_create_ability();
		}
		if ( ! empty( $settings['abilities_update'] ) ) {
			$this->register_update_ability();
		}
		if ( ! empty( $settings['abilities_delete'] ) ) {
			$this->register_delete_ability();
		}
	}

	private function register_get_ability(): void {
		wp_register_ability(
			"meta-box/get-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->read );
				},
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
						],
						'orderby' => [
							'type'        => 'string',
							'description' => __( 'Order results by.', 'mb-custom-post-type' ),
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
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'          => [
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
					'type'       => 'object',
					'required'   => [ 'slug' ],
					'properties' => [
						'slug' => [
							'type'        => 'string',
							'description' => __( 'Post type slug.', 'mb-custom-post-type' ),
						],
					],
				],
				'output_schema'       => $this->post_type_output_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'          => [
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
						'title'   => [
							'type'        => 'string',
							'description' => __( 'Post title.', 'mb-custom-post-type' ),
						],
						'content' => [
							'type'        => 'string',
							'description' => __( 'Post content.', 'mb-custom-post-type' ),
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
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
					],
					'mcp'          => [
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
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'          => [
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
					'mcp'          => [
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
		$args = [
			'post_type'   => $this->slug,
			'post_status' => $input['status'] ?? 'publish',
			'numberposts' => min( $input['limit'] ?? 10, 100 ),
			'orderby'     => $input['orderby'] ?? 'date',
			'order'       => $input['order'] ?? 'DESC',
		];

		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );
			return $post && $post->post_type === $this->slug ? [ $this->format_post( $post ) ] : [];
		}

		if ( ! empty( $input['search'] ) ) {
			$args['s'] = sanitize_text_field( $input['search'] );
		}

		$posts = get_posts( $args );

		return array_map( [ $this, 'format_post' ], $posts );
	}

	private function execute_get_post_type(): array {
		$post_type = get_post_type_object( $this->slug );

		if ( ! $post_type ) {
			return [];
		}

		return [
			'name'               => $post_type->name,
			'label'              => $post_type->label,
			'labels'             => (array) $post_type->labels,
			'description'        => $post_type->description,
			'public'             => $post_type->public,
			'hierarchical'       => $post_type->hierarchical,
			'exclude_from_search' => $post_type->exclude_from_search,
			'publicly_queryable' => $post_type->publicly_queryable,
			'embeddable'         => $post_type->embeddable,
			'show_ui'            => $post_type->show_ui,
			'show_in_menu'       => $post_type->show_in_menu,
			'show_in_nav_menus'  => $post_type->show_in_nav_menus,
			'show_in_admin_bar'  => $post_type->show_in_admin_bar,
			'menu_position'      => $post_type->menu_position,
			'menu_icon'          => $post_type->menu_icon ?? null,
			'capability_type'    => $post_type->capability_type,
			'capabilities'       => (array) $post_type->cap,
			'map_meta_cap'       => $post_type->map_meta_cap,
			'supports'           => array_keys( get_all_post_type_supports( $this->slug ) ),
			'taxonomies'         => get_object_taxonomies( $this->slug ),
			'has_archive'        => $post_type->has_archive,
			'rewrite'            => (array) $post_type->rewrite,
			'query_var'          => $post_type->query_var,
			'can_export'         => $post_type->can_export,
			'delete_with_user'   => $post_type->delete_with_user,
			'show_in_rest'       => $post_type->show_in_rest,
			'rest_base'          => $post_type->rest_base,
			'template'           => $post_type->template,
			'template_lock'      => $post_type->template_lock,
		];
	}

	private function execute_create( array $input ): array {
		$args = [
			'post_title'   => sanitize_text_field( $input['title'] ),
			'post_content' => wp_kses_post( $input['content'] ?? '' ),
			'post_excerpt' => sanitize_textarea_field( $input['excerpt'] ?? '' ),
			'post_status'  => $input['status'] ?? 'draft',
			'post_type'    => $this->slug,
		];

		$post_id = wp_insert_post( $args, true );

		if ( is_wp_error( $post_id ) ) {
			return [];
		}

		$post = get_post( $post_id );
		return $this->format_post( $post );
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

		$result = wp_update_post( $args, true );

		if ( is_wp_error( $result ) ) {
			return [];
		}

		$post = get_post( $post_id );
		return $this->format_post( $post );
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
				'show_in_nav_menus'   => [ 'type' => 'boolean' ],
				'show_in_admin_bar'   => [ 'type' => 'boolean' ],
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
