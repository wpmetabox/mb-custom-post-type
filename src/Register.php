<?php
namespace MBCPT;

use MetaBox\Support\Arr;
use WP_Post;

abstract class Register {
	public function __construct() {
		$this->register();
		add_filter( 'post_updated_messages', [ $this, 'updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ $this, 'bulk_updated_messages' ], 10, 2 );
	}

	abstract public function register(): void;

	protected function unarray( &$value, string $key, array $ignore = [] ): void {
		$value = 1 === count( $value ) && ! in_array( $key, $ignore, true ) ? $value[0] : $value;
	}

	protected function normalize_checkbox( &$value ): void {
		if ( is_numeric( $value ) && in_array( $value, [ 0, 1 ] ) ) { // phpcs:ignore
			$value = 1 === (int) $value;
		}
	}

	protected function change_key( array &$arr, string $from, string $to ): void {
		if ( isset( $arr[ $from ] ) ) {
			$arr[ $to ] = $arr[ $from ];
		}
		unset( $arr[ $from ] );
	}

	public static function sanitize_labels( array &$settings ): void {
		$labels = Arr::get( $settings, 'labels', [] );
		$labels = array_map( 'sanitize_text_field', $labels );
		$labels = array_map( function ( $text ) {
			return str_replace( [ '&lt;', '&gt;' ], '', $text );
		}, $labels );
		Arr::set( $settings, 'labels', $labels );
	}

	/**
	 * True when the definition has no saved JSON, or an admin forces re-migration.
	 */
	protected function should_migrate( WP_Post $post ): bool {
		return empty( $post->post_content ) || $this->can_force_migrate();
	}

	/**
	 * Force migration requires a logged-in user with manage_options.
	 */
	protected function can_force_migrate(): bool {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return isset( $_GET['mbcpt-force'] ) && current_user_can( 'manage_options' );
	}

	/**
	 * Delete pre-2.0 per-field meta so migration cannot run again from stale data.
	 *
	 * @param int      $post_id Post ID.
	 * @param string[] $keys    Meta keys to delete.
	 */
	protected function delete_legacy_meta( int $post_id, array $keys ): void {
		foreach ( $keys as $key ) {
			delete_post_meta( $post_id, $key );
		}
	}
}
