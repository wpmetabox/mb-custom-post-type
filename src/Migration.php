<?php
namespace MBCPT;

class Migration {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
	}

	public function add_menu() {
		$slug      = defined( 'RWMB_VER' ) ? 'meta-box' : 'edit.php?post_type=mb-post-type';
		$page_hook = add_submenu_page(
			$slug,
			esc_html__( 'CPT UI Migration', 'mb-custom-post-type' ),
			esc_html__( 'CPT UI Migration', 'mb-custom-post-type' ),
			'manage_options',
			'mb-migrate-post-type',
			[ $this, 'render' ]
		);
		add_action( "admin_print_styles-$page_hook", [ $this, 'enqueue' ] );
	}

	public function enqueue() {
		wp_enqueue_script( 'mb-cpt', MB_CPT_URL . 'assets/migrate.js', [], MB_CPT_VER, true );
		wp_localize_script( 'mb-cpt', 'MbCpt', [
			'start'               => __( 'Start...', 'mb-custom-post-type' ),
			'migratingPostTypes'  => __( 'Migrating post types...', 'mb-custom-post-type' ),
			'migratingTaxonomies' => __( 'Migrating taxonomies...', 'mb-custom-post-type' ),
			'deactivate'          => __( 'Deactivating plugin CPT UI...', 'mb-custom-post-type' ),
			'done'                => __( 'Done!', 'mb-custom-post-type' ),
		] );
	}

	public function render() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ) ?></h1>
			<p>
				<button class="button button-primary" id="migrate-button"><?php esc_html_e( 'Migrate', 'mb-custom-post-type' ) ?></button>
			</p>
			<h2><?php esc_html_e( 'Notes:', 'mb-custom-post-type' ) ?></h2>
			<ul>
				<li><?php esc_html_e( 'Always backup your database first as the plugin will remove/replace the existing CPT UI data. If you find any problem, restore the database and report us. We can\'t help you if you don\'t backup the database and there\'s something wrong.', 'mb-custom-post-type' ) ?></li>
				<li><?php esc_html_e( 'Not all data types and settings in CPT UI have an equivalent in Meta Box CPT. The plugin will try to migrate as much as it can.', 'mb-custom-post-type' ) ?></li>
			</ul>
			<div id="migrate-status"></div>
			<div id="migrate-links" style="display: none">
				<a href="<?= esc_url( admin_url( 'edit.php?post_type=mb-post-type' ) ) ?>"><?php esc_html_e( 'View post types', 'mb-custom-post-type' ) ?> &rarr;</a> |
				<a href="<?= esc_url( admin_url( 'edit.php?post_type=mb-taxonomy' ) ) ?>"><?php esc_html_e( 'View taxonomies', 'mb-custom-post-type' ) ?> &rarr;</a>
			</div>
		</div>
		<?php
	}
}
