<?php
namespace MBCPT\Abilities;

use WP_Error;
use WP_Taxonomy;
use WP_REST_Request;
use WP_REST_Taxonomies_Controller;
use WP_REST_Terms_Controller;
use WP_REST_Server;

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
				'permission_callback' => function ( $input = [] ) {
					return $this->user_can( $this->taxonomy->cap->assign_terms, is_array( $input ) ? $input : [] );
				},
				'input_schema'        => [
					'type'       => 'object',
					'properties' => $this->collection_input_schema(),
				],
				'output_schema'       => [
					'type'  => 'array',
					'items' => $this->term_controller()->get_item_schema(),
				],
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
				],
				'execute_callback'    => [ $this, 'execute_get_terms' ],
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
				'permission_callback' => function ( $input = [] ) {
					return $this->user_can( $this->taxonomy->cap->manage_terms, is_array( $input ) ? $input : [] );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'name' ],
					'properties' => array_merge(
						$this->term_controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
						[ 'context' => $this->context_param() ]
					),
				],
				'output_schema'       => $this->term_controller()->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => false,
					],
					'mcp'         => [ 'public' => true ],
				],
				'execute_callback'    => [ $this, 'execute_create_term' ],
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
				'permission_callback' => function ( $input = [] ) {
					return $this->user_can( $this->taxonomy->cap->edit_terms, is_array( $input ) ? $input : [] );
				},
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => $this->update_input_schema(),
				],
				'output_schema'       => $this->term_controller()->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
				],
				'execute_callback'    => [ $this, 'execute_update_term' ],
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
				'permission_callback' => function ( $input = [] ) {
					return $this->user_can( $this->taxonomy->cap->delete_terms, is_array( $input ) ? $input : [] );
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
				'output_schema'       => [
					'type'       => 'object',
					'required'   => [ 'deleted' ],
					'properties' => [
						'deleted'  => [ 'type' => 'boolean' ],
						'previous' => $this->term_controller()->get_item_schema(),
					],
				],
				'meta'                => [
					'annotations' => [
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
				],
				'execute_callback'    => [ $this, 'execute_delete_term' ],
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
				'permission_callback' => function ( $input = [] ) {
					return $this->user_can( $this->taxonomy->cap->assign_terms, is_array( $input ) ? $input : [] );
				},
				'input_schema'        => [ 'type' => 'object' ],
				'output_schema'       => ( new WP_REST_Taxonomies_Controller( $this->slug ) )->get_item_schema(),
				'meta'                => [
					'annotations' => [
						'readonly'    => true,
						'destructive' => false,
						'idempotent'  => true,
					],
					'mcp'         => [ 'public' => true ],
				],
				'execute_callback'    => [ $this, 'execute_get_taxonomy' ],
			]
		);
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get_terms( array $input ) {
		$context = $input['context'] ?? 'view';
		$base    = '/' . ( $this->taxonomy->rest_base ?: $this->slug );
		$rest    = rest_get_server();

		if ( ! empty( $input['id'] ) ) {
			$request            = new WP_REST_Request( 'GET', $base . '/' . (int) $input['id'] );
			$request['id']      = (int) $input['id'];
			$request['context'] = $context;
			$response           = $this->term_controller()->get_item( $request );

			if ( is_wp_error( $response ) ) {
				return $response;
			}

			$data = $rest->response_to_data( $response, true );
			return is_array( $data ) ? [ $data ] : [];
		}

		$request = new WP_REST_Request( 'GET', $base );
		$request->set_query_params( $this->collection_query( $input ) );
		$request['context'] = $context;
		$response           = $this->term_controller()->get_items( $request );

		return is_wp_error( $response ) ? $response : $rest->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get_taxonomy() {
		$controller = new WP_REST_Taxonomies_Controller( $this->slug );
		$request    = new WP_REST_Request( 'GET', '/taxonomies/' . $this->slug );
		$response   = $controller->get_item( $request );
		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_create_term( array $input ) {
		return $this->dispatch( 'POST', '', $input );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_update_term( array $input ) {
		return $this->dispatch( 'PUT', (int) $input['id'], $input );
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_delete_term( array $input ) {
		$context            = $input['context'] ?? 'view';
		$base               = '/' . ( $this->taxonomy->rest_base ?: $this->slug );
		$request            = new WP_REST_Request( 'DELETE', $base . '/' . (int) $input['id'] );
		$request['id']      = (int) $input['id'];
		$request['context'] = $context;
		$request['force']   = ! empty( $input['force'] );

		$response = $this->term_controller()->delete_item( $request );
		return is_wp_error( $response ) ? $response : $response->get_data();
	}

	/**
	 * @return array|WP_Error
	 */
	private function dispatch( string $method, string $path_suffix, array $input ) {
		$context = $input['context'] ?? 'view';
		$base    = '/' . ( $this->taxonomy->rest_base ?: $this->slug );
		$request = new WP_REST_Request( $method, $base . $path_suffix );
		if ( $path_suffix !== '' ) {
			$id = (int) ltrim( (string) $path_suffix, '/' );
			$request->set_url_params( [ 'id' => $id ] );
			$request['id'] = $id;
		}
		$request->set_body_params( $this->passthrough( $input, $this->term_controller()->get_endpoint_args_for_item_schema( 'PUT' === $method ? WP_REST_Server::EDITABLE : WP_REST_Server::CREATABLE ) ) );
		$request['context'] = $context;

		$controller = $this->term_controller();
		$response   = 'PUT' === $method ? $controller->update_item( $request ) : $controller->create_item( $request );
		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	private function term_controller(): WP_REST_Terms_Controller {
		return new WP_REST_Terms_Controller( $this->slug );
	}

	private function user_can( string $cap, array $input ): bool {
		return ! empty( $input['id'] )
			? current_user_can( $cap, (int) $input['id'] )
			: current_user_can( $cap );
	}

	private function collection_input_schema(): array {
		$properties       = $this->term_controller()->get_collection_params();
		$properties['id'] = [
			'type'        => 'integer',
			'description' => __( 'Term ID to retrieve a single item.', 'mb-custom-post-type' ),
		];
		return $properties;
	}

	private function update_input_schema(): array {
		$properties            = $this->term_controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE );
		$properties['id']      = [
			'type'        => 'integer',
			'description' => __( 'Term ID to update.', 'mb-custom-post-type' ),
		];
		$properties['context'] = $this->context_param();
		return $properties;
	}

	private function passthrough( array $input, array $allowed ): array {
		return array_intersect_key( $input, $allowed );
	}

	private function collection_query( array $input ): array {
		$params     = $this->term_controller()->get_collection_params();
		$query      = [];
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
