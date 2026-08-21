<?php
namespace MBCPT;

class ListTableColumns {
	private $post_type;

	private const PREFIX = 'mbcpt-';

	private const SCREENS = [
		'edit-mb-post-type' => 'mb-post-type',
		'edit-mb-taxonomy'  => 'mb-taxonomy',
	];

	private const COLUMNS = [
		'mb-post-type' => [ 'slug', 'public', 'hierarchical', 'block_editor', 'has_archive' ],
		'mb-taxonomy'  => [ 'slug', 'public', 'hierarchical', 'types' ],
	];

	public function __construct() {
		add_action( 'admin_head-edit.php', [ $this, 'init' ] );
	}

	public function init(): void {
		$screen = get_current_screen();
		if ( ! isset( self::SCREENS[ $screen->id ] ) ) {
			return;
		}

		$this->post_type = self::SCREENS[ $screen->id ];
		$this->output_css();

		add_filter( "manage_{$this->post_type}_posts_columns", [ $this, 'columns' ] );
		add_action( "manage_{$this->post_type}_posts_custom_column", [ $this, 'output' ], 10, 2 );
	}

	public function columns( array $columns ): array {
		unset( $columns['date'] );

		$labels = [
			'slug'         => __( 'Slug', 'mb-custom-post-type' ),
			'public'       => __( 'Public', 'mb-custom-post-type' ),
			'hierarchical' => __( 'Hierarchical', 'mb-custom-post-type' ),
			'block_editor' => __( 'Block Editor', 'mb-custom-post-type' ),
			'has_archive'  => __( 'Archive', 'mb-custom-post-type' ),
			'types'        => __( 'Post Types', 'mb-custom-post-type' ),
		];

		$new_columns = [];
		foreach ( self::COLUMNS[ $this->post_type ] as $column ) {
			$new_columns[ self::PREFIX . $column ] = $labels[ $column ];
		}

		// Insert new columns after the checkbox and title columns.
		return array_merge( array_slice( $columns, 0, 2 ), $new_columns, array_slice( $columns, 2 ) );
	}

	public function output( string $column, int $post_id ): void {
		$column = str_replace( self::PREFIX, '', $column );

		if ( ! in_array( $column, self::COLUMNS[ $this->post_type ], true ) ) {
			return;
		}

		$settings = json_decode( get_post( $post_id )->post_content, true );
		$settings = is_array( $settings ) ? $settings : [];

		if ( $column === 'slug' ) {
			echo esc_html( (string) ( $settings['slug'] ?? '' ) );
			return;
		}

		if ( $column === 'types' ) {
			$this->output_post_types( $settings['types'] ?? [] );
			return;
		}

		$value = [
			'public'       => 'public',
			'hierarchical' => 'hierarchical',
			'block_editor' => 'show_in_rest',
			'has_archive'  => 'has_archive',
		][ $column ];

		$this->output_icon( ! empty( $settings[ $value ] ) );
	}

	private function output_post_types( $types ): void {
		if ( empty( $types ) ) {
			return;
		}

		$labels = [];
		foreach ( $types as $type ) {
			$post_type = get_post_type_object( $type );
			$labels[]  = $post_type ? $post_type->labels->singular_name : $type;
		}

		echo esc_html( implode( ', ', $labels ) );
	}

	private function output_css(): void {
		$selectors = [];
		foreach ( [ 'public', 'hierarchical', 'block_editor', 'has_archive' ] as $column ) {
			$selectors[] = '.column-' . self::PREFIX . $column;
		}
		$selectors    = implode( ', ', $selectors );
		$th_selectors = str_replace( '.column-', '.widefat thead th.column-', $selectors );
		?>
		<style>
			<?php echo esc_html( $selectors ); ?> { text-align: center; }
			<?php echo esc_html( $th_selectors ); ?> { text-align: center; }
			.mbcpt-icon--yes:after, .mbcpt-icon--no:after { display: inline-block; width: 12px; height: 12px; font-size: 8px; line-height: 12px; border-radius: 9px; color: #fff; text-align: center; }
			.mbcpt-icon--yes:after { content: "✓"; background: #70d028; }
			.mbcpt-icon--no:after { content: "✕"; background: #e53e3e; }
		</style>
		<?php
	}

	private function output_icon( bool $enabled ): void {
		if ( $enabled ) {
			echo '<span class="mbcpt-icon--yes"></span>';
		} else {
			echo '<span class="mbcpt-icon--no"></span>';
		}
	}
}
