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
	private ?WP_REST_Terms_Controller $controller = null;

	private const ABILITY_MAP = [
		'abilities_get_data' => 'register_get_metadata',
		'abilities_get'      => 'register_get',
		'abilities_create'   => 'register_create',
		'abilities_update'   => 'register_update',
		'abilities_delete'   => 'register_delete',
	];

	public function __construct( string $slug, WP_Taxonomy $taxonomy, array $settings ) {
		$this->slug     = $slug;
		$this->taxonomy = $taxonomy;
		$this->singular = $taxonomy->labels->singular_name ?? $slug;
		$this->label    = $taxonomy->labels->name ?? $slug;
		$this->settings = $settings;
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
			"meta-box/get-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->assign_terms ),
				'input_schema'        => [
					'type'       => 'object',
					'properties' => $this->collection_input_schema(),
				],
				'output_schema'       => $this->items_schema(),
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'execute_get_terms' ],
			]
		);
	}

	private function register_get_metadata(): void {
		wp_register_ability(
			"meta-box/get-taxonomy-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s taxonomy', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Get %s taxonomy data.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->assign_terms ),
				'input_schema'        => [ 'type' => 'object' ],
				'output_schema'       => ( new WP_REST_Taxonomies_Controller( $this->slug ) )->get_item_schema(),
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'execute_get_taxonomy' ],
			]
		);
	}

	private function register_create(): void {
		wp_register_ability(
			"meta-box/create-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Create %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Create a new %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->manage_terms ),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'name' ],
					'properties' => array_merge(
						$this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
						[ 'context' => $this->context_param() ]
					),
				],
				'output_schema'       => $this->controller()->get_item_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_create_term' ],
			]
		);
	}

	private function register_update(): void {
		wp_register_ability(
			"meta-box/update-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Update %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Update an existing %s. Only provided fields are modified.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->edit_terms ),
				'input_schema'        => [
					'type'       => 'object',
					'required'   => [ 'id' ],
					'properties' => $this->update_input_schema(),
				],
				'output_schema'       => $this->controller()->get_item_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'execute_update_term' ],
			]
		);
	}

	private function register_delete(): void {
		wp_register_ability(
			"meta-box/delete-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->delete_terms ),
				'input_schema'        => $this->delete_input_schema( 'Term' ),
				'output_schema'       => $this->delete_output_schema(),
				'meta'                => $this->meta( false, true ),
				'execute_callback'    => [ $this, 'execute_delete_term' ],
			]
		);
	}

	/**
	 * @return array|WP_Error
	 */
	public function execute_get_terms( array $input ) {
		$context = $input['context'] ?? 'view';
		$base    = $this->rest_base();
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
		$request            = new WP_REST_Request( 'DELETE', $this->rest_base() . '/' . (int) $input['id'] );
		$request['id']      = (int) $input['id'];
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

	/**
	 * @return array|WP_Error
	 */
	private function dispatch( string $method, string $path_suffix, array $input ) {
		$request = new WP_REST_Request( $method, $this->rest_base() . $path_suffix );
		if ( $path_suffix !== '' ) {
			$id = (int) ltrim( (string) $path_suffix, '/' );
			$request->set_url_params( [ 'id' => $id ] );
			$request['id'] = $id;
		}
		$request->set_body_params( $this->passthrough( $input, $this->controller()->get_endpoint_args_for_item_schema( 'PUT' === $method ? WP_REST_Server::EDITABLE : WP_REST_Server::CREATABLE ) ) );
		$request['context'] = $input['context'] ?? 'view';

		$response = 'PUT' === $method
			? $this->controller()->update_item( $request )
			: $this->controller()->create_item( $request );

		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	private function controller(): WP_REST_Terms_Controller {
		$this->controller ??= new WP_REST_Terms_Controller( $this->slug );
		return $this->controller;
	}

	private function rest_base(): string {
		return '/' . ( $this->taxonomy->rest_base ?: $this->slug );
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
			'description' => __( 'Term ID to retrieve a single item.', 'mb-custom-post-type' ),
		];
		$properties['context'] = $this->context_param();
		return $properties;
	}

	private function update_input_schema(): array {
		$properties            = $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE );
		$properties['id']      = [
			'type'        => 'integer',
			'description' => __( 'Term ID to update.', 'mb-custom-post-type' ),
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
					'description' => __( 'Required to be true, as terms do not support trashing.', 'mb-custom-post-type' ),
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
				'previous' => $this->controller()->get_item_schema(),
			],
		];
	}

	private function items_schema(): array {
		return [
			'type'  => 'array',
			'items' => $this->controller()->get_item_schema(),
		];
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
