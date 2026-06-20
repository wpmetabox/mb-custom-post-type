<?php
namespace MBCPT\Abilities;

abstract class BaseAbilities {

	protected string $slug;
	protected string $singular;
	protected string $label;
	protected array $settings;

	private const ABILITY_MAP = [
		'abilities_get_data' => 'register_get_metadata_ability',
		'abilities_get'      => 'register_get_ability',
		'abilities_create'   => 'register_create_ability',
		'abilities_update'   => 'register_update_ability',
		'abilities_delete'   => 'register_delete_ability',
	];

	abstract protected function register_get_ability(): void;
	abstract protected function register_get_metadata_ability(): void;
	abstract protected function register_create_ability(): void;
	abstract protected function register_update_ability(): void;
	abstract protected function register_delete_ability(): void;
	abstract protected function get_item_schema(): array;

	public function register(): void {
		foreach ( self::ABILITY_MAP as $key => $method ) {
			if ( ! empty( $this->settings[ $key ] ) ) {
				$this->$method();
			}
		}
	}

	protected function permission( string $cap ): callable {
		return function ( $input = [] ) use ( $cap ) {
			$input = is_array( $input ) ? $input : [];
			return ! empty( $input['id'] )
				? current_user_can( $cap, (int) $input['id'] )
				: current_user_can( $cap );
		};
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

	protected function delete_input_schema( string $label ): array {
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
}
