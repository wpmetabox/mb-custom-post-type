<?php
namespace MBCPT\Abilities;

use WP_Error;
use WP_Taxonomy;
use WP_REST_Request;
use WP_REST_Taxonomies_Controller;
use WP_REST_Terms_Controller;
use WP_REST_Server;

class TaxonomyAbilities extends BaseAbilities {

	private WP_Taxonomy $taxonomy;
	private ?WP_REST_Terms_Controller $controller = null;

	public function __construct( string $slug, WP_Taxonomy $taxonomy, array $settings ) {
		$this->slug     = $slug;
		$this->taxonomy = $taxonomy;
		$this->singular = $taxonomy->labels->singular_name ?? $slug;
		$this->label    = $taxonomy->labels->name ?? $slug;
		$this->settings = $settings;
	}

	protected function register_get_ability(): void {
		wp_register_ability(
			"meta-box/get-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Get %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Search and list %s.', 'mb-custom-post-type' ), strtolower( $this->label ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->assign_terms ),
				'input_schema'        => [
					'type'       => 'object',
					'properties' => array_merge(
						$this->controller()->get_collection_params(),
						[
							'id' => [
								'type'        => 'integer',
								'description' => __( 'Term ID to retrieve a single item.', 'mb-custom-post-type' ),
							],
						]
					),
				],
				'output_schema'       => [
					'type'  => 'array',
					'items' => $this->get_item_schema(),
				],
				'meta'                => $this->meta( true ),
				'execute_callback'    => [ $this, 'get_terms' ],
			]
		);
	}

	protected function register_get_metadata_ability(): void {
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
				'execute_callback'    => [ $this, 'get_taxonomy' ],
			]
		);
	}

	protected function register_create_ability(): void {
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
					'properties' => $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				],
				'output_schema'       => $this->get_item_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'create_term' ],
			]
		);
	}

	protected function register_update_ability(): void {
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
				'output_schema'       => $this->get_item_schema(),
				'meta'                => $this->meta(),
				'execute_callback'    => [ $this, 'update_term' ],
			]
		);
	}

	protected function register_delete_ability(): void {
		wp_register_ability(
			"meta-box/delete-term-{$this->slug}",
			[
				'label'               => sprintf( __( 'Delete %s', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'description'         => sprintf( __( 'Delete a %s.', 'mb-custom-post-type' ), strtolower( $this->singular ) ),
				'category'            => 'meta-box',
				'permission_callback' => $this->permission( $this->taxonomy->cap->delete_terms ),
				'input_schema'        => $this->delete_input_schema( 'Term' ),
				'output_schema'       => [
					'type'       => 'object',
					'required'   => [ 'deleted' ],
					'properties' => [
						'deleted'  => [ 'type' => 'boolean' ],
						'previous' => $this->get_item_schema(),
					],
				],
				'meta'                => $this->meta( false, true ),
				'execute_callback'    => [ $this, 'delete_term' ],
			]
		);
	}

	protected function get_item_schema(): array {
		return $this->controller()->get_item_schema();
	}

	/**
	 * @return array|WP_Error
	 */
	public function get_terms( array $input ) {
		if ( ! empty( $input['id'] ) ) {
			$term = get_term( (int) $input['id'], $this->slug );
			if ( is_wp_error( $term ) || ! $term ) {
				return new WP_Error( 'mb_cpt_not_found', __( 'Term not found.', 'mb-custom-post-type' ) );
			}
			return [ $this->format_term( $term ) ];
		}

		$per_page = isset( $input['per_page'] ) ? (int) $input['per_page'] : 10;
		$page     = isset( $input['page'] ) ? (int) $input['page'] : 1;
		$query    = [
			'taxonomy'   => $this->slug,
			'hide_empty' => false,
			'number'     => $per_page,
			'offset'     => max( 0, ( $page - 1 ) * $per_page ),
		];
		if ( ! empty( $input['search'] ) ) {
			$query['search'] = $input['search'];
		}
		if ( ! empty( $input['parent'] ) ) {
			$query['parent'] = (int) $input['parent'];
		}
		if ( ! empty( $input['include'] ) ) {
			$query['include'] = array_map( 'intval', (array) $input['include'] );
		}
		if ( ! empty( $input['exclude'] ) ) {
			$query['exclude'] = array_map( 'intval', (array) $input['exclude'] );
		}
		if ( ! empty( $input['orderby'] ) ) {
			$query['orderby'] = $input['orderby'];
			$query['order']   = $input['order'] ?? 'ASC';
		}

		$terms = get_terms( $query );
		if ( is_wp_error( $terms ) ) {
			return $terms;
		}
		return array_map( fn( $t ) => $this->format_term( $t ), $terms );
	}

	/**
	 * @return array|WP_Error
	 */
	public function get_taxonomy() {
		$controller = new WP_REST_Taxonomies_Controller( $this->slug );
		$request    = new WP_REST_Request( 'GET', '/taxonomies/' . $this->slug );
		$response   = $controller->get_item( $request );
		return is_wp_error( $response ) ? $response : rest_get_server()->response_to_data( $response, true );
	}

	/**
	 * @return array|WP_Error
	 */
	public function create_term( array $input ) {
		$name   = $input['name'] ?? '';
		$result = wp_insert_term( $name, $this->slug, $this->build_term_args( $input ) );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return $this->format_term( get_term( $result['term_id'], $this->slug ) );
	}

	/**
	 * @return array|WP_Error
	 */
	public function update_term( array $input ) {
		$id = (int) ( $input['id'] ?? 0 );
		if ( ! $id ) {
			return new WP_Error( 'mb_cpt_invalid_id', __( 'Invalid term ID.', 'mb-custom-post-type' ) );
		}
		$result = wp_update_term( $id, $this->slug, $this->build_term_args( $input ) );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return $this->format_term( get_term( $id, $this->slug ) );
	}

	/**
	 * @return array|WP_Error
	 */
	public function delete_term( array $input ) {
		$id   = (int) ( $input['id'] ?? 0 );
		$term = get_term( $id, $this->slug );
		if ( is_wp_error( $term ) || ! $term ) {
			return new WP_Error( 'mb_cpt_not_found', __( 'Term not found.', 'mb-custom-post-type' ) );
		}
		$previous = $this->format_term( $term );
		$result   = wp_delete_term( $id, $this->slug );
		if ( is_wp_error( $result ) || ! $result ) {
			return new WP_Error( 'mb_cpt_delete_failed', __( 'Could not delete term.', 'mb-custom-post-type' ) );
		}
		return [
			'deleted'  => true,
			'previous' => $previous,
		];
	}

	private function controller(): WP_REST_Terms_Controller {
		$this->controller ??= new WP_REST_Terms_Controller( $this->slug );
		return $this->controller;
	}

	private function update_input_schema(): array {
		$properties            = $this->controller()->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE );
		$properties['id'] = [
			'type'        => 'integer',
			'description' => __( 'Term ID to update.', 'mb-custom-post-type' ),
		];
		return $properties;
	}

	private function build_term_args( array $input ): array {
		$map  = [
			'name'        => 'name',
			'slug'        => 'slug',
			'description' => 'description',
			'parent'      => 'parent',
		];
		$args = [];
		foreach ( $map as $in => $out ) {
			if ( array_key_exists( $in, $input ) ) {
				$args[ $out ] = $input[ $in ];
			}
		}
		return $args;
	}

	private function format_term( $term ): array {
		if ( ! $term || is_wp_error( $term ) ) {
			return [];
		}
		return [
			'id'          => (int) $term->term_id,
			'count'       => (int) $term->count,
			'description' => $term->description,
			'link'        => get_term_link( $term ),
			'name'        => $term->name,
			'slug'        => $term->slug,
			'taxonomy'    => $term->taxonomy,
			'parent'      => (int) $term->parent,
		];
	}
}
