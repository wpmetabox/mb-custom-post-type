<?php
namespace MBCPT;

class AddField {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'add_field_post_type' ] );
		add_action( 'admin_init', [ $this, 'add_field_taxonomy' ] );
	}

	public function add_field_post_type() {
		if ( ! $this->check_screen( 'mb-post-type' ) ) {
			return;
		}

		$post         = get_post( $_GET['mb-post-type'] );
		$data         = [
			'post_title'  => $post->post_title . ' Fields',
			'post_type'   => 'meta-box',
			'post_status' => 'publish',
		];
		$post_id      = wp_insert_post( $data );
		$post_content = json_decode( $post->post_content, ARRAY_A );
		$settings     = [
			'object_type' => 'post',
			'post_types'  => [ $post_content['slug'] ],
		];
		update_post_meta( $post_id, 'settings', $settings );
		wp_safe_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
		exit;
	}

	public function add_field_taxonomy() {
		if ( ! $this->check_screen( 'mb-taxonomy' ) ) {
			return;
		}

		$post         = get_post( $_GET['mb-taxonomy'] );
		$data         = [
			'post_title'  => $post->post_title . ' Fields',
			'post_type'   => 'meta-box',
			'post_status' => 'publish',
		];
		$post_id      = wp_insert_post( $data );
		$post_content = json_decode( $post->post_content, ARRAY_A );
		$settings     = [
			'object_type' => 'term',
			'taxonomies'  => [ $post_content['slug'] ],
		];
		update_post_meta( $post_id, 'settings', $settings );
		wp_safe_redirect( admin_url( 'post.php?post=' . $post_id . '&action=edit' ) );
		exit;
	}

	public function check_screen( $type ) {
		global $pagenow;
		return $pagenow == 'post-new.php' && ! empty( $_GET['post_type'] ) && 'meta-box' == $_GET['post_type'] && ! empty( $_GET[ $type ] );
	}
}
