<?php
namespace MBCPT;

class Edit {
	private $post_type;

	public function __construct( $post_type ) {
		$this->post_type = $post_type;

		add_action( 'edit_form_after_title', [ $this, 'output_root' ] );
		add_action( 'add_meta_boxes', [ $this, 'register_upgrade_meta_box' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public function output_root() {
		if ( $this->is_screen() ) {
			echo '<div id="root" class="mb-cpt"></div>';
		}
	}

	public function register_upgrade_meta_box( $meta_boxes ) {
		if ( $this->is_screen() && ! $this->is_premium_user() ) {
			add_meta_box( 'mb-cpt-upgrade', __( 'Upgrade', 'mb-custom-post-type' ), [ $this, 'upgrade_message' ], null, 'side', 'low' );
		}
	}

	public function upgrade_message() {
		?>
		<p><?php esc_html_e( 'Upgrade now to have more features & speedy technical support:', 'mb-custom-post-type' ) ?></p>
		<ul>
			<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Create custom fields with UI', 'mb-custom-post-type' ) ?></li>
			<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Add custom fields to terms and users', 'mb-custom-post-type' ) ?></li>
			<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Create custom settings pages', 'mb-custom-post-type' ) ?></li>
			<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Create frontend submission forms', 'mb-custom-post-type' ) ?></li>
			<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'Create frontend templates', 'mb-custom-post-type' ) ?></li>
			<li><span class="dashicons dashicons-yes"></span><?php esc_html_e( 'And much more!', 'mb-custom-post-type' ) ?></li>
		</ul>
		<a href="https://metabox.io/pricing/?utm_source=plugin_cpt&utm_medium=btn_upgrade&utm_campaign=cpt_upgrade" class="button" target="_blank" rel="noopenner noreferer"><?php esc_html_e( 'Upgrade now', 'mb-custom-post-type' ) ?></a>
		<?php
	}

	public function enqueue_scripts() {
		if ( ! $this->is_screen() ) {
			return;
		}

		wp_enqueue_style( $this->post_type, MB_CPT_URL . 'assets/style.css', ['wp-components'], MB_CPT_VER );

		$object      = str_replace( 'mb-', '', $this->post_type );
		$object_name = str_replace( ' ', '', ucwords( str_replace( '-', ' ', $this->post_type ) ) );
		wp_enqueue_code_editor( ['type' => 'application/x-httpd-php'] );
		wp_enqueue_script( $this->post_type, MB_CPT_URL . "assets/$object.js", ['wp-element', 'wp-components', 'clipboard', 'wp-i18n'], MB_CPT_VER, true );
		wp_localize_script( $this->post_type, $object_name, $this->js_vars() );
		wp_set_script_translations( $this->post_type, 'mb-custom-post-type' );
	}

	private function js_vars() {
		$vars = [];
		$vars['settings'] = json_decode( get_post()->post_content, ARRAY_A );

		if ( 'mb-post-type' === get_current_screen()->id ) {
			$taxonomies = get_taxonomies( '', 'objects' );
			$taxonomies = array_diff_key( $taxonomies, array_flip( [
				'nav_menu',
				'link_category',
			] ) );

			$options = [];
			foreach ( $taxonomies as $slug => $taxonomy ) {
				$options[] = [
					'value'   => $slug,
					'label'   => $taxonomy->labels->singular_name,
					'checked' => false,
				];
			}
			$vars['taxonomies'] = $options;
		}

		if ( 'mb-taxonomy' === get_current_screen()->id ) {
			$post_types = get_post_types( '', 'objects' );
			$post_types = array_diff_key( $post_types, array_flip( [
				'custom_css',
				'customize_changeset',
				'oembed_cache',
				'nav_menu_item',
				'revision',
				'user_request',
				'wp_block',

				'mb-post-type',
				'mb-taxonomy',
				'mb-views',
				'meta-box',
			] ) );

			$options    = [];
			foreach ( $post_types as $slug => $post_type ) {
				$options[] = [
					'value'   => $slug,
					'label'   => $post_type->labels->singular_name,
					'checked' => 'post' === $slug,
				];
			}
			$vars['types'] = $options;
		}

		return $vars;
	}

	private function is_screen() {
		$screen = get_current_screen();
		return 'post' === $screen->base && $this->post_type === $screen->post_type;
	}

	private function is_premium_user() {
		if ( ! defined( 'RWMB_VER' ) ) {
			return false;
		}
		$update_option = new \RWMB_Update_Option();
		$update_checker = new \RWMB_Update_Checker( $update_option );
		return $update_checker->has_extensions();
	}
}
