<?php
namespace MBCPT;

class Import {

	public function __construct() {
		add_action( 'admin_footer-edit.php', [ $this, 'output_js_templates' ] );
		add_action( 'admin_print_styles-edit.php', [ $this, 'enqueue' ] );
		add_action( 'admin_init', [ $this, 'import' ] );
	}

	public function enqueue() {
		if ( ! in_array( get_current_screen()->id, [ 'edit-mb-post-type', 'edit-mb-taxonomy' ] ) ) {
			return;
		}

		wp_enqueue_style( 'mbcpt-import', MB_CPT_URL . 'assets/import.css', [], MB_CPT_VER );
		wp_enqueue_script( 'mbcpt-import', MB_CPT_URL . 'assets/import.js', [ 'wp-element', 'wp-components', 'wp-i18n', 'clipboard' ], MB_CPT_VER, true );

		wp_localize_script( 'mbcpt-import', 'MBCPT', [
			'export' => esc_html__( 'Export', 'mb-custom-post-type' ),
			'import' => esc_html__( 'Import', 'mb-custom-post-type' ),
		] );
	}

	public function output_js_templates() {
		if ( ! in_array( get_current_screen()->id, [ 'edit-mb-post-type', 'edit-mb-taxonomy' ] ) ) {
			return;
		}
		?>
		<?php if ( isset( $_GET['imported'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Post types have been imported successfully!', 'mb-custom-post-type' ); ?></p></div>
		<?php endif; ?>

		<script type="text/template" id="mbcpt-import-form">
			<div class="mbcpt-import-form">
				<p><?php esc_html_e( 'Choose an exported ".json" file from your computer:', 'mb-custom-post-type' ); ?></p>
				<form enctype="multipart/form-data" method="post" action="">
					<?php wp_nonce_field( 'import', 'nonce' ); ?>
					<input type="file" name="mbcpt_file">
					<input type="hidden" name="mbcpt_post_type" value=" <?php echo esc_attr( get_current_screen()->post_type ) ?> ">
					<?php submit_button( esc_attr__( 'Import', 'mb-custom-post-type' ), 'secondary', 'submit', false, [ 'disabled' => true ] ); ?>
				</form>
			</div>
		</script>
		<?php
	}

	public function import() {
		// No file uploaded.
		if ( empty( $_FILES['mbcpt_file'] ) || empty( $_FILES['mbcpt_file']['tmp_name'] ) || empty( $_REQUEST['mbcpt_post_type'] ) ) {
			return;
		}

		$url = admin_url( 'edit.php?post_type=' . str_replace( ' ', '', wp_unslash( $_REQUEST['mbcpt_post_type'] ) ) );

		// Verify nonce.
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! wp_verify_nonce( $nonce, 'import' ) ) {
			// Translators: %s - go back URL.
			wp_die( wp_kses_post( sprintf( __( 'Invalid form submit. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $url ) ) );
		}

		$data = file_get_contents( wp_unslash( $_FILES['mbcpt_file']['tmp_name'] ) );

		$result = $this->import_json( $data );

		if ( ! $result ) {
			// Translators: %s - go back URL.
			wp_die( wp_kses_post( sprintf( __( 'Invalid file data. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $url ) ) );
		}

		$url = add_query_arg( 'imported', 'true', $url );
		wp_safe_redirect( $url );
		die;
	}

	private function import_json( $data ) {
		$posts = json_decode( $data, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return false;
		}

		// If import only one post.
		if ( isset( $posts['post_title'] ) ) {
			$posts = [ $posts ];
		}

		foreach ( $posts as $post ) {
			$post['post_content'] = wp_json_encode( $post['settings'] );

			$post_id              = wp_insert_post( $post );
			if ( ! $post_id ) {
				wp_die( wp_kses_post( sprintf(
					// Translators: %s - go back URL.
					__( 'Cannot import the post type <strong>%1$s</strong>. <a href="%2$s">Go back</a>.', 'mb-custom-post-type' ),
					$post['title'],
					admin_url( 'edit.php?post_type=' . str_replace( ' ', '', wp_unslash( $_REQUEST['mbcpt_post_type'] ) ) )
				) ) );
			}
			if ( is_wp_error( $post_id ) ) {
				wp_die( wp_kses_post( implode( '<br>', $post_id->get_error_messages() ) ) );
			}
		}

		return true;
	}

}
