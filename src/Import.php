<?php
namespace MBCPT;

class Import {

	public function __construct() {
		add_action( 'admin_footer-edit.php', [ $this, 'output_js_templates' ] );
		add_action( 'admin_print_styles-edit.php', [ $this, 'enqueue' ] );
		add_action( 'admin_init', [ $this, 'import' ] );
	}

	public function enqueue() {
		if ( 'edit-mb-post-type' !== get_current_screen()->id ) {
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
		if ( 'edit-mb-post-type' !== get_current_screen()->id ) {
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
					<?php submit_button( esc_attr__( 'Import', 'mb-custom-post-type' ), 'secondary', 'submit', false, ['disabled' => true] ); ?>
				</form>
			</div>
		</script>
		<?php
	}

	public function import() {
		// No file uploaded.
		if ( empty( $_FILES['mbcpt_file'] ) || empty( $_FILES['mbcpt_file']['tmp_name'] ) ) {
			return;
		}

		$url = admin_url( 'edit.php?post_type=mb-post-type' );

		// Verify nonce.
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! wp_verify_nonce( $nonce, 'import' ) ) {
			wp_die( sprintf( __( 'Invalid form submit. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $url ) );
		}

		$data = file_get_contents( $_FILES['mbcpt_file']['tmp_name'] );

		$result = $this->import_json( $data );

		if ( ! $result ) {
			wp_die( sprintf( __( 'Invalid file data. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $url ) );
		}

		$url = add_query_arg( 'imported', 'true', $url );
		wp_safe_redirect( $url );
		die;
	}

	/**
	 * Import .json from v4.
	 */
	private function import_json( $data ) {
		$posts = json_decode( $data, true );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			return false;
		}

		// If import only one post.
		if ( isset( $posts['ID'] ) ) {
			$posts = [ $posts ];
		}

		foreach ( $posts as $post ) {
			unset( $post['ID'] );
			$post_id = wp_insert_post( $post );
			if ( ! $post_id ) {
				wp_die( sprintf( __( 'Cannot import the post types <strong>%s</strong>. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $post['post_title'], $url ) );
			}
			if ( is_wp_error( $post_id ) ) {
				wp_die( implode( '<br>', $post_id->get_error_messages() ) );
			}

			$check_duplicate_posts = get_posts( [
				'post_type' => 'mb-post-type',
				'post_name' => $post['post_name'],
			] );

			if ( count( $check_duplicate_posts ) === 0 ) {
				continue;
			}

			$post_detail = get_post( $post_id );
			$post_content = json_decode( $post_detail->post_content, true );

			$post_content['slug'] = $post_detail->post_name;
			$post_detail->post_content = json_encode( $post_content );

			wp_update_post( $post_detail );
		}

		return true;
	}

}
