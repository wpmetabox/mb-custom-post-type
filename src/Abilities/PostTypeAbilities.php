<?php
namespace MBCPT\Abilities;

use WP_Error;
use WP_Post_Type;
use WP_REST_Posts_Controller;
use WP_REST_Post_Types_Controller;
use WP_REST_Request;
use WP_REST_Server;

class PostTypeAbilities {

	private string $slug;
	private string $singular;
	private string $label;
	private WP_Post_Type $post_type;
	private array $settings;
	private ?WP_REST_Posts_Controller $controller = null;

	private const ABILITY_MAP = [
		'abilities_get_data' => 'register_get_metadata',
		'abilities_get'      => 'register_get',
		'abilities_create'   => 'register_create',
		'abilities_update'   => 'register_update',
		'abilities_delete'   => 'register_delete',
	];

	public function __construct( string $slug, WP_Post_Type $post_type, array $settings ) {
		$this->slug      = $slug;
		$this->post_type = $post_type;
		$this->singular  = $post_type->labels->singular_name ?? $slug;
		$this->label     = $post_type->labels->name ?? $slug;
		$this->settings  = $settings;
	}

	public function register(): void {
		foreach ( self::ABILITY_MAP as $key => $method ) {
			if ( ! empty( $this->settings[ $key ] ) ) {
				$this->$method();
			}
		}
	}

	private function register_get(): void {
		wp_register_ability(
			"meta-box/get-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->post_type->cap->read ),
				'input_schema'        => [
					'type'       => 'object',
					'properties' => $this->collection_input_schema(),
				],
				'output_schema'       => $this->items_schema(),
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'execute_get_posts' ],
			]
		);
	}

	private function register_get_metadata(): void {
		wp_register_ability(
			"meta-box/get-post-type-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s post type', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Get %s post type data.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->post_type->cap->read ),
				'input_schema'        => [ 'type' => 'object' ],
				'output_schema'       => ( new WP_REST_Post_Types_Controller( $this->slug ) )->get_item_schema(),
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'execute_get_post_type' ],
			]
		);
	}

	private function register_create(): void {
		wp_register_ability(
			"meta-box/create-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Create a new %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->post_type->cap->create_posts ),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'title' ],
					'properties' => array_merge(
						$this->flatten_string_fields( $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ) ),
						[ 'context' => $this->context_param() ]
					),
				],
				'output_schema'       => $this->output_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_create_post' ],
			]
		);
	}

	private function register_update(): void {
		wp_register_ability(
			"meta-box/update-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Update an existing %s. Only provided fields are modified.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->post_type->cap->edit_post ),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => $this->flatten_string_fields( $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ) + [
						'id'      => [
							'type'        => 'integer',
							'description' => __( 'Post ID to update.', 'mb-custom-post-type' ),
						],
						'context' => $this->context_param(),
					] ),
				],
				'output_schema'       => $this->output_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_update_post' ],
			]
		);
	}

	private function flatten_string_fields( array $args ): array {
		foreach ( [ 'title', 'content', 'excerpt' ] as $field ) {
			if ( isset( $args[ $field ]['properties']['raw']['type'] ) ) {
				$args[ $field ] = [
					'type'        => 'string',
					'description' => $args[ $field ]['description'] ?? '',
				];
			}
		}
		return $args;
	}

	private function register_delete(): void {
		wp_register_ability(
			"meta-box/delete-post-{$this->slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->post_type->cap->delete_post ),
				'input_schema'        => $this->delete_input_schema( 'Post' ),
				'output_schema'       => $this->delete_output_schema(),
				'meta'                => $this->meta( false, true ),
				'execute_callback'    => [ $this, 'execute_delete_post' ],
			]
		);
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get_posts( array $input ) {
		$context = $input['context'] ?? 'view';

		if ( ! empty( $input['id'] ) ) {
			$post = get_post( (int) $input['id'] );
			if ( ! $post || $this->slug !== $post->post_type ) {
				return new WP_Error( 'mb_cpt_not_found', __( 'Post not found.', 'mb-custom-post-type' ) );
			}
			return [ $this->format_post( $post, $context ) ];
		}

		$query = [
			'post_type'      => $this->slug,
			'post_status'    => $input['status'] ?? 'any',
			'posts_per_page' => isset( $input['per_page'] ) ? (int) $input['per_page'] : 10,
			'paged'          => isset( $input['page'] ) ? (int) $input['page'] : 1,
		];
		if ( ! empty( $input['search'] ) ) {
			$query['s'] = $input['search'];
		}
		if ( ! empty( $input['author'] ) ) {
			$query['author'] = (int) $input['author'];
		}
		if ( ! empty( $input['parent'] ) ) {
			$query['post_parent'] = (int) $input['parent'];
		}
		if ( ! empty( $input['include'] ) ) {
			$query['post__in'] = array_map( 'intval', (array) $input['include'] );
		}
		if ( ! empty( $input['exclude'] ) ) {
			$query['post__not_in'] = array_map( 'intval', (array) $input['exclude'] );
		}
		if ( ! empty( $input['orderby'] ) ) {
			$query['orderby'] = $input['orderby'];
			$query['order']   = $input['order'] ?? 'DESC';
		}

		$posts = get_posts( $query );
		return array_map( fn( $p ) => $this->format_post( $p, $context ), $posts );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get_post_type() {
		$controller = new WP_REST_Post_Types_Controller( $this->slug );
		$request    = new WP_REST_Request( 'GET', '/types/' . $this->slug );
		$response   = $controller->get_item( $request );
		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_create_post( array $input ) {
		$postarr = $this->build_postarr( $input );
		$id      = wp_insert_post( $postarr, true );
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$this->apply_terms_and_meta( $id, $input );
		return $this->format_post( get_post( $id ), $input['context'] ?? 'view' );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_update_post( array $input ) {
		$id = (int) ( $input['id'] ?? 0 );
		if ( ! $id ) {
			return new WP_Error( 'mb_cpt_invalid_id', __( 'Invalid post ID.', 'mb-custom-post-type' ) );
		}
		$existing = get_post( $id );
		if ( ! $existing || $this->slug !== $existing->post_type ) {
			return new WP_Error( 'mb_cpt_not_found', __( 'Post not found.', 'mb-custom-post-type' ) );
		}
		$postarr       = $this->build_postarr( $input );
		$postarr['ID'] = $id;
		$result        = wp_update_post( $postarr, true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$this->apply_terms_and_meta( $id, $input );
		return $this->format_post( get_post( $id ), $input['context'] ?? 'view' );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_delete_post( array $input ) {
		$id   = (int) ( $input['id'] ?? 0 );
		$post = get_post( $id );
		if ( ! $post || $this->slug !== $post->post_type ) {
			return new WP_Error( 'mb_cpt_not_found', __( 'Post not found.', 'mb-custom-post-type' ) );
		}
		$previous = $this->format_post( $post, $input['context'] ?? 'view' );
		$result   = wp_delete_post( $id, ! empty( $input['force'] ) );
		if ( ! $result ) {
			return new WP_Error( 'mb_cpt_delete_failed', __( 'Could not delete post.', 'mb-custom-post-type' ) );
		}
		return [
			'deleted'  => true,
			'previous' => $previous,
		];
	}

	private function controller(): WP_REST_Posts_Controller {
		$this->controller ??= new WP_REST_Posts_Controller( $this->slug );
		return $this->controller;
	}

	private function permission( string $cap ): callable {
		return function ( $input = [] ) use ( $cap ) {
			$input = is_array( $input ) ? $input : [];
			return ! empty( $input['id'] )
				? current_user_can( $cap, (int) $input['id'] )
				: current_user_can( $cap );
		};
	}

	private function meta( bool $readonly = false, bool $destructive = false, bool $idempotent = true ): array {
		return [
			'annotations' => [
				'readonly'    => $readonly,
				'destructive' => $destructive,
				'idempotent'  => $idempotent,
			],
			'mcp'         => [ 'public' => true ],
		];
	}

	private function collection_input_schema(): array {
		$properties            = $this->controller()->get_collection_params();
		$properties['id']      = [
			'type'        => 'integer',
			'description' => __( 'Post ID to retrieve a single item.', 'mb-custom-post-type' ),
		];
		$properties['context'] = $this->context_param();
		return $properties;
	}

	private function delete_input_schema( string $label ): array {
		return [
			'type'       => 'object',
			'required'   => [ 'id' ],
			'properties' => [
				'id'    => [
					'type'        => 'integer',
					'description' => sprintf( __( '%s ID to delete.', 'mb-custom-post-type' ), $label ),
				],
				'force' => [
					'type'        => 'boolean',
					'description' => __( 'Skip trash and delete permanently.', 'mb-custom-post-type' ),
					'default'     => false,
				],
			],
		];
	}

	private function delete_output_schema(): array {
		return [
			'type'       => 'object',
			'required'   => [ 'deleted' ],
			'properties' => [
				'deleted'  => [ 'type' => 'boolean' ],
				'previous' => $this->output_schema(),
			],
		];
	}

	private function items_schema(): array {
		return [
			'type'  => 'array',
			'items' => $this->output_schema(),
		];
	}

	private function output_schema(): array {
		$schema = $this->controller()->get_item_schema();
		if ( isset( $schema['properties']['status']['enum'] ) ) {
			$schema['properties']['status']['enum'][] = 'trash';
		}
		return $schema;
	}

	private function context_param(): array {
		return [
			'type'        => 'string',
			'description' => __( 'Scope under which the request is made.', 'mb-custom-post-type' ),
			'enum'        => [ 'view', 'embed', 'edit' ],
			'default'     => 'view',
		];
	}

	private function build_postarr( array $input ): array {
		$map     = [
			'title'          => 'post_title',
			'content'        => 'post_content',
			'excerpt'        => 'post_excerpt',
			'status'         => 'post_status',
			'password'       => 'post_password',
			'name'           => 'post_name',
			'slug'           => 'post_name',
			'parent'         => 'post_parent',
			'menu_order'     => 'menu_order',
			'comment_status' => 'comment_status',
			'ping_status'    => 'ping_status',
			'author'         => 'post_author',
			'date'           => 'post_date',
			'date_gmt'       => 'post_date_gmt',
		];
		$postarr = [ 'post_type' => $this->slug ];
		foreach ( $map as $in => $out ) {
			if ( array_key_exists( $in, $input ) ) {
				$postarr[ $out ] = $input[ $in ];
			}
		}
		return $postarr;
	}

	private function apply_terms_and_meta( int $id, array $input ): void {
		foreach ( array_keys( $input ) as $key ) {
			if ( ! taxonomy_exists( $key ) || ! is_array( $input[ $key ] ?? null ) ) {
				continue;
			}
			if ( ! is_object_in_taxonomy( $this->slug, $key ) ) {
				register_taxonomy_for_object_type( $key, $this->slug );
			}
			$terms = (array) $input[ $key ];
			$ids   = array_filter( array_map( fn( $t ) => is_numeric( $t ) ? (int) $t : null, $terms ) );
			wp_set_object_terms( $id, $ids, $key );
		}
		if ( ! empty( $input['meta'] ) && is_array( $input['meta'] ) ) {
			foreach ( $input['meta'] as $key => $value ) {
				update_post_meta( $id, $key, $value );
			}
		}
		if ( ! empty( $input['featured_media'] ) ) {
			set_post_thumbnail( $id, (int) $input['featured_media'] );
		}
	}

	private function format_post( $post, string $context ): array {
		if ( ! $post ) {
			return [];
		}
		return [
			'id'             => (int) $post->ID,
			'date'           => mysql2date( 'c', $post->post_date_gmt ?: $post->post_date, false ),
			'date_gmt'       => mysql2date( 'c', $post->post_date_gmt, false ),
			'modified'       => mysql2date( 'c', $post->post_modified_gmt ?: $post->post_modified, false ),
			'modified_gmt'   => mysql2date( 'c', $post->post_modified_gmt, false ),
			'slug'           => $post->post_name,
			'status'         => $post->post_status,
			'type'           => $post->post_type,
			'link'           => get_permalink( $post ),
			'title'          => [
				'raw'      => $post->post_title,
				'rendered' => get_the_title( $post ),
			],
			'content'        => [
				'raw'      => $post->post_content,
				'rendered' => apply_filters( 'the_content', $post->post_content ),
			],
			'excerpt'        => [
				'raw'      => $post->post_excerpt,
				'rendered' => apply_filters( 'the_excerpt', $post->post_excerpt ),
			],
			'author'         => (int) $post->post_author,
			'featured_media' => (int) get_post_thumbnail_id( $post ),
			'parent'         => (int) $post->post_parent,
			'menu_order'     => (int) $post->menu_order,
			'password'       => $post->post_password,
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'sticky'         => is_sticky( $post->ID ),
		];
	}
}
