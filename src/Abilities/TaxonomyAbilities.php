<?php
namespace MBCPT\Abilities;

class TaxonomyAbilities {

	public function register( string $slug, $taxonomy, array $settings ): void {
		$singular = $taxonomy->labels->singular_name ?? $slug;
		$label    = $taxonomy->labels->name ?? $slug;

		if ( ! empty( $settings['abilities_get_data'] ) ) {
			$this->register_get_taxonomy_ability( $slug, $singular );
		}
		if ( ! empty( $settings['abilities_get'] ) ) {
			$this->register_get_term_ability( $slug, $singular, $label );
		}
		if ( ! empty( $settings['abilities_create'] ) ) {
			$this->register_create_term_ability( $slug, $singular );
		}
		if ( ! empty( $settings['abilities_update'] ) ) {
			$this->register_update_term_ability( $slug, $singular );
		}
		if ( ! empty( $settings['abilities_delete'] ) ) {
			$this->register_delete_term_ability( $slug, $singular );
		}
	}

	private function register_get_term_ability( string $slug, string $singular, string $label ): void {
		$taxonomy = get_taxonomy( $slug );

		wp_register_ability(
			"meta-box/get-term-{$slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $label ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () use ( $taxonomy ) {
					return current_user_can( $taxonomy->cap->assign_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'id' => [
							'type'        => 'integer',
							'description' => __( 'Term ID to retrieve.', 'mb-custom-post-type' ),
						],
						'search' => [
							'type'        => 'string',
							'description' => __( 'Search keyword.', 'mb-custom-post-type' ),
						],
						'parent' => [
							'type'        => 'integer',
							'description' => __( 'Parent term ID to filter by.', 'mb-custom-post-type' ),
							'default'     => 0,
						],
						'limit' => [
							'type'        => 'integer',
							'description' => __( 'Maximum number of terms to return (1-100).', 'mb-custom-post-type' ),
							'default'     => 10,
						],
						'orderby' => [
							'type'        => 'string',
							'description' => __( 'Order results by.', 'mb-custom-post-type' ),
							'enum'        => [ 'name', 'slug', 'count', 'term_group', 'description', 'term_id' ],
							'default'     => 'name',
						],
						'order' => [
							'type'        => 'string',
							'description' => __( 'Sort direction.', 'mb-custom-post-type' ),
							'enum'        => [ 'ASC', 'DESC' ],
							'default'     => 'ASC',
						],
						'hide_empty' => [
							'type'        => 'boolean',
							'description' => __( 'Hide terms with no posts.', 'mb-custom-post-type' ),
							'default'     => false,
						],
					],
				],
				'output_schema'       => $this->term_output_schema(),
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
				'execute_callback'    => function ( array $input ) use ( $slug ): array {
					return $this->execute_get_terms( $slug, $input );
				},
			]
		);
	}

	private function register_create_term_ability( string $slug, string $singular ): void {
		$taxonomy = get_taxonomy( $slug );

		wp_register_ability(
			"meta-box/create-term-{$slug}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Create a new %s.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () use ( $taxonomy ) {
					return current_user_can( $taxonomy->cap->manage_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'name' ],
					'properties' => [
						'name' => [
							'type'        => 'string',
							'description' => __( 'Term name.', 'mb-custom-post-type' ),
						],
						'slug' => [
							'type'        => 'string',
							'description' => __( 'Term slug.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'description' => [
							'type'        => 'string',
							'description' => __( 'Term description.', 'mb-custom-post-type' ),
							'default'     => '',
						],
						'parent' => [
							'type'        => 'integer',
							'description' => __( 'Parent term ID.', 'mb-custom-post-type' ),
							'default'     => 0,
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
					'mcp'          => [
						'public' => true,
					],
				],
				'execute_callback'    => function ( array $input ) use ( $slug ): array {
					return $this->execute_create_term( $slug, $input );
				},
			]
		);
	}

	private function register_update_term_ability( string $slug, string $singular ): void {
		$taxonomy = get_taxonomy( $slug );

		wp_register_ability(
			"meta-box/update-term-{$slug}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Update an existing %s. Only provided fields are modified.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () use ( $taxonomy ) {
					return current_user_can( $taxonomy->cap->edit_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id' => [
							'type'        => 'integer',
							'description' => __( 'Term ID to update.', 'mb-custom-post-type' ),
						],
						'name' => [
							'type'        => 'string',
							'description' => __( 'New term name.', 'mb-custom-post-type' ),
						],
						'slug' => [
							'type'        => 'string',
							'description' => __( 'New term slug.', 'mb-custom-post-type' ),
						],
						'description' => [
							'type'        => 'string',
							'description' => __( 'New term description.', 'mb-custom-post-type' ),
						],
						'parent' => [
							'type'        => 'integer',
							'description' => __( 'New parent term ID.', 'mb-custom-post-type' ),
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
					'mcp'          => [
						'public' => true,
					],
				],
				'execute_callback'    => function ( array $input ) use ( $slug ): array {
					return $this->execute_update_term( $slug, $input );
				},
			]
		);
	}

	private function register_delete_term_ability( string $slug, string $singular ): void {
		$taxonomy = get_taxonomy( $slug );

		wp_register_ability(
			"meta-box/delete-term-{$slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () use ( $taxonomy ) {
					return current_user_can( $taxonomy->cap->delete_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id' => [
							'type'        => 'integer',
							'description' => __( 'Term ID to delete.', 'mb-custom-post-type' ),
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
								'id'   => [ 'type' => 'integer' ],
								'name' => [ 'type' => 'string' ],
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
				'execute_callback'    => function ( array $input ) use ( $slug ): array {
					return $this->execute_delete_term( $slug, $input );
				},
			]
		);
	}

	private function register_get_taxonomy_ability( string $slug, string $singular ): void {
		$taxonomy = get_taxonomy( $slug );

		wp_register_ability(
			"meta-box/get-taxonomy-{$slug}",
			[
				'label'               => sprintf( __( 'Get %s taxonomy', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'description'         => sprintf( __( 'Get %s taxonomy data.', 'mb-custom-post-type' ), strtolower( $singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () use ( $taxonomy ) {
					return current_user_can( $taxonomy->cap->assign_terms );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'slug' ],
					'properties' => [
						'slug' => [
							'type'        => 'string',
							'description' => __( 'Taxonomy slug.', 'mb-custom-post-type' ),
						],
					],
				],
				'output_schema'       => $this->taxonomy_output_schema(),
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
				'execute_callback'    => function () use ( $slug ): array {
					return $this->execute_get_taxonomy( $slug );
				},
			]
		);
	}

	public function execute_get_terms( string $slug, array $input ): array {
		$args = [
			'taxonomy'   => $slug,
			'hide_empty' => $input['hide_empty'] ?? false,
			'number'     => min( $input['limit'] ?? 10, 100 ),
			'orderby'    => $input['orderby'] ?? 'name',
			'order'      => $input['order'] ?? 'ASC',
		];

		if ( ! empty( $input['id'] ) ) {
			$term = get_term( (int) $input['id'], $slug );
			return $term && ! is_wp_error( $term ) ? [ $this->format_term( $term ) ] : [];
		}

		if ( ! empty( $input['search'] ) ) {
			$args['search'] = sanitize_text_field( $input['search'] );
		}

		if ( isset( $input['parent'] ) ) {
			$args['parent'] = (int) $input['parent'];
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		return array_map( [ $this, 'format_term' ], $terms );
	}

	public function execute_create_term( string $slug, array $input ): array {
		$args = [
			'name'        => sanitize_text_field( $input['name'] ),
			'slug'        => sanitize_title( $input['slug'] ?? '' ),
			'description' => sanitize_textarea_field( $input['description'] ?? '' ),
			'parent'      => (int) ( $input['parent'] ?? 0 ),
		];

		$result = wp_insert_term( $args['name'], $slug, $args );

		if ( is_wp_error( $result ) ) {
			return [];
		}

		$term = get_term( $result['term_id'], $slug );
		return $this->format_term( $term );
	}

	public function execute_update_term( string $slug, array $input ): array {
		$term_id = (int) $input['id'];
		$term    = get_term( $term_id, $slug );

		if ( ! $term || is_wp_error( $term ) ) {
			return [];
		}

		$args = [];
		if ( isset( $input['name'] ) ) {
			$args['name'] = sanitize_text_field( $input['name'] );
		}
		if ( isset( $input['slug'] ) ) {
			$args['slug'] = sanitize_title( $input['slug'] );
		}
		if ( isset( $input['description'] ) ) {
			$args['description'] = sanitize_textarea_field( $input['description'] );
		}
		if ( isset( $input['parent'] ) ) {
			$args['parent'] = (int) $input['parent'];
		}

		$result = wp_update_term( $term_id, $slug, $args );

		if ( is_wp_error( $result ) ) {
			return [];
		}

		$term = get_term( $term_id, $slug );
		return $this->format_term( $term );
	}

	public function execute_delete_term( string $slug, array $input ): array {
		$term_id = (int) $input['id'];
		$term    = get_term( $term_id, $slug );

		if ( ! $term || is_wp_error( $term ) ) {
			return [];
		}

		$previous = [
			'id'   => $term->term_id,
			'name' => $term->name,
		];

		$result = wp_delete_term( $term_id, $slug );

		if ( ! $result ) {
			return [];
		}

		return [
			'deleted'  => true,
			'previous' => $previous,
		];
	}

	public function execute_get_taxonomy( string $slug ): array {
		$taxonomy = get_taxonomy( $slug );

		if ( ! $taxonomy ) {
			return [];
		}

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

	private function format_term( \WP_Term $term ): array {
		return [
			'id'          => $term->term_id,
			'name'        => $term->name,
			'slug'        => $term->slug,
			'description' => $term->description,
			'parent'      => $term->parent,
			'count'       => $term->count,
			'taxonomy'    => $term->taxonomy,
			'link'        => get_term_link( $term ),
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
