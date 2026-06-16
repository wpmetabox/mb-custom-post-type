<?php
namespace MBCPT\Abilities;

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
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => function () {
					return current_user_can( $this->post_type->cap->read );
				},
				'input_schema'        => [
					'type'       => 'object',
					'properties' => $this->collection_input_schema(),
				],
				'output_schema'       => [
					'type'  => 'array',
					'items' => $this->post_controller()->get_item_schema(),
				],
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
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
				'input_schema'        => [ 'type' => 'object' ],
				'output_schema'       => ( new WP_REST_Post_Types_Controller( $this->slug ) )->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
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
					'properties' => array_merge(
						$this->post_controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
						[ 'context' => $this->context_param() ]
					),
				],
				'output_schema'       => $this->post_controller()->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
					],
					'mcp'         => [ 'public' => true ],
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
					'properties' => $this->update_input_schema(),
				],
				'output_schema'       => $this->post_controller()->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
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
				'output_schema'       => $this->post_controller()->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
				],
				'execute_callback'    => function ( array $input ): array {
					return $this->execute_delete( $input );
				},
			]
		);
	}

	private function execute_get( array $input ): array {
		$context = $input['context'] ?? 'view';
		$base    = '/' . ( $this->post_type->rest_base ?: $this->slug );
		$rest    = rest_get_server();

		if ( ! empty( $input['id'] ) ) {
			$request            = new WP_REST_Request( 'GET', $base . '/' . (int) $input['id'] );
			$request['context'] = $context;
			$response           = $this->post_controller()->get_item( $request );
		} else {
			$request = new WP_REST_Request( 'GET', $base );
			$request->set_query_params( $this->passthrough( $input, $this->post_controller()->get_collection_params() ) );
			$request['context'] = $context;
			$response           = $this->post_controller()->get_items( $request );
		}

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$data = $rest->response_to_data( $response, true );
		return empty( $input['id'] ) ? $data : [ $data ];
	}

	private function execute_get_post_type(): array {
		$controller = new WP_REST_Post_Types_Controller( $this->slug );
		$request    = new WP_REST_Request( 'GET', '/types/' . $this->slug );
		$response   = $controller->get_item( $request );
		return is_wp_error( $response ) ? [] : rest_get_server()->response_to_data( $response, true );
	}

	private function execute_create( array $input ): array {
		return $this->dispatch( 'POST', '', $input );
	}

	private function execute_update( array $input ): array {
		return $this->dispatch( 'PUT', (int) $input['id'], $input );
	}

	private function execute_delete( array $input ): array {
		$context            = $input['context'] ?? 'view';
		$base               = '/' . ( $this->post_type->rest_base ?: $this->slug );
		$request            = new WP_REST_Request( 'DELETE', $base . '/' . (int) $input['id'] );
		$request['context'] = $context;
		$request['force']   = ! empty( $input['force'] );

		$response = $this->post_controller()->delete_item( $request );
		return is_wp_error( $response ) ? [] : rest_get_server()->response_to_data( $response, true );
	}

	private function dispatch( string $method, string $path_suffix, array $input ): array {
		$context = $input['context'] ?? 'view';
		$base    = '/' . ( $this->post_type->rest_base ?: $this->slug );
		$request = new WP_REST_Request( $method, $base . $path_suffix );
		$request->set_body_params( $this->passthrough( $input, $this->post_controller()->get_endpoint_args_for_item_schema( 'PUT' === $method ? WP_REST_Server::EDITABLE : WP_REST_Server::CREATABLE ) ) );
		$request['context'] = $context;

		$controller = $this->post_controller();
		$response   = 'PUT' === $method ? $controller->update_item( $request ) : $controller->create_item( $request );
		return is_wp_error( $response ) ? [] : rest_get_server()->response_to_data( $response, true );
	}

	private function post_controller(): WP_REST_Posts_Controller {
		return new WP_REST_Posts_Controller( $this->slug );
	}

	private function collection_input_schema(): array {
		$properties       = $this->post_controller()->get_collection_params();
		$properties['id'] = [
			'type'        => 'integer',
			'description' => __( 'Post ID to retrieve a single item.', 'mb-custom-post-type' ),
		];
		return $properties;
	}

	private function update_input_schema(): array {
		$properties            = $this->post_controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE );
		$properties['id']      = [
			'type'        => 'integer',
			'description' => __( 'Post ID to update.', 'mb-custom-post-type' ),
		];
		$properties['context'] = $this->context_param();
		return $properties;
	}

	private function passthrough( array $input, array $allowed ): array {
		return array_intersect_key( $input, $allowed );
	}

	private function context_param(): array {
		return [
			'type'        => 'string',
			'description' => __( 'Scope under which the request is made.', 'mb-custom-post-type' ),
			'enum'        => [ 'view', 'embed', 'edit' ],
			'default'     => 'view',
		];
	}
}
