<?php
namespace MBCPT;

use WP_Query;

class Export {
	public function __construct() {
		add_filter( 'post_row_actions', [$this, 'add_export_link'], 10, 2 );
		add_action( 'admin_init', [ $this, 'export' ] );
	}

	public function add_export_link( $actions, $post ) {
		if ( 'mb-post-type' === $post->post_type ) {
			$actions['export'] = '<a href="' . add_query_arg( ['action' => 'mbb-export', 'post[]' => $post->ID] ) . '">' . esc_html__( 'Export111', 'mb-custom-post-type' ) . '</a>';
		}
		return $actions;
	}

	public function export() {
		$action  = isset( $_REQUEST['action'] ) && 'mbb-export' === $_REQUEST['action'];
		$action2 = isset( $_REQUEST['action2'] ) && 'mbb-export' === $_REQUEST['action2'];

		if ( ( ! $action && ! $action2 ) || empty( $_REQUEST['post'] ) ) {
			return;
		}

		$post_ids = $_REQUEST['post'];

		$query = new WP_Query( [
			'post_type'              => 'mb-post-type',
			'post__in'               => $post_ids,
			'posts_per_page'         => count( $post_ids ),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		] );

		$data = [];
		foreach ( $query->posts as $post ) {
			$data[] = array_merge( (array) $post, [
				'settings' => get_post_meta( $post->ID, 'settings', true ),
				'fields'    => get_post_meta( $post->ID, 'fields', true ),
				'data'     => get_post_meta( $post->ID, 'data', true ),
				'meta_box' => get_post_meta( $post->ID, 'meta_box', true ),
			] );
		}

		$file_name = 'field-groups-exported';
		if ( count( $post_ids ) === 1 ) {
			$data = reset( $data );
			$file_name = $query->posts[0]->post_name;
		}

		$data = json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT );

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
