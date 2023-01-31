<?php
namespace MBCPT;

use WP_Query;

class Export {
	public function __construct() {
		add_filter( 'post_row_actions', [ $this, 'add_export_link' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'export' ] );
	}

	public function add_export_link( $actions, $post ) {
		if ( ! in_array( $post->post_type, [ 'mb-post-type', 'mb-taxonomy' ], true ) ) {
			return $actions;
		}

		$url               = wp_nonce_url( add_query_arg( [
			'action'    => 'mbcpt-export',
			'post_type' => $post->post_type,
			'post[]'    => $post->ID,
		] ), 'bulk-posts' ); // @see WP_List_Table::display_tablenav()
		$actions['export'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Export', 'mb-custom-post-type' ) . '</a>';

		return $actions;
	}

	public function export() {
		$action  = isset( $_REQUEST['action'] ) && 'mbcpt-export' === $_REQUEST['action'];
		$action2 = isset( $_REQUEST['action2'] ) && 'mbcpt-export' === $_REQUEST['action2'];

		if ( ( ! $action && ! $action2 ) || empty( $_REQUEST['post'] ) || empty( $_REQUEST['post_type'] ) ) {
			return;
		}

		check_ajax_referer( 'bulk-posts' );

		$post_ids = wp_parse_id_list( wp_unslash( $_REQUEST['post'] ) );

		$query = new WP_Query( [
			'post_type'              => sanitize_text_field( wp_unslash( $_REQUEST['post_type'] ) ),
			'post__in'               => $post_ids,
			'posts_per_page'         => count( $post_ids ),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		] );

		$data = [];
		foreach ( $query->posts as $post ) {
			$data[] = [
				'post_title'  => $post->post_title,
				'settings'    => json_decode( $post->post_content, true ),
				'post_date'   => $post->post_date,
				'post_status' => $post->post_status,
				'post_type'   => $post->post_type,
			];
		}
		if ( $_REQUEST['post_type'] === 'mb-post-type' ) {
			$file_name = 'post-types-exported';
		} elseif ( $_REQUEST['post_type'] === 'mb-taxonomy' ) {
			$file_name = 'taxonomies-exported';
		}
		if ( count( $post_ids ) === 1 ) {
			$data      = reset( $data );
			$post      = $query->posts[0];
			$file_name = $post->post_name ?: sanitize_key( $post->post_title );
		}

		$data = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

		header( 'Content-Type: application/octet-stream' );
		header( "Content-Disposition: attachment; filename=$file_name.json" );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );
		header( 'Content-Length: ' . strlen( $data ) );
		echo $data;
		die;
	}
}
