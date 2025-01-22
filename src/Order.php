<?php
namespace MBCPT;

use WP_Query;
use MetaBox\Support\Data;

class Order {
	public function __construct() {
		add_action( 'load-edit.php', [ $this, 'setup_for_edit_screen' ] );
		add_action( 'admin_init', [ $this, 'add_admin_columns' ] );
		add_action( 'wp_ajax_mbcpt_update_order_items', [ $this, 'update_order_items' ] );
		add_action( 'pre_get_posts', [ $this, 'set_orderby_menu_order' ] );
		add_filter( 'get_previous_post_where', [ $this, 'order_previous_post_where' ] );
		add_filter( 'get_previous_post_sort', [ $this, 'order_previous_post_sort' ] );
		add_filter( 'get_next_post_where', [ $this, 'order_next_post_where' ] );
		add_filter( 'get_next_post_sort', [ $this, 'order_next_post_sort' ] );
	}

	public function setup_for_edit_screen(): void {
		$screen = get_current_screen();
		if ( $screen->base !== 'edit' || ! $this->is_enabled_ordering( $screen->post_type ) ) {
			return;
		}

		// Set initial orders
		$this->set_initial_orders( $screen->post_type );

		// Enqueue assets
		wp_enqueue_style( 'order', MB_CPT_URL . 'assets/order.css', [], MB_CPT_VER );
		wp_enqueue_script( 'order', MB_CPT_URL . 'assets/order.js', [ 'jquery-ui-sortable' ], MB_CPT_VER, true );
		wp_localize_script( 'order', 'MBCPT', [ 'security' => wp_create_nonce( 'order' ) ] );
	}

	public function add_admin_columns() {
		$post_types = Data::get_post_types();
		foreach ( $post_types as $slug => $post_type ) {
			if ( ! $this->is_enabled_ordering( $slug ) ) {
				continue;
			}
			add_filter( "manage_{$slug}_posts_columns", [ $this, 'add_admin_order_column' ] );
			add_action( "manage_{$slug}_posts_custom_column", [ $this, 'show_admin_order_column' ] );
		}
	}

	private function set_initial_orders( string $post_type ): void {
		global $wpdb;
		$query = $wpdb->prepare(
			"
			SELECT COUNT(*) AS total, MAX(menu_order) AS max
			FROM $wpdb->posts
			WHERE post_type = %s
			",
			$post_type
		);

		$result = $wpdb->get_row( $query );

		if ( $result->total == 0 || $result->total == $result->max ) {
			return;
		}

		$wpdb->query( 'SET @count = 0;' );
		$wpdb->query(
			"UPDATE $wpdb->posts as pt JOIN (
			SELECT ID, (@count:=@count + 1) AS `rank`
			FROM $wpdb->posts
			WHERE post_type = '$post_type'
			ORDER BY menu_order ASC
		) as pt2
		ON pt.id = pt2.id
		SET pt.menu_order = pt2.`rank`;"
		);
	}

	public function update_order_items(): void {
		check_ajax_referer( 'order', 'security' );
		global $wpdb;
		parse_str( $_POST['order'], $data );
		if ( ! is_array( $data ) ) {
			wp_send_json_error( __( 'Error: Invalid data!', 'mb-custom-post-type' ) );
		}

		$post_id = empty( $_POST['id'] ) ? false : (int) $_POST['id'];
		$prev_id = empty( $_POST['prev_id'] ) ? false : (int) $_POST['prev_id'];
		$next_id = empty( $_POST['next_id'] ) ? false : (int) $_POST['next_id'];
		if ( ! $post_id ) {
			wp_send_json_error( __( 'Missing mandatory parameters.', 'mb-custom-post-type' ) );
		}
		$parent_id        = wp_get_post_parent_id( $post_id );
		$next_post_parent = $next_id ? wp_get_post_parent_id( $next_id ) : false;
		if ( $prev_id === $next_post_parent ) {
			$parent_id = $next_post_parent;
		}
		if ( $next_post_parent !== $parent_id ) {
			$prev_post_parent = $prev_id ? wp_get_post_parent_id( $prev_id ) : false;
			if ( $prev_post_parent !== $parent_id ) {
				$parent_id = ( false !== $prev_post_parent ) ? $prev_post_parent : $next_post_parent;
			}
		}
		wp_update_post([
			'ID'          => $post_id,
			'post_parent' => $parent_id,
		]);

		$id_arr = [];
		foreach ( $data as $values ) {
			foreach ( $values as $id ) {
				$id_arr[] = (int) $id;
			}
		}

		$menu_order_arr = [];
		foreach ( $id_arr as $id ) {
			$sql              = "SELECT menu_order FROM $wpdb->posts WHERE ID = %d";
			$menu_order_arr[] = (int) $wpdb->get_var( $wpdb->prepare( $sql, $id ) );
		}

		sort( $menu_order_arr );

		foreach ( $data as $values ) {
			foreach ( $values as $position => $id ) {
				$wpdb->update(
					$wpdb->posts,
					[ 'menu_order' => $menu_order_arr[ $position ] ],
					[ 'ID' => $id ],
					[ '%d' ],
					[ '%d' ]
				);
			}
		}

		$ancestors = get_post_ancestors( $post_id );
		wp_send_json_success( count( $ancestors ) );
	}

	public function set_orderby_menu_order( WP_Query $query ): void {
		$post_type = $query->get( 'post_type' );

		if ( ! $post_type || ! is_string( $post_type ) || ! $this->is_enabled_ordering( $post_type ) ) {
			return;
		}

		if ( $query->get( 'orderby' ) ) {
			return;
		}

		$query->set( 'orderby', 'menu_order' );
		if ( ! $query->get( 'order' ) ) {
			$query->set( 'order', 'ASC' );
		}
	}

	public function add_admin_order_column( array $columns ): array {
		return [ 'mbcpt_order' => '' ] + $columns;
	}

	public function show_admin_order_column( string $name ): void {
		if ( $name === 'mbcpt_order' ) {
			echo '<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.375 3.67c0-.645-.56-1.17-1.25-1.17s-1.25.525-1.25 1.17c0 .646.56 1.17 1.25 1.17s1.25-.524 1.25-1.17zm0 8.66c0-.646-.56-1.17-1.25-1.17s-1.25.524-1.25 1.17c0 .645.56 1.17 1.25 1.17s1.25-.525 1.25-1.17zm-1.25-5.5c.69 0 1.25.525 1.25 1.17 0 .645-.56 1.17-1.25 1.17S4.875 8.645 4.875 8c0-.645.56-1.17 1.25-1.17zm5-3.16c0-.645-.56-1.17-1.25-1.17s-1.25.525-1.25 1.17c0 .646.56 1.17 1.25 1.17s1.25-.524 1.25-1.17zm-1.25 7.49c.69 0 1.25.524 1.25 1.17 0 .645-.56 1.17-1.25 1.17s-1.25-.525-1.25-1.17c0-.646.56-1.17 1.25-1.17zM11.125 8c0-.645-.56-1.17-1.25-1.17s-1.25.525-1.25 1.17c0 .645.56 1.17 1.25 1.17s1.25-.525 1.25-1.17z"/></svg>';
		}
	}

	public function order_previous_post_where( string $where ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->is_enabled_ordering( $post->post_type ) ) {
			$where = preg_replace( "/p.post_date < \'[0-9\-\s\:]+\'/i", "p.menu_order > '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	public function order_previous_post_sort( string $orderby ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->is_enabled_ordering( $post->post_type ) ) {
			$orderby = 'ORDER BY p.menu_order ASC LIMIT 1';
		}
		return $orderby;
	}

	public function order_next_post_where( string $where ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->is_enabled_ordering( $post->post_type ) ) {
			$where = preg_replace( "/p.post_date > \'[0-9\-\s\:]+\'/i", "p.menu_order < '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	public function order_next_post_sort( string $orderby ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->is_enabled_ordering( $post->post_type ) ) {
			$orderby = 'ORDER BY p.menu_order DESC LIMIT 1';
		}
		return $orderby;
	}

	private function is_enabled_ordering( string $post_type ): bool {
		$post_type_object = get_post_type_object( $post_type );
		return ! empty( $post_type_object->order );
	}
}
