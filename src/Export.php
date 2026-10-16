<?php
namespace MBCPT;

use WP_Post;

class Export {
	private const ALLOWED_POST_TYPES = [ 'mb-post-type', 'mb-taxonomy' ];

	public function __construct() {
		add_filter( 'post_row_actions', [ $this, 'add_export_link' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'export' ] );
	}

	public function add_export_link( array $actions, WP_Post $post ): array {
		if ( ! in_array( $post->post_type, self::ALLOWED_POST_TYPES, true ) || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}

		$url               = wp_nonce_url( add_query_arg( [
			'action'    => 'mbcpt-export',
			'post_type' => $post->post_type,
			'post[]'    => $post->ID,
		] ), 'bulk-posts' );
		$actions['export'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Export', 'mb-custom-post-type' ) . '</a>';

		return $actions;
	}

	public function export(): void {
		$action  = isset( $_REQUEST['action'] ) && 'mbcpt-export' === $_REQUEST['action'];
		$action2 = isset( $_REQUEST['action2'] ) && 'mbcpt-export' === $_REQUEST['action2'];

		if ( ( ! $action && ! $action2 ) || empty( $_REQUEST['post'] ) || empty( $_REQUEST['post_type'] ) ) {
			return;
		}

		check_ajax_referer( 'bulk-posts' );

		$post_type        = sanitize_key( wp_unslash( $_REQUEST['post_type'] ) );
		$post_type_object = in_array( $post_type, self::ALLOWED_POST_TYPES, true ) ? get_post_type_object( $post_type ) : null;
		if ( ! $post_type_object || ! current_user_can( $post_type_object->cap->edit_posts ) ) {
			wp_die( esc_html__( 'You are not allowed to export this post type.', 'mb-custom-post-type' ), 403 );
		}

		$posts = [];
		foreach ( wp_parse_id_list( wp_unslash( $_REQUEST['post'] ) ) as $post_id ) {
			$post = get_post( $post_id );
			if ( $post instanceof WP_Post && $post->post_type === $post_type && current_user_can( 'edit_post', $post_id ) ) {
				$posts[] = $post;
			}
		}

		if ( ! $posts ) {
			wp_die( esc_html__( 'You are not allowed to export these items.', 'mb-custom-post-type' ), 403 );
		}

		$data = [];
		foreach ( $posts as $post ) {
			$data[] = [
				'post_title'  => $post->post_title,
				'settings'    => json_decode( $post->post_content, true ),
				'post_date'   => $post->post_date,
				'post_status' => $post->post_status,
				'post_type'   => $post->post_type,
			];
		}

		$file_name = $post_type === 'mb-taxonomy' ? 'taxonomies-export' : 'post-types-export';
		if ( count( $posts ) === 1 ) {
			$data      = reset( $data );
			$file_name = $posts[0]->post_name ?: sanitize_key( $posts[0]->post_title );
		}

		$output = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( $file_name ) . '.json' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . strlen( $output ) );

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		die;
	}
}
