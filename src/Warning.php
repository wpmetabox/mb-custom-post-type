<?php
namespace MBCPT;

class Warning {
	public function __construct() {
		if ( get_option( 'permalink_structure' ) == "" ) {
			//add_action( 'admin_notices', 'permalink_admin_notice__warning' );
			add_action( 'admin_notices', array( $this, 'permalink_admin_notice__warning' ) );
		}
	}
	
	public function permalink_admin_notice__warning() {
		?>
		<div class="notice notice-warning">
			<p><?php _e( 'Permalink has not been set. <a href="'.esc_url( admin_url('options-permalink.php') ).'">Permalink Setting</a>' ); ?></p>
		</div>
		<?php
	}
}
