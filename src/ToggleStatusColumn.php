<?php
namespace MBCPT;

use MetaBox\Support\Data;

class ToggleStatusColumn {

	public function __construct() {
		add_action( 'admin_init', [ $this, 'init' ] );
	}

	public function init(): void {
		if ( ! defined( 'MBB_VER' ) ) {
			return;
		}
		add_filter( 'mbb_toggle_status_post_types', [ $this, 'add_toggle_status_column' ] );
	}

	public function add_toggle_status_column( array $post_types ): array {
		$all_post_types = Data::get_post_types();
		foreach ( $all_post_types as $slug => $post_type ) {
			if ( ! empty( $post_type->status_column ) ) {
				$post_types[] = $slug;
			}
		}
		return $post_types;
	}
}
