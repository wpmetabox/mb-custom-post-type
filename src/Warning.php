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
			<p><?php _e( wp_kses_post( sprintf( __( 'Permarlink is not set. It\'s recommended to set permalink for custom post types. <a href="%s">Set it here.</a>', 'mb-custom-post-type' ), admin_url( 'options-permalink.php' ) ) ) ); ?></p>			
		</div>
		<?php
	}
}
