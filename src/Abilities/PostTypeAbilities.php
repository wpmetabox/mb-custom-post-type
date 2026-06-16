<?php
namespace MBCPT\Abilities;

use WP_Post_Type;

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
						'tax_relation'    => [
							'type'        => 'string',
							'description' => __( 'Limit result set based on relationship between multiple taxonomies.', 'mb-custom-post-type' ),
							'enum'        => [ 'AND', 'OR' ],
						],
						'taxonomies'      => [
							'type'        => 'object',
							'description' => __( 'Limit result set to items with specific terms assigned in taxonomies. Each key is a taxonomy slug; each value is an array of term IDs, IDs to exclude, or an object with terms/operator/field.', 'mb-custom-post-type' ),
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

		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );
			if ( ! $post || $post->post_type !== $this->slug ) {
				return [];
			}
			return [ $this->format_post( $post, $context ) ];
		}

		$per_page = isset( $input['per_page'] ) ? max( 1, min( 100, (int) $input['per_page'] ) ) : 10;
		$page     = isset( $input['page'] ) ? max( 1, (int) $input['page'] ) : 1;
		$orderby  = $input['orderby'] ?? 'date';
		$order    = strtoupper( $input['order'] ?? 'desc' );

		$query_args = [
			'post_type'      => $this->slug,
			'post_status'    => $this->parse_status( $input['status'] ?? [ 'publish' ] ),
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => $this->map_orderby( $orderby ),
			'order'          => $order,
			'no_found_rows'  => false,
		];

		if ( ! empty( $input['search'] ) ) {
			$query_args['s'] = sanitize_text_field( $input['search'] );
		}

		if ( ! empty( $input['search_columns'] ) ) {
			$query_args['search_columns'] = array_map( 'sanitize_key', (array) $input['search_columns'] );
		}

		$date_query = $this->build_date_query( $input );
		if ( $date_query ) {
			$query_args['date_query'] = $date_query;
		}

		if ( ! empty( $input['author'] ) ) {
			$query_args['author__in'] = array_map( 'intval', (array) $input['author'] );
		}
		if ( ! empty( $input['author_exclude'] ) ) {
			$query_args['author__not_in'] = array_map( 'intval', (array) $input['author_exclude'] );
		}

		if ( ! empty( $input['exclude'] ) ) {
			$query_args['post__not_in'] = array_map( 'intval', (array) $input['exclude'] );
		}
		if ( ! empty( $input['include'] ) ) {
			$include                = array_map( 'intval', (array) $input['include'] );
			$query_args['post__in'] = $include;
			$query_args['orderby']  = 'post__in';
		}

		if ( isset( $input['offset'] ) ) {
			$query_args['offset'] = max( 0, (int) $input['offset'] );
		}

		if ( ! empty( $input['slug'] ) ) {
			$query_args['post_name__in'] = array_map( 'sanitize_title', (array) $input['slug'] );
			if ( 'slug' !== $orderby && 'include_slugs' !== $orderby ) {
				$query_args['orderby'] = 'post_name__in';
			}
		}

		if ( ! empty( $input['sticky'] ) ) {
			$query_args['ignore_sticky_posts'] = false;
			$sticky                            = get_option( 'sticky_posts', [] );
			if ( ! empty( $sticky ) ) {
				$query_args['post__in'] = ! empty( $query_args['post__in'] )
					? array_intersect( $query_args['post__in'], $sticky )
					: $sticky;
			}
		}

		$tax_query = $this->build_tax_query( $input );
		if ( $tax_query ) {
			$query_args['tax_query'] = $tax_query;
		}

		$meta_query = $this->build_meta_query( $input );
		if ( $meta_query ) {
			$query_args['meta_query'] = $meta_query;
		}

		$query = new \WP_Query( $query_args );
		$posts = $query->posts;

		return array_map( function ( $post ) use ( $context ) {
			return $this->format_post( $post, $context );
		}, $posts );
	}

	private function parse_status( $status ): array {
		$statuses = array_map( 'sanitize_key', (array) $status );
		$valid    = [ 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'any' ];
		$statuses = array_intersect( $statuses, $valid );
		return empty( $statuses ) ? [ 'publish' ] : $statuses;
	}

	private function map_orderby( string $orderby ): string {
		$map = [
			'id'            => 'ID',
			'include'       => 'post__in',
			'include_slugs' => 'post_name__in',
			'relevance'     => 'relevance',
		];
		return $map[ $orderby ] ?? $orderby;
	}

	private function build_date_query( array $input ): array {
		$clauses = [];
		$map     = [
			'after'           => 'after',
			'before'          => 'before',
			'modified_after'  => 'modified_after',
			'modified_before' => 'modified_before',
		];
		foreach ( $map as $key => $column ) {
			if ( empty( $input[ $key ] ) ) {
				continue;
			}
			try {
				$clauses[] = [
					'after'     => 'modified_after' === $column || 'modified_before' === $column ? null : $input[ $key ],
					'before'    => 'modified_after' === $column || 'modified_before' === $column ? null : $input[ $key ],
					'column'    => 'modified_after' === $column || 'modified_before' === $column ? 'post_modified' : 'post_date',
					'inclusive' => true,
				];
			} catch ( \Exception $e ) {
				continue;
			}
		}
		return $clauses;
	}

	private function build_tax_query( array $input ): array {
		if ( empty( $input['taxonomies'] ) || ! is_array( $input['taxonomies'] ) ) {
			return [];
		}

		$tax_query = [];
		foreach ( $input['taxonomies'] as $taxonomy => $value ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}
			$clauses = $this->parse_tax_term_clause( $taxonomy, $value );
			if ( $clauses ) {
				$tax_query[] = $clauses;
			}
		}

		if ( count( $tax_query ) > 1 && ! empty( $input['tax_relation'] ) ) {
			$relation              = strtoupper( (string) $input['tax_relation'] );
			$valid                 = [ 'AND', 'OR' ];
			$tax_query['relation'] = in_array( $relation, $valid, true ) ? $relation : 'AND';
		}

		return $tax_query;
	}

	private function parse_tax_term_clause( string $taxonomy, $value ): ?array {
		if ( is_array( $value ) && $this->is_associative( $value ) ) {
			$clause = [
				'taxonomy' => $taxonomy,
				'field'    => 'term_id',
				'terms'    => [],
				'operator' => 'IN',
			];
			if ( isset( $value['terms'] ) ) {
				$clause['terms'] = array_map( 'intval', (array) $value['terms'] );
			} elseif ( isset( $value['term_id'] ) ) {
				$clause['terms'] = array_map( 'intval', (array) $value['term_id'] );
			}
			if ( isset( $value['exclude'] ) ) {
				$clause['terms']    = array_map( 'intval', (array) $value['exclude'] );
				$clause['operator'] = 'NOT IN';
			}
			if ( isset( $value['operator'] ) ) {
				$clause['operator'] = (string) $value['operator'];
			}
			if ( isset( $value['field'] ) ) {
				$clause['field'] = (string) $value['field'];
			}
			if ( empty( $clause['terms'] ) ) {
				return null;
			}
			return $clause;
		}

		$terms = array_map( 'intval', (array) $value );
		if ( empty( $terms ) ) {
			return null;
		}
		return [
			'taxonomy' => $taxonomy,
			'field'    => 'term_id',
			'terms'    => $terms,
		];
	}

	private function build_meta_query( array $input ): array {
		if ( empty( $input['meta_query'] ) || ! is_array( $input['meta_query'] ) ) {
			return [];
		}

		$meta_query = [];
		foreach ( $input['meta_query'] as $clause ) {
			if ( ! is_array( $clause ) || empty( $clause['key'] ) ) {
				continue;
			}
			$built = [
				'key' => sanitize_key( $clause['key'] ),
			];
			if ( isset( $clause['value'] ) ) {
				$built['value'] = $clause['value'];
			}
			if ( isset( $clause['compare'] ) ) {
				$built['compare'] = (string) $clause['compare'];
			}
			if ( isset( $clause['type'] ) ) {
				$built['type'] = (string) $clause['type'];
			}
			$meta_query[] = $built;
		}

		if ( count( $meta_query ) > 1 ) {
			$meta_query['relation'] = strtoupper( (string) ( $input['meta_relation'] ?? 'AND' ) );
		}

		return $meta_query;
	}

	private function is_associative( array $arr ): bool {
		if ( [] === $arr ) {
			return false;
		}
		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

	private function render_post_content( \WP_Post $post ): string {
		if ( ! empty( $post->post_password ) ) {
			return '';
		}
		return apply_filters( 'the_content', $post->post_content );
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

	private function format_post( \WP_Post $post, string $context = 'view' ): array {
		$data = [
			'id'             => $post->ID,
			'date'           => $post->post_date,
			'date_gmt'       => $post->post_date_gmt,
			'guid'           => [ 'rendered' => $post->guid ],
			'modified'       => $post->post_modified,
			'modified_gmt'   => $post->post_modified_gmt,
			'slug'           => $post->post_name,
			'status'         => $post->post_status,
			'type'           => $post->post_type,
			'title'          => [ 'rendered' => get_the_title( $post ) ],
			'content'        => [
				'rendered'  => $this->render_post_content( $post ),
				'protected' => ! empty( $post->post_password ),
			],
			'excerpt'        => [
				'rendered'  => get_the_excerpt( $post ),
				'protected' => ! empty( $post->post_password ),
			],
			'author'         => (int) $post->post_author,
			'featured_media' => (int) get_post_thumbnail_id( $post->ID ),
			'parent'         => (int) $post->post_parent,
			'menu_order'     => (int) $post->menu_order,
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'template'       => get_page_template_slug( $post ) ?: '',
			'sticky'         => is_sticky( $post->ID ),
			'link'           => get_permalink( $post->ID ),
		];

		if ( 'edit' === $context ) {
			$data['edit_link'] = get_edit_post_link( $post->ID, 'raw' );
		} else {
			$data['edit_link'] = null;
		}

		return $data;
	}

	private function post_output_schema(): array {
		return [
			'type'       => 'object',
			'properties' => [
				'id'             => [ 'type' => 'integer' ],
				'date'           => [ 'type' => 'string' ],
				'date_gmt'       => [ 'type' => 'string' ],
				'guid'           => [
					'type'       => 'object',
					'properties' => [
						'rendered' => [ 'type' => 'string' ],
					],
				],
				'modified'       => [ 'type' => 'string' ],
				'modified_gmt'   => [ 'type' => 'string' ],
				'slug'           => [ 'type' => 'string' ],
				'status'         => [ 'type' => 'string' ],
				'type'           => [ 'type' => 'string' ],
				'title'          => [
					'type'       => 'object',
					'properties' => [
						'rendered' => [ 'type' => 'string' ],
					],
				],
				'content'        => [
					'type'       => 'object',
					'properties' => [
						'rendered'  => [ 'type' => 'string' ],
						'protected' => [ 'type' => 'boolean' ],
					],
				],
				'excerpt'        => [
					'type'       => 'object',
					'properties' => [
						'rendered'  => [ 'type' => 'string' ],
						'protected' => [ 'type' => 'boolean' ],
					],
				],
				'author'         => [ 'type' => 'integer' ],
				'featured_media' => [ 'type' => 'integer' ],
				'parent'         => [ 'type' => 'integer' ],
				'menu_order'     => [ 'type' => 'integer' ],
				'comment_status' => [ 'type' => 'string' ],
				'ping_status'    => [ 'type' => 'string' ],
				'template'       => [ 'type' => 'string' ],
				'sticky'         => [ 'type' => 'boolean' ],
				'link'           => [ 'type' => 'string' ],
				'edit_link'      => [ 'type' => [ 'string', 'null' ] ],
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
