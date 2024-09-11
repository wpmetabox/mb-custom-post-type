<?php
namespace MBCPT;

class Order {
	public function __construct() {
		add_action( 'admin_init', [ $this, 'load_script_css_order' ] );
		add_action( 'wp_ajax_update_menu_order', [ $this, 'update_menu_order' ] );
		add_action( 'pre_get_posts', [ $this, 'order_pre_get_posts' ], 99 );
		add_filter( 'get_previous_post_where', [ $this, 'order_previous_post_where' ] );
		add_filter( 'get_previous_post_sort', [ $this, 'order_previous_post_sort' ] );
		add_filter( 'get_next_post_where', [ $this, 'order_next_post_where' ] );
		add_filter( 'get_next_post_sort', [ $this, 'order_next_post_sort' ] );
	}

	public function load_script_css_order() {

		global $pagenow;
		$post_type = $_GET['post_type'] ?? '';
		if ( 'edit.php' != $pagenow || empty( $post_type ) || ! $this->check_order_post_type( $post_type ) ) {
			return;
		}

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'order', MB_CPT_URL . 'assets/order.js', [], MB_CPT_VER, true );
		add_action( 'admin_print_styles', [ $this, 'print_order_style' ] );
		$this->refresh( $post_type );
	}

	public function print_order_style() {
		?>
		<style>
			.ui-sortable tr:hover {
				cursor : move;
			}

			.ui-sortable tr.alternate {
				background-color : #F9F9F9;
			}

			.ui-sortable tr.ui-sortable-helper {
				background-color : #F9F9F9;
				border-top       : 1px solid #DFDFDF;
			}
		</style>
		<?php
	}

	public function refresh( $post_type ) {

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

	public function update_menu_order() {
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

	public function order_pre_get_posts( $wp_query ) {
		$post_type = $wp_query->query['post_type'] ?? '';

		if ( ! $post_type || ! $this->check_order_post_type( $post_type ) ) {
			return;
		}
		if ( ! $wp_query->get( 'orderby' ) ) {
			$wp_query->set( 'orderby', 'menu_order' );
		}
		if ( ! $wp_query->get( 'order' ) ) {
			$wp_query->set( 'order', 'ASC' );
		}
	}

	public function order_previous_post_where( $where ) {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$where = preg_replace( "/p.post_date < \'[0-9\-\s\:]+\'/i", "p.menu_order > '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	public function order_previous_post_sort( $orderby ) {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$orderby = 'ORDER BY p.menu_order ASC LIMIT 1';
		}
		return $orderby;
	}

	public function order_next_post_where( $where ) {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$where = preg_replace( "/p.post_date > \'[0-9\-\s\:]+\'/i", "p.menu_order < '" . $post->menu_order . "'", $where );
		}
		return $where;
	}

	public function order_next_post_sort( $orderby ) {
		global $post;

		if ( ! empty( $post->post_type ) && $this->check_order_post_type( $post->post_type ) ) {
			$orderby = 'ORDER BY p.menu_order DESC LIMIT 1';
		}
		return $orderby;
	}

	public function check_order_post_type( $post_type ) {
		$post_types = get_post_type_object( $post_type );
		if ( empty( $post_types->order ) ) {
			return false;
		}
		return true;
	}

	public function order_doing_ajax() {

		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return true;
		}

		return;
	}
}
