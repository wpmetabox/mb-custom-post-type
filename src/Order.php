<?php
namespace MBCPT;

use WP_Query;

class Order {

	public function __construct() {
		add_action( 'load-edit.php', [ $this, 'setup_for_edit_screen' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_mb_cpt_save_order', [ $this, 'save_order' ] );
	}

	public function setup_for_edit_screen(): void {
		$screen = get_current_screen();
		$mode      = $_GET['mode'] ?? 'default';
		if ( $screen->base !== 'edit' || $mode !== 'sortable' ) {
			return;
		}

		// Set initial orders
		$this->set_initial_orders( $screen->post_type );
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
			filemtime( MB_CPT_DIR . '/assets/order.js' ),
			true
		);

		// Use the global $wp_query to get the already-queried posts
		global $wp_query;
		$queried_posts = $wp_query->posts;
		
		// Add menu_order to the posts array
		$all_posts = [];
		foreach ($queried_posts as $index => $post) {
			$post->menu_order = $index + 1;
			$all_posts[] = $post;
		}

		// Get pagination info
		$per_page = (int) get_user_option( 'edit_' . $post_type . '_per_page', get_current_user_id() );
		if ( ! $per_page ) {
			$per_page = 20; // Default value if not set in Screen Options
		}
		$current_page = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;

		// Filter top-level posts (post_parent = 0) for pagination
		$top_level_posts = array_filter( $all_posts, function ($post) {
			return $post->post_parent == 0;
		} );
		$top_level_ids   = wp_list_pluck( $top_level_posts, 'ID' );

		// Split top-level IDs into chunks
		$top_level_chunks      = array_chunk( $top_level_ids, $per_page );
		$current_top_level_ids = $top_level_chunks[ $current_page - 1 ] ?? [];

		// Build a list of IDs to fetch: current top-level IDs + all their descendants
		$post_ids_to_fetch = $current_top_level_ids;
		$all_post_map      = array_column( $all_posts, null, 'ID' ); // Map for quick lookup
		foreach ( $current_top_level_ids as $parent_id ) {
			// Recursively find all children
			$post_ids_to_fetch = array_merge( $post_ids_to_fetch, $this->get_children( $parent_id, $all_post_map ) );
		}

		$post_ids_to_fetch = array_unique( $post_ids_to_fetch ); // Remove duplicates

		// Fetch full post data only if we have IDs to fetch
		$posts = [];
		if ( ! empty( $post_ids_to_fetch ) ) {
			$args = [ 
				'post_type'              => $post_type,
				'post__in'               => $post_ids_to_fetch,
				'posts_per_page'         => -1,                   // Get all matching posts (parents + children)
				'orderby'                => 'post__in',           // Preserve the original order
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'no_found_rows'          => false,
			];

			$full_query = new WP_Query( $args );
			$posts      = array_map( function ($post) use ( $all_post_map ) {
				return [ 
					'ID'          => $post->ID,
					'post_title'  => $post->post_title ?: __( '(no title)', 'mb-custom-post-type' ),
					'post_parent' => $post->post_parent,
					'menu_order'  => $post->menu_order == 0 ? $all_post_map[ $post->ID ]->menu_order : $post->menu_order,
					'post_status' => $post->post_status,
				];
			}, $full_query->posts );
		}

		// Localize script with the queried posts
		wp_localize_script( 'mb-cpt-order-script', 'MB_CPT_ORDER', [
			'posts'        => $posts,
			'nonce'        => wp_create_nonce( 'mb_cpt_order_nonce' ),
			'post_type'    => $post_type,
			'mode'         => $_GET['mode'] ?? 'default',
			'hierarchical' => $hierarchical,
			'current_page' => $current_page,
			'per_page'     => $per_page,
		] );

		// Enqueue styles
		wp_enqueue_style(
			'mb-cpt-order-style',
			MB_CPT_URL . 'assets/order.css',
			[],
			filemtime( MB_CPT_DIR . '/assets/order.css' ),
		);
	}

	private function set_initial_orders( string $post_type ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Error.
		$result = $wpdb->get_row( $wpdb->prepare(
			"
			SELECT COUNT(*) AS total, MAX(menu_order) AS max
			FROM $wpdb->posts
			WHERE post_type = %s
			",
			$post_type
		) );

		if ( $result->total == 0 || $result->total == $result->max ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			return;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Error.
		$wpdb->query( 'SET @count = 0;' );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Error.
		$wpdb->query( $wpdb->prepare(
			"UPDATE $wpdb->posts as pt JOIN (
				SELECT ID, (@count:=@count + 1) AS `rank`
				FROM $wpdb->posts
				WHERE post_type = %s
				ORDER BY menu_order ASC
			) as pt2
			ON pt.id = pt2.id
			SET pt.menu_order = pt2.`rank`;",
			$post_type
		) );
	}

	private function get_children( $parent_id, $post_map ) {
		$children = [];
		foreach ( $post_map as $post ) {
			if ( $post->post_parent == $parent_id ) {
				$children[] = $post->ID;
				$children   = array_merge( $children, $this->get_children( $post->ID, $post_map ) );
			}
		}

		return $children;
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

		// translators: %1$s: Current class, %2$s: URL, %3$s: Re-Order text
		$views['toggle_sortable'] = sprintf(
			'<a id="toggle-sortable-btn" class="toggle-sortable-btn %1$s" href="%2$s">
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"><path d="M7 20h2V8h3L8 4 4 8h3zm13-4h-3V4h-2v12h-3l4 4z"></path></svg>
				%3$s
			</a>',
			$mode === 'sortable' ? 'current' : '',
			$url,
			esc_html__( 'Re-Order', 'mb-custom-post-type' )
		);

		return $views;
	}

	// AJAX handler for saving order
	public function save_order() {
		global $wpdb;

		check_ajax_referer( 'mb_cpt_order_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'mb-custom-post-type' ) );
		}

		$order_data = json_decode( wp_unslash( $_POST['order_data'] ), true );

		if ( ! is_array( $order_data ) || empty( $order_data ) ) {
			wp_send_json_error( __( 'Invalid order data', 'mb-custom-post-type' ) );
		}

		foreach ( $order_data as $item ) {
			$wpdb->update(
				$wpdb->posts,
				[
					'post_parent' => $item['parent_id'] ? $item['parent_id'] : 0,
					'menu_order'  => $item['order'],
				],
				[ 'ID' => $item['id'] ]
			);
		}

		wp_send_json_success( __( 'Order updated', 'mb-custom-post-type' ) );
	}
}
