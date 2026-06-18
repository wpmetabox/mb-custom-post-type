<?php
namespace MBCPT\Abilities;

use MBCPT\Register;
use WP_Error;
use WP_Post;
use WP_Post_Type;

abstract class DefinitionAbilities {

	abstract protected function post_type(): string;
	abstract protected function ability_slug(): string;
	abstract protected function ability_slug_plural(): string;
	abstract protected function settings_description(): string;
	abstract protected function label_singular(): string;
	abstract protected function label_plural(): string;

	private ?WP_Post_Type $post_type_object = null;

	public function register(): void {
		$this->register_get();
		$this->register_create();
		$this->register_update();
		$this->register_delete();
	}

	protected function post_type_object(): WP_Post_Type {
		$this->post_type_object ??= get_post_type_object( $this->post_type() );
		return $this->post_type_object;
	}

	protected function singular_label(): string {
		return strtolower( $this->post_type_object()->labels->singular_name );
	}

	protected function plural_label(): string {
		return strtolower( $this->post_type_object()->labels->name );
	}

	protected function register_get(): void {
		wp_register_ability(
			'meta-box/get-' . $this->ability_slug_plural(),
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), $this->label_plural() ),
				'description'         => sprintf( __( 'List or read %s definitions.', 'mb-custom-post-type' ), $this->label_plural() ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'properties' => [
						'page'     => [
							'type'        => 'integer',
							'description' => __( 'Current page of the collection.', 'mb-custom-post-type' ),
							'default'     => 1,
							'minimum'     => 1,
						],
						'per_page' => [
							'type'        => 'integer',
							'description' => __( 'Maximum number of items to be returned in result set.', 'mb-custom-post-type' ),
							'default'     => 10,
							'minimum'     => 1,
							'maximum'     => 100,
						],
						'search'   => [
							'type'        => 'string',
							'description' => __( 'Limit results to those matching a string.', 'mb-custom-post-type' ),
						],
						'slug'     => [
							'type'        => 'array',
							'items'       => [ 'type' => 'string' ],
							'description' => __( 'Limit result set to definitions with one or more specific slugs.', 'mb-custom-post-type' ),
						],
						'include'  => [
							'type'        => 'array',
							'items'       => [ 'type' => 'integer' ],
							'description' => __( 'Limit result set to specific definition IDs.', 'mb-custom-post-type' ),
						],
						'exclude'  => [
							'type'        => 'array',
							'items'       => [ 'type' => 'integer' ],
							'description' => __( 'Ensure result set excludes specific definition IDs.', 'mb-custom-post-type' ),
						],
						'orderby'  => [
							'type'        => 'string',
							'description' => __( 'Sort collection by post attribute.', 'mb-custom-post-type' ),
							'default'     => 'date',
							'enum'        => [ 'author', 'date', 'id', 'include', 'modified', 'parent', 'relevance', 'slug', 'title' ],
						],
						'order'    => [
							'type'        => 'string',
							'description' => __( 'Order sort attribute ascending or descending.', 'mb-custom-post-type' ),
							'default'     => 'desc',
							'enum'        => [ 'asc', 'desc' ],
						],
						'status'   => [
							'type'        => 'array',
							'items'       => [
								'type' => 'string',
								'enum' => [ 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'any' ],
							],
							'description' => __( 'Limit result set to definitions assigned one or more statuses.', 'mb-custom-post-type' ),
							'default'     => [ 'publish' ],
						],
						'id'       => [
							'type'        => 'integer',
							'description' => sprintf( __( '%s definition ID to retrieve a single item.', 'mb-custom-post-type' ), ucfirst( $this->singular_label() ) ),
						],
					],
				],
				'output_schema'       => [
					'type'  => 'array',
					'items' => $this->output_schema(),
				],
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'execute_get' ],
			]
		);
	}

	protected function register_create(): void {
		wp_register_ability(
			'meta-box/create-' . $this->ability_slug(),
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), $this->label_singular() ),
				'description'         => sprintf( __( 'Create a new %s definition.', 'mb-custom-post-type' ), $this->label_singular() ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'title', 'settings' ],
					'properties' => [
						'title'    => [
							'type'        => 'string',
							'description' => sprintf( __( '%s definition title.', 'mb-custom-post-type' ), ucfirst( $this->singular_label() ) ),
						],
						'settings' => [
							'type'        => 'object',
							'description' => sprintf( $this->settings_description(), $this->singular_label() ),
						],
						'status'   => [
							'type'        => 'string',
							'description' => __( 'Post status.', 'mb-custom-post-type' ),
							'enum'        => [ 'publish', 'future', 'draft', 'pending', 'private' ],
							'default'     => 'publish',
						],
					],
				],
				'output_schema'       => $this->output_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_create' ],
			]
		);
	}

	protected function register_update(): void {
		wp_register_ability(
			'meta-box/update-' . $this->ability_slug(),
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), $this->label_singular() ),
				'description'         => sprintf( __( 'Update an existing %s definition. Only provided fields are modified; settings are merged into the existing configuration.', 'mb-custom-post-type' ), $this->label_singular() ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'       => [
							'type'        => 'integer',
							'description' => sprintf( __( '%s definition ID to update.', 'mb-custom-post-type' ), ucfirst( $this->singular_label() ) ),
						],
						'title'    => [
							'type'        => 'string',
							'description' => sprintf( __( '%s definition title.', 'mb-custom-post-type' ), ucfirst( $this->singular_label() ) ),
						],
						'status'   => [
							'type'        => 'string',
							'description' => __( 'Post status.', 'mb-custom-post-type' ),
							'enum'        => [ 'publish', 'future', 'draft', 'pending', 'private' ],
						],
						'settings' => [
							'type'        => 'object',
							'description' => sprintf( __( 'Partial %s settings to merge into the existing configuration.', 'mb-custom-post-type' ), $this->singular_label() ),
						],
					],
				],
				'output_schema'       => $this->output_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_update' ],
			]
		);
	}

	protected function register_delete(): void {
		wp_register_ability(
			'meta-box/delete-' . $this->ability_slug(),
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), $this->label_singular() ),
				'description'         => sprintf( __( 'Delete a %s definition.', 'mb-custom-post-type' ), $this->label_singular() ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => [
						'id'    => [
							'type'        => 'integer',
							'description' => sprintf( __( '%s definition ID to delete.', 'mb-custom-post-type' ), ucfirst( $this->singular_label() ) ),
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
					'required'   => [ 'deleted' ],
					'properties' => [
						'deleted'  => [ 'type' => 'boolean' ],
						'previous' => $this->output_schema(),
					],
				],
				'meta'                => $this->meta( false, true ),
				'execute_callback'    => [ $this, 'execute_delete' ],
			]
		);
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get( array $input ) {
		if ( ! empty( $input['id'] ) ) {
			$post = $this->find_definition( (int) $input['id'] );
			if ( is_wp_error( $post ) ) {
				return $post;
			}
			return [ $this->format_post( $post ) ];
		}

		$posts = get_posts( $this->build_collection_query( $input ) );
		return array_map( [ $this, 'format_post' ], $posts );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_create( array $input ) {
		$title = (string) ( $input['title'] ?? '' );
		if ( $title === '' ) {
			return new WP_Error( 'mbcpt_invalid_title', __( 'Title is required.', 'mb-custom-post-type' ), [ 'status' => 400 ] );
		}

		$settings = $this->extract_settings( $input );

		$post_id = wp_insert_post( [
			'post_type'    => $this->post_type(),
			'post_status'  => (string) ( $input['status'] ?? 'publish' ),
			'post_title'   => $title,
			'post_content' => $this->encode_settings( $settings ),
		], true );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		return $this->format_post( get_post( $post_id ) );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_update( array $input ) {
		$post = $this->find_definition( (int) ( $input['id'] ?? 0 ) );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$post_data = [ 'ID' => $post->ID ];
		if ( isset( $input['title'] ) ) {
			$post_data['post_title'] = (string) $input['title'];
		}
		if ( isset( $input['status'] ) ) {
			$post_data['post_status'] = (string) $input['status'];
		}
		if ( isset( $input['settings'] ) && is_array( $input['settings'] ) ) {
			$existing = json_decode( (string) $post->post_content, true );
			$merged   = is_array( $existing ) ? array_merge( $existing, $input['settings'] ) : $input['settings'];
			Register::sanitize_labels( $merged );
			$post_data['post_content'] = $this->encode_settings( $merged );
		}

		$result = wp_update_post( $post_data, true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return $this->format_post( get_post( $post->ID ) );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_delete( array $input ) {
		$post = $this->find_definition( (int) ( $input['id'] ?? 0 ) );
		if ( is_wp_error( $post ) ) {
			return $post;
		}

		$previous = $this->format_post( $post );
		$result   = wp_delete_post( $post->ID, ! empty( $input['force'] ) );
		if ( ! $result ) {
			return new WP_Error( 'mbcpt_delete_failed', __( 'Could not delete the definition.', 'mb-custom-post-type' ), [ 'status' => 500 ] );
		}

		return [
			'deleted'  => true,
			'previous' => $previous,
		];
	}

	/**
	 * @return WP_Post|WP_Error
	 */
	protected function find_definition( int $id ) {
		$post = $id ? get_post( $id ) : null;
		if ( ! $post || $post->post_type !== $this->post_type() ) {
			return new WP_Error( 'mbcpt_not_found', sprintf( __( '%s definition not found.', 'mb-custom-post-type' ), ucfirst( $this->singular_label() ) ), [ 'status' => 404 ] );
		}
		return $post;
	}

	protected function build_collection_query( array $input ): array {
		$query = [
			'post_type'              => $this->post_type(),
			'post_status'            => $this->normalize_status( $input['status'] ?? 'publish' ),
			'posts_per_page'         => isset( $input['per_page'] ) ? max( 1, (int) $input['per_page'] ) : 10,
			'paged'                  => isset( $input['page'] ) ? max( 1, (int) $input['page'] ) : 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		];

		if ( ! empty( $input['search'] ) ) {
			$query['s'] = (string) $input['search'];
		}
		if ( ! empty( $input['slug'] ) ) {
			$query['post_name__in'] = is_array( $input['slug'] ) ? $input['slug'] : [ (string) $input['slug'] ];
		}
		if ( ! empty( $input['include'] ) ) {
			$query['post__in'] = array_map( 'intval', (array) $input['include'] );
		}
		if ( ! empty( $input['exclude'] ) ) {
			$query['post__not_in'] = array_map( 'intval', (array) $input['exclude'] );
		}
		if ( ! empty( $input['orderby'] ) ) {
			$query['orderby'] = (string) $input['orderby'];
		}
		if ( ! empty( $input['order'] ) ) {
			$query['order'] = strtolower( (string) $input['order'] ) === 'asc' ? 'ASC' : 'DESC';
		}
		return $query;
	}

	protected function normalize_status( $status ): array {
		if ( is_array( $status ) ) {
			return $status;
		}
		if ( $status === 'any' ) {
			return get_post_stati( [ 'show_in_admin_all_list' => true ] );
		}
		return [ (string) $status ];
	}

	protected function extract_settings( array $input ): array {
		if ( empty( $input['settings'] ) || ! is_array( $input['settings'] ) ) {
			return [];
		}
		$settings = $input['settings'];
		Register::sanitize_labels( $settings );
		return $settings;
	}

	protected function format_post( WP_Post $post ): array {
		$settings = json_decode( (string) $post->post_content, true );
		return [
			'id'           => (int) $post->ID,
			'title'        => $post->post_title,
			'slug'         => $post->post_name,
			'status'       => $post->post_status,
			'date'         => $post->post_date,
			'date_gmt'     => $post->post_date_gmt,
			'modified'     => $post->post_modified,
			'modified_gmt' => $post->post_modified_gmt,
			'settings'     => is_array( $settings ) ? $settings : [],
		];
	}

	protected function encode_settings( array $settings ): string {
		return wp_slash( wp_json_encode( $settings ) );
	}

	protected function output_schema(): array {
		return [
			'type'       => 'object',
			'required'   => [ 'id' ],
			'properties' => [
				'id'           => [ 'type' => 'integer' ],
				'title'        => [ 'type' => 'string' ],
				'slug'         => [ 'type' => 'string' ],
				'status'       => [ 'type' => 'string' ],
				'date'         => [ 'type' => 'string' ],
				'date_gmt'     => [ 'type' => 'string' ],
				'modified'     => [ 'type' => 'string' ],
				'modified_gmt' => [ 'type' => 'string' ],
				'settings'     => [ 'type' => 'object' ],
			],
		];
	}

	protected function meta( bool $readonly = false, bool $destructive = false, bool $idempotent = true ): array {
		return [
			'annotations' => [
				'readonly'    => $readonly,
				'destructive' => $destructive,
				'idempotent'  => $idempotent,
			],
			'mcp'         => [ 'public' => true ],
		];
	}

	protected function permission(): callable {
		return static function () {
			return current_user_can( 'manage_options' );
		};
	}
}
