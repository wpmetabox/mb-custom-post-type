<?php
class MB_CPT_About_Page {
	public function __construct() {
		add_action( 'rwmb_about_tabs', array( $this, 'add_tabs' ) );
		add_action( 'rwmb_about_tabs_content', array( $this, 'add_tabs_content' ) );
	}

	public function add_tabs() {
		?>
		<a href="#types-taxonomies" class="nav-tab"><?php esc_html_e( 'Post Types & Taxonomies', 'mb-custom-post-type' ); ?></a>
		<?php
	}

	public function add_tabs_content() {
		include __DIR__ . '/types-taxonomies.php';
	}
}
