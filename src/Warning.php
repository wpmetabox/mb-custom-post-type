<?php
namespace MBCPT;

class Warning {
	public function __construct() {
		if ( get_option( 'permalink_structure' ) === '' ) {
			add_action( 'admin_notices', [ $this, 'permalink_warning' ] );
		}
	}

	public function permalink_warning() {
		?>
		<div class="notice notice-warning">
			<?php // Translators: %s - URl to permalink settings page ?>
			<p><?php echo wp_kses_post( sprintf( __( 'Permalink is not set. It\'s recommended to set permalink for custom post types. <a href="%s">Set it here.</a>', 'mb-custom-post-type' ), admin_url( 'options-permalink.php' ) ) ); ?></p>
		</div>
		<?php
	}
}
