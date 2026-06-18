<?php
namespace MBCPT\Abilities;

use MBCPT\Register;
use WP_Error;
use WP_REST_Posts_Controller;
use WP_REST_Request;
use WP_REST_Server;

class TaxonomyDefinitionAbilities {

	private const POST_TYPE = 'mb-taxonomy';
	private const SLUG      = 'taxonomy';
	private const LABEL     = 'taxonomy';
	private const LABEL_PL = 'taxonomies';

	private ?WP_REST_Posts_Controller $controller = null;

	public function register(): void {
		$this->register_get();
		$this->register_create();
		$this->register_update();
		$this->register_delete();
	}

	private function register_get(): void {
		wp_register_ability(
			"meta-box/get-{self::LABEL_PL}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), self::LABEL_PL ),
				'description'         => sprintf( __( 'List or read %s definitions.', 'mb-custom-post-type' ), self::LABEL_PL ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'properties' => $this->collection_input_schema(),
				],
				'output_schema'       => $this->items_schema(),
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'execute_get' ],
			]
		);
	}

	private function register_create(): void {
		wp_register_ability(
			"meta-box/create-{self::SLUG}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), self::LABEL ),
				'description'         => sprintf( __( 'Create a new %s definition.', 'mb-custom-post-type' ), self::LABEL ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'title', 'settings' ],
					'properties' => array_merge(
						$this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
						[
							'settings' => [
								'type'        => 'object',
								'description' => sprintf( __( 'The %s settings object (slug, labels, types, etc.).', 'mb-custom-post-type' ), self::LABEL ),
							],
							'context'  => $this->context_param(),
						]
					),
				],
				'output_schema'       => $this->output_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_create' ],
			]
		);
	}

	private function register_update(): void {
		wp_register_ability(
			"meta-box/update-{self::SLUG}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), self::LABEL ),
				'description'         => sprintf( __( 'Update an existing %s definition. Only provided fields are modified; settings are merged into the existing configuration.', 'mb-custom-post-type' ), self::LABEL ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => $this->update_input_schema(),
				],
				'output_schema'       => $this->output_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_update' ],
			]
		);
	}

	private function register_delete(): void {
		wp_register_ability(
			"meta-box/delete-{self::SLUG}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), self::LABEL ),
				'description'         => sprintf( __( 'Delete a %s definition.', 'mb-custom-post-type' ), self::LABEL ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission(),
				'input_schema'        => $this->delete_input_schema(),
				'output_schema'       => $this->delete_output_schema(),
				'meta'                => $this->meta( false, true ),
				'execute_callback'    => [ $this, 'execute_delete' ],
			]
		);
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get( array $input ) {
		$context = $input['context'] ?? 'view';
		$base    = '/' . self::POST_TYPE;
		$rest    = rest_get_server();

		if ( ! empty( $input['id'] ) ) {
			$request            = new WP_REST_Request( 'GET', $base . '/' . (int) $input['id'] );
			$request['id']      = (int) $input['id'];
			$request['context'] = $context;
			$response           = $this->controller()->get_item( $request );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$data = $rest->response_to_data( $response, true );
			return is_array( $data ) ? [ $data ] : [];
		}

		$request = new WP_REST_Request( 'GET', $base );
		$request->set_query_params( $this->collection_query( $input ) );
		$request['context'] = $context;
		$response           = $this->controller()->get_items( $request );

		return is_wp_error( $response ) ? $response : $rest->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_create( array $input ) {
		if ( ! empty( $input['settings'] ) && is_array( $input['settings'] ) ) {
			$settings = $input['settings'];
			Register::sanitize_labels_static( $settings );
			$input['settings'] = $settings;
		}

		$post_content = $this->encode_settings( $input['settings'] ?? [] );
		$body         = $this->passthrough( $input, $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ) );
		$body['content'] = $post_content;

		$request = new WP_REST_Request( 'POST', '/' . self::POST_TYPE );
		$request->set_body_params( $body );
		$request['context'] = $input['context'] ?? 'view';

		$response = $this->controller()->create_item( $request );
		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_update( array $input ) {
		$id   = (int) $input['id'];
		$post = get_post( $id );
		if ( ! $post || $post->post_type !== self::POST_TYPE ) {
			return new WP_Error( 'mbcpt_not_found', sprintf( __( '%s definition not found.', 'mb-custom-post-type' ), ucfirst( self::LABEL ) ), [ 'status' => 404 ] );
		}

		$body = $this->passthrough( $input, $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ) );

		if ( isset( $input['settings'] ) && is_array( $input['settings'] ) ) {
			$existing = json_decode( (string) $post->post_content, true );
			if ( ! is_array( $existing ) ) {
				$existing = [];
			}
			$merged            = array_merge( $existing, $input['settings'] );
			Register::sanitize_labels_static( $merged );
			$body['content']   = $this->encode_settings( $merged );
		}

		$request = new WP_REST_Request( 'PUT', '/' . self::POST_TYPE . '/' . $id );
		$request->set_url_params( [ 'id' => $id ] );
		$request->set_body_params( $body );
		$request['id']      = $id;
		$request['context'] = $input['context'] ?? 'view';

		$response = $this->controller()->update_item( $request );
		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_delete( array $input ) {
		$id = (int) $input['id'];

		$request            = new WP_REST_Request( 'DELETE', '/' . self::POST_TYPE . '/' . $id );
		$request['id']      = $id;
		$request['context'] = $input['context'] ?? 'view';
		$request['force']   = ! empty( $input['force'] );

		$response = $this->controller()->delete_item( $request );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$data = $response->get_data();
		if ( isset( $data['deleted'] ) ) {
			return $data;
		}

		return [
			'deleted'  => true,
			'previous' => $data,
		];
	}

	private function encode_settings( array $settings ): string {
		return wp_slash( wp_json_encode( $settings ) );
	}

	private function controller(): WP_REST_Posts_Controller {
		$this->controller ??= new WP_REST_Posts_Controller( self::POST_TYPE );
		return $this->controller;
	}

	private function permission(): callable {
		return function () {
			return current_user_can( 'manage_options' );
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
			'description' => sprintf( __( '%s definition ID to retrieve a single item.', 'mb-custom-post-type' ), ucfirst( self::LABEL ) ),
		];
		$properties['context'] = $this->context_param();
		return $properties;
	}

	private function update_input_schema(): array {
		$properties             = $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE );
		$properties['id']       = [
			'type'        => 'integer',
			'description' => sprintf( __( '%s definition ID to update.', 'mb-custom-post-type' ), ucfirst( self::LABEL ) ),
		];
		$properties['settings'] = [
			'type'        => 'object',
			'description' => sprintf( __( 'Partial %s settings to merge into the existing configuration.', 'mb-custom-post-type' ), self::LABEL ),
		];
		$properties['context']  = $this->context_param();
		return $properties;
	}

	private function delete_input_schema(): array {
		return [
			'type'       => 'object',
			'required'   => [ 'id' ],
			'properties' => [
				'id'    => [
					'type'        => 'integer',
					'description' => sprintf( __( '%s definition ID to delete.', 'mb-custom-post-type' ), ucfirst( self::LABEL ) ),
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
		return $this->controller()->get_item_schema();
	}

	private function passthrough( array $input, array $allowed ): array {
		return array_intersect_key( $input, $allowed );
	}

	private function collection_query( array $input ): array {
		$params = $this->controller()->get_collection_params();
		$query  = [];
		foreach ( $params as $key => $args ) {
			if ( array_key_exists( $key, $input ) ) {
				$query[ $key ] = $input[ $key ];
			} elseif ( isset( $args['default'] ) ) {
				$query[ $key ] = $args['default'];
			}
		}
		return $query;
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
