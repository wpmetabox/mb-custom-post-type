<?php
namespace MBCPT;

class TaxonomyReorder {

	public function __construct() {
		if ( ! get_option( 'add_term_order_column' ) ) {
			$this->add_term_order_column();
		}
		add_action( 'admin_print_styles-edit-tags.php', [ $this, 'setup_for_edit_screen' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'wp_ajax_mb_cpt_save_order_terms', [ $this, 'save_order' ] );
		add_filter( 'get_terms_orderby', [ $this, 'order_get_terms_orderby' ], 10, 3 );
		add_filter( 'wp_get_object_terms', [ $this, 'order_get_object_terms' ], 10, 3 );
		add_filter( 'get_terms', [ $this, 'order_get_object_terms' ], 10, 3 );
	}

	public function setup_for_edit_screen(): void {
		$screen = get_current_screen();
		$mode   = $_GET['mode'] ?? 'default';
		if ( $screen->base !== 'edit-tags' || $mode !== 'sortable' ) {
			return;
		}
		// Set initial orders
		$this->set_initial_orders( $screen->taxonomy );
	}

	public function enqueue_scripts(): void {
		// Get current post type from screen
		$screen          = get_current_screen();
		$taxonomy        = $screen->taxonomy;
		$taxonomy_object = get_taxonomy( $taxonomy );

		if ( ! $this->reorderable( $taxonomy ) ) {
			return;
		}

		$hierarchical = (bool) $taxonomy_object->hierarchical;

		add_filter( "views_edit-{$taxonomy}", [ $this, 'add_toggle_sortable_button' ] );

		// Enqueue SortableJS
		wp_enqueue_script(
			'sortablejs',
			'https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js',
			[],
			'1.15.6',
			true
		);

		// Enqueue our custom script
		wp_enqueue_script(
			'mb-cpt-order-script',
			MB_CPT_URL . 'assets/taxonomy-order.js',
			[ 'jquery', 'sortablejs' ],
			filemtime( MB_CPT_DIR . '/assets/taxonomy-order.js' ),
			true
		);
		// Get pagination info
		$per_page = (int) get_user_option( 'edit-' . $taxonomy . '_per_page', get_current_user_id() );
		if ( ! $per_page ) {
			$per_page = 20; // Default value if not set in Screen Options
		}
		$current_page = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
		$terms        = get_terms([
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		]);
		$all_terms    = [];

		// For hierarchical queries, we don't have term_order so we use the index + 1 for the order
		foreach ( $terms as $index => $term ) {
			$term->term_order = $term->term_order ?? $index + 1;
			$all_terms[]      = $term;
		}

		// Order by menu_order, asc
		usort( $all_terms, function ( $a, $b ) {
			return $a->term_order <=> $b->term_order;
		} );

		$terms = $hierarchical ? $this->get_hierarchical_terms( $taxonomy, $all_terms, $current_page, $per_page ) : $all_terms;

		// Localize script with the queried posts
		wp_localize_script( 'mb-cpt-order-script', 'MB_CPT_ORDER_TERMS', [
			'terms'        => $terms,
			'nonce'        => wp_create_nonce( 'mb_cpt_order_terms_nonce' ),
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

	private function get_hierarchical_terms( string $taxonomy, array $all_terms, int $current_page, int $per_page ): array {
		// Filter top-level terms (parent = 0) for pagination
		$top_level_posts = array_filter( $all_terms, function ( $post ) {
			return $post->parent == 0;
		} );
		$top_level_ids   = wp_list_pluck( $top_level_posts, 'term_id' );

		// Split top-level IDs into chunks
		$top_level_chunks      = array_chunk( $top_level_ids, $per_page );
		$current_top_level_ids = $top_level_chunks[ $current_page - 1 ] ?? [];

		// Build a list of IDs to fetch: current top-level IDs + all their descendants
		$term_ids_to_fetch = $current_top_level_ids;
		$all_term_map      = array_column( $all_terms, null, 'term_id' ); // Map for quick lookup
		foreach ( $current_top_level_ids as $parent_id ) {
			// Recursively find all children
			$term_ids_to_fetch = array_merge( $term_ids_to_fetch, $this->get_children( $parent_id, $all_term_map ) );
		}

		$term_ids_to_fetch = array_unique( $term_ids_to_fetch ); // Remove duplicates

		// Fetch full post data only if we have IDs to fetch
		$terms = [];
		if ( ! empty( $term_ids_to_fetch ) ) {
			$args = [
				'taxonomy'   => $taxonomy,
				'include'    => $term_ids_to_fetch,
				'hide_empty' => false,
				'orderby'    => 'include',
				'number'     => 0,
			];

			$full_query = get_terms( $args );
			$terms      = array_map( function ( $term ) use ( $all_term_map ) {
				return [
					'term_id'    => $term->term_id,
					'name'       => $term->name,
					'parent'     => $term->parent,
					'term_order' => $term->term_order == 0 ? $all_term_map[ $term->ID ]->term_order : $term->term_order,
				];
			}, $full_query);
		}

		return $terms;
	}

	private function set_initial_orders( string $taxonomy ): void {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Error.
		$result = $wpdb->get_row( $wpdb->prepare(
			"
			SELECT COUNT(*) AS total, MAX(term_order) AS max
			FROM $wpdb->terms AS terms
			INNER JOIN $wpdb->term_taxonomy AS term_taxonomy ON ( terms.term_id = term_taxonomy.term_id )
			WHERE term_taxonomy.taxonomy = %s
			",
			$taxonomy
		) );

		if ( $result->total == 0 || $result->total == $result->max ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			return;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Error.
		$query   = $wpdb->prepare(
			"
				SELECT terms.term_id
				FROM $wpdb->terms AS terms
				INNER JOIN $wpdb->term_taxonomy AS term_taxonomy ON ( terms.term_id = term_taxonomy.term_id )
				WHERE term_taxonomy.taxonomy = %s
				ORDER BY term_order ASC
				",
			$taxonomy
		);
		$results = $wpdb->get_col( $query ); // Passage en requette préparée
		foreach ( $results as $key => $result ) {
			$wpdb->update( $wpdb->terms, [ 'term_order' => $key + 1 ], [ 'term_id' => $result ] );
		}
	}

	private function get_children( int $parent_id, array $term_map ): array {
		$children = [];
		foreach ( $term_map as $term ) {
			if ( $term->parent == $parent_id ) {
				$children[] = $term->term_id;
				$children   = array_merge( $children, $this->get_children( $term->term_id, $term_map ) );
			}
		}

		return $children;
	}

	// Add toggle sortable link to views
	public function add_toggle_sortable_button( array $views ): array {
		$screen    = get_current_screen();
		$post_type = $screen->post_type;
		$taxonomy  = $screen->taxonomy;
		$mode      = $_GET['mode'] ?? 'default';
		$url       = add_query_arg( [
			'taxonomy'  => $taxonomy,
			'post_type' => $post_type,
			'mode'      => $mode === 'sortable' ? 'default' : 'sortable',
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
	public function save_order(): void {
		global $wpdb;

		check_ajax_referer( 'mb_cpt_order_terms_nonce', 'nonce' );

		if ( ! current_user_can( 'edit_pages' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'mb-custom-post-type' ) );
		}

		$order_data = json_decode( wp_unslash( $_POST['order_data'] ), true );

		if ( ! is_array( $order_data ) || empty( $order_data ) ) {
			wp_send_json_error( __( 'Invalid order data', 'mb-custom-post-type' ) );
		}

		foreach ( $order_data as $item ) {
			$wpdb->update(
				$wpdb->terms,
				[
					'term_order' => $item['order'],
				],
				[ 'term_id' => $item['id'] ]
			);
			$wpdb->update(
				$wpdb->term_taxonomy,
				[
					'parent' => $item['parent_id'] ? (int) $item['parent_id'] : 0,
				],
				[ 'term_id' => $item['id'] ]
			);
			clean_term_cache( $item['id'] );
		}

		wp_send_json_success( __( 'Order updated', 'mb-custom-post-type' ) );
	}

	public function order_get_terms_orderby( string $orderby, array $args ): string {
		if ( ! isset( $args['taxonomy'] ) ) {
			return $orderby;
		}

		$taxonomy = $args['taxonomy'];

		if ( is_array( $args['taxonomy'] ) ) {
			$taxonomy = reset( $args['taxonomy'] );
		}

		return is_string( $taxonomy ) && $this->reorderable( $taxonomy ) ? 't.term_order' : $orderby;
	}

	public function order_get_object_terms( $terms ) {

		if ( ! is_array( $terms ) ) {
			return $terms;
		}

		foreach ( $terms as  $term ) {
			if ( ! is_object( $term ) || ! isset( $term->taxonomy ) ) {
				return $terms;
			}

			$taxonomy = $term->taxonomy;
			if ( ! $this->reorderable( $taxonomy ) ) {
				return $terms;
			}
		}

		usort( $terms, function ( $a, $b ) {
			return $a->term_order <=> $b->term_order;
		} );

		return $terms;
	}

	private function reorderable( string $taxonomy ): bool {
		$taxonomy_object = get_taxonomy( $taxonomy );
		$enabled         = $taxonomy_object && ! empty( $taxonomy_object->order );
		return apply_filters( 'mbcpt_taxonomy_reorderable', $enabled, $taxonomy );
	}

	public function add_term_order_column(): void {
		global $wpdb;
		$result = $wpdb->query( "DESCRIBE $wpdb->terms `term_order`" );
		if ( ! $result ) {
			$query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
			$wpdb->query( $query );
		}
		update_option( 'add_term_order_column', 1 );
	}
}
