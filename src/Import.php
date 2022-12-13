<?php
namespace MBCPT;

use MBB\Upgrade\Ver404;

class Import {
	private $upgrader_v4;

	public function __construct() {
		$this->upgrader_v4 = new Ver404;

		add_action( 'admin_footer-edit.php', [ $this, 'output_js_templates' ] );
		add_action( 'admin_init', [ $this, 'import' ] );
	}

	public function output_js_templates() {
		if ( 'edit-mb-post-type' !== get_current_screen()->id ) {
			return;
		}
		?>
		<?php if ( isset( $_GET['imported'] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Field groups have been imported successfully!', 'mb-custom-post-type' ); ?></p></div>
		<?php endif; ?>

		<script type="text/template" id="mbb-import-form">
			<div class="mbb-import-form">
				<p><?php esc_html_e( 'Choose an exported ".json" file from your computer:', 'mb-custom-post-type' ); ?></p>
				<form enctype="multipart/form-data" method="post" action="">
					<?php wp_nonce_field( 'import', 'nonce' ); ?>
					<input type="file" name="mbb_file">
					<?php submit_button( esc_attr__( 'Import', 'mb-custom-post-type' ), 'secondary', 'submit', false, ['disabled' => true] ); ?>
				</form>
			</div>
		</script>
		<?php
	}

	public function import() {
		// No file uploaded.
		if ( empty( $_FILES['mbb_file'] ) || empty( $_FILES['mbb_file']['tmp_name'] ) ) {
			return;
		}

		$url = admin_url( 'edit.php?post_type=mb-post-type' );

		// Verify nonce.
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! wp_verify_nonce( $nonce, 'import' ) ) {
			wp_die( sprintf( __( 'Invalid form submit. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $url ) );
		}

		$data = file_get_contents( $_FILES['mbb_file']['tmp_name'] );

		$result = $this->import_json( $data );
		if ( ! $result ) {
			$result = $this->import_dat( $data );
		}

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
				wp_die( sprintf( __( 'Cannot import the field group <strong>%s</strong>. <a href="%s">Go back</a>.', 'mb-custom-post-type' ), $post['post_title'], $url ) );
			}
			if ( is_wp_error( $post_id ) ) {
				wp_die( implode( '<br>', $post_id->get_error_messages() ) );
			}
			update_post_meta( $post_id, 'settings', $post['settings'] );
			update_post_meta( $post_id, 'fields', $post['fields'] );
			update_post_meta( $post_id, 'data', $post['data'] );
			update_post_meta( $post_id, 'meta_box', $post['meta_box'] );
		}

		return true;
	}

	/**
	 * Import .dat files from < v4.
	 */
	private function import_dat( $data ) {
		/**
		 * Removed excerpt_save_pre filter for meta box, which adds rel="noopener"
		 * to <a target="_blank"> links, thus braking JSON validity.
		 *
		 * @see https://elightup.freshdesk.com/a/tickets/27894
		 */
		remove_all_filters( 'excerpt_save_pre' );

		$meta_boxes = @unserialize( $data );
		if ( false === $meta_boxes ) {
			return false;
		}

		foreach ( $meta_boxes as $meta_box ) {
			$post    = unserialize( base64_decode( $meta_box ) );
			$excerpt = $post->post_excerpt;
			$excerpt = addslashes( $excerpt );

			$post_arr = (array) $post;
			$post_arr['post_excerpt'] = $excerpt;
			unset( $post_arr['ID'] );

			$post->ID = wp_insert_post( $post_arr );

			$this->upgrader_v4->migrate_post( $post );
		}

		return true;
	}
}
