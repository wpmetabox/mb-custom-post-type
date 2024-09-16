<?php
namespace MBCPT;

use WP_Query;

class Order {
	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_script_css_order' ] );
		add_action( 'wp_ajax_mbcpt_update_menu_order', [ $this, 'update_menu_order' ] );
		add_action( 'pre_get_posts', [ $this, 'order_pre_get_posts' ] );
		add_filter( 'get_previous_post_where', [ $this, 'order_previous_post_where' ] );
		add_filter( 'get_previous_post_sort', [ $this, 'order_previous_post_sort' ] );
		add_filter( 'get_next_post_where', [ $this, 'order_next_post_where' ] );
		add_filter( 'get_next_post_sort', [ $this, 'order_next_post_sort' ] );
	}

	public function load_script_css_order(): void {
		global $pagenow;
		$post_type = $_GET['post_type'] ?? '';
		if ( 'edit.php' != $pagenow || empty( $post_type ) || ! $this->check_order_post_type( $post_type ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'order', MB_CPT_URL . 'assets/order.css', [], MB_CPT_VER );
		wp_enqueue_script( 'order', MB_CPT_URL . 'assets/order.js', [], MB_CPT_VER, true );
		wp_localize_script( 'order', 'MBCPT', [ 'security' => wp_create_nonce( 'order' ) ] );
	}

	private function refresh( string $post_type ): void {

		if ( $this->order_doing_ajax() ) {
			return;
		}

		global $wpdb;
		$query = $wpdb->prepare(
			"
			SELECT COUNT(*) AS cnt, MAX(menu_order) AS max, MIN(menu_order) AS min
			FROM $wpdb->posts
			WHERE post_type = %s AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
			",
			$post_type
		);

		$result = $wpdb->get_results( $query );

		if ( $result[0]->cnt == 0 || $result[0]->cnt == $result[0]->max ) {
			return;
		}

		$wpdb->query( 'SET @row_number = 0;' );
		$wpdb->query(
			"UPDATE $wpdb->posts as pt JOIN (

			SELECT ID, (@row_number:=@row_number + 1) AS `rank`
			FROM $wpdb->posts
			WHERE post_type = '$post_type' AND post_status IN ( 'publish', 'pending', 'draft', 'private', 'future' )
			ORDER BY menu_order ASC
		) as pt2
		ON pt.id = pt2.id
		SET pt.menu_order = pt2.`rank`;"
		);
	}

	public function update_menu_order(): void {
		check_ajax_referer( 'order', 'security' );

		global $wpdb;
		parse_str( $_POST['order'], $data );
		if ( ! is_array( $data ) ) {
			return;
		}

		$id_arr = [];
		foreach ( $data as $values ) {
			foreach ( $values as $id ) {
				$id_arr[] = $id;
			}
		}

		$menu_order_arr = [];
		foreach ( $id_arr as $id ) {
			$id          = intval( $id );
			$sql         = "SELECT menu_order FROM $wpdb->posts WHERE ID = %d";
			$menu_orders = $wpdb->get_col( $wpdb->prepare( $sql, $id ) );
			foreach ( $menu_orders as $menu_order ) {
				$menu_order_arr[] = $menu_order;
			}
		}

		sort( $menu_order_arr );

		foreach ( $data as $values ) {
			foreach ( $values as $position => $id ) {
				$wpdb->update(
					$wpdb->posts,
					[ 'menu_order' => $menu_order_arr[ $position ] ],
					[ 'ID' => intval( $id ) ],
					[ '%d' ],
					[ '%d' ]
				);
			}
		}
	}

	public function order_pre_get_posts( WP_Query $wp_query ): void {
		$post_type = $wp_query->query['post_type'] ?? '';

		if ( ! $post_type || ! $this->check_order_post_type( $post_type ) ) {
			return;
		}
		if ( is_admin() ) {
			$this->refresh( $post_type );
			add_filter( "manage_{$post_type}_posts_columns", [ $this, 'order_custom_columns_list' ] );
			add_action( "manage_{$post_type}_posts_custom_column", [ $this, 'order_custom_column_values' ] );
		}
		if ( ! $wp_query->get( 'orderby' ) ) {
			$wp_query->set( 'orderby', 'menu_order' );
		}
		if ( ! $wp_query->get( 'order' ) ) {
			$wp_query->set( 'order', 'ASC' );
		}
	}

	public function order_custom_columns_list( array $columns ): array {
		return [ 'mbcpt_order' => '' ] + $columns;
	}

	public function order_custom_column_values( string $name ): void {
		if ( $name == 'mbcpt_order' ) {
			echo '<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.375 3.67c0-.645-.56-1.17-1.25-1.17s-1.25.525-1.25 1.17c0 .646.56 1.17 1.25 1.17s1.25-.524 1.25-1.17zm0 8.66c0-.646-.56-1.17-1.25-1.17s-1.25.524-1.25 1.17c0 .645.56 1.17 1.25 1.17s1.25-.525 1.25-1.17zm-1.25-5.5c.69 0 1.25.525 1.25 1.17 0 .645-.56 1.17-1.25 1.17S4.875 8.645 4.875 8c0-.645.56-1.17 1.25-1.17zm5-3.16c0-.645-.56-1.17-1.25-1.17s-1.25.525-1.25 1.17c0 .646.56 1.17 1.25 1.17s1.25-.524 1.25-1.17zm-1.25 7.49c.69 0 1.25.524 1.25 1.17 0 .645-.56 1.17-1.25 1.17s-1.25-.525-1.25-1.17c0-.646.56-1.17 1.25-1.17zM11.125 8c0-.645-.56-1.17-1.25-1.17s-1.25.525-1.25 1.17c0 .645.56 1.17 1.25 1.17s1.25-.525 1.25-1.17z"/></svg>';
		}
	}

	public function order_previous_post_where( string $where ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$where = preg_replace( "/p.post_date < \'[0-9\-\s\:]+\'/i", "p.menu_order > '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	public function order_previous_post_sort( string $orderby ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$orderby = 'ORDER BY p.menu_order ASC LIMIT 1';
		}
		return $orderby;
	}

	public function order_next_post_where( string $where ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$where = preg_replace( "/p.post_date > \'[0-9\-\s\:]+\'/i", "p.menu_order < '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	public function order_next_post_sort( string $orderby ): string {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$orderby = 'ORDER BY p.menu_order DESC LIMIT 1';
		}
		return $orderby;
	}

	private function check_order_post_type( string $post_type ): bool {
		$post_types = get_post_type_object( $post_type );
		if ( empty( $post_types->order ) ) {
			return false;
		}
		return true;
	}

	private function order_doing_ajax(): bool {

		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		return false;
	}
}
