<?php
namespace MBCPT;

use WP_Query;

class Order {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_mb_cpt_save_order', [ $this, 'save_order' ] );
	}

	public function enqueue_scripts( $hook ) {
		if ( $hook !== 'edit.php' ) {
			return;
		}

		// Get current post type from screen
		$screen    = get_current_screen();
		$post_type = $screen->post_type;
		$post_type_object = get_post_type_object( $post_type );
		
		if ( ! isset( $post_type_object->order ) || ! $post_type_object->order ) {
			return;
		}

		$hierarchical = (bool) $post_type_object->hierarchical;

		add_filter( "views_edit-{$post_type}", [ $this, 'add_toggle_sortable_button' ], 10, 1 );

		// Enqueue SortableJS
		wp_enqueue_script(
			'sortablejs',
			MB_CPT_URL . 'assets/lib/Sortable.min.js',
			[],
			'1.15.6',
			true
		);

		// Enqueue our custom script
		wp_enqueue_script(
			'mb-cpt-order-script',
			MB_CPT_URL . 'assets/order.js',
			[ 'jquery', 'sortablejs' ],
			time(),
			true
		);

		// Use the global $wp_query to get the already-queried posts
		global $wp_query;
		$post_ids = wp_list_pluck( $wp_query->posts, 'ID' );

		// Fetch full post data in one query using post__in
		$args       = [ 
			'post_type' => $post_type,
			'post__in' => $post_ids,
			'posts_per_page' => -1, // Ensure we get all matching posts
			'orderby' => 'post__in', // Preserve the original order from $wp_query
			'ignore_sticky_posts' => true,
			'no_found_rows' => true,
			'update_post_term_cache' => false,
		];
		$full_query = new WP_Query( $args );
		$posts      = array_map( function ($post) {
			return [ 
				'ID' => $post->ID,
				'post_title' 	=> $post->post_title ?: __( '(no title)', 'advanced-page-ordering' ),
				'post_parent' 	=> $post->post_parent,
				'post_status' 	=> $post->post_status,
				'menu_order' 	=> $post->menu_order,
			];
		}, $full_query->posts );

		// Localize script with the queried posts
		wp_localize_script( 'mb-cpt-order-script', 'MB_CPT_ORDER', [ 
			'posts' 	=> $posts,
			'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
			'nonce' 	=> wp_create_nonce( 'mb_cpt_order_nonce' ),
			'post_type' => $post_type,
			'mode' 		=> $_GET['mode'] ?? 'default',
			'hierarchical' => $hierarchical,
		] );

		// Enqueue styles
		wp_enqueue_style(
			'mb-cpt-order-style',
			MB_CPT_URL . 'assets/order.css',
			[],
			time()
		);
	}

	// Add toggle sortable link to views
	public function add_toggle_sortable_button( $views ) {
		$screen    = get_current_screen();
		$post_type = $screen->post_type;
		$mode      = $_GET['mode'] ?? 'default';
		$url       = add_query_arg( [ 
			'post_type' => $post_type,
			'mode' => $mode === 'sortable' ? 'default' : 'sortable',
		] );

		$views['toggle_sortable'] = sprintf(
			'<a id="toggle-sortable-btn" class="toggle-sortable-btn %s" href="%s"><span class="dashicons dashicons-sort"></span> %s</a>',
			$mode === 'sortable' ? 'current' : '',
			$url,
			esc_html__( 'Toggle Sortable', 'advanced-page-ordering' )
		);

		return $views;
	}

	// AJAX handler for saving order
	public function save_order() {
		global $wpdb;

		check_ajax_referer( 'mb_cpt_order_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( 'Insufficient permissions' );
		}

		$order_data = json_decode( stripslashes( $_POST['order_data'] ), true );

		foreach ( $order_data as $item ) {
			$wpdb->update(
				$wpdb->posts,
				[ 
					'post_parent' => $item['parent_id'] ? $item['parent_id'] : 0,
					'menu_order' => $item['order'],
				],
				[ 'ID' => $item['id'] ]
			);
		}

		wp_send_json_success( 'Order updated' );
	}
}
