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

		$object = str_replace( 'mb-', '', $this->post_type );
		wp_enqueue_code_editor( ['type' => 'application/x-httpd-php'] );
		wp_enqueue_script( $this->post_type, MB_CPT_URL . "assets/$object.js", ['wp-element', 'wp-components', 'wp-i18n', 'clipboard'], MB_CPT_VER, true );
		wp_localize_script( $this->post_type, 'MBCPT', $this->js_vars() );
		wp_set_script_translations( $this->post_type, 'mb-custom-post-type' );
	}

	private function js_vars() {
		$vars = [
			'icons'    => $this->get_icons(),
			'settings' => json_decode( get_post()->post_content, ARRAY_A ),
		];

		if ( 'mb-post-type' === get_current_screen()->id ) {
			$taxonomies = get_taxonomies( '', 'objects' );
			$taxonomies = array_diff_key( $taxonomies, array_flip( [
				'nav_menu',
				'link_category',
			] ) );

			$options = [];
			foreach ( $taxonomies as $slug => $taxonomy ) {
				$options[ $slug ] = $taxonomy->labels->singular_name;
			}
			$vars['taxonomies'] = $options;
			$vars['menu_position_options'] = $this->get_menu_position_options();
			$vars['show_in_menu_options'] = $this->get_show_in_menu_options();
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
				$options[ $slug ] = $post_type->labels->singular_name;
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

	private function get_icons() {
		return [
			'admin-appearance',
			'admin-collapse',
			'admin-comments',
			'admin-generic',
			'admin-home',
			'admin-links',
			'admin-media',
			'admin-network',
			'admin-page',
			'admin-plugins',
			'admin-post',
			'admin-settings',
			'admin-site',
			'admin-tools',
			'admin-users',
			'album',
			'align-center',
			'align-left',
			'align-none',
			'align-right',
			'analytics',
			'archive',
			'arrow-down-alt2',
			'arrow-down-alt',
			'arrow-down',
			'arrow-left-alt2',
			'arrow-left-alt',
			'arrow-left',
			'arrow-right-alt2',
			'arrow-right-alt',
			'arrow-right',
			'arrow-up-alt2',
			'arrow-up-alt',
			'arrow-up',
			'art',
			'awards',
			'backup',
			'book-alt',
			'book',
			'building',
			'businessman',
			'calendar-alt',
			'calendar',
			'camera',
			'carrot',
			'cart',
			'category',
			'chart-area',
			'chart-bar',
			'chart-line',
			'chart-pie',
			'clipboard',
			'clock',
			'cloud',
			'controls-back',
			'controls-forward',
			'controls-pause',
			'controls-play',
			'controls-repeat',
			'controls-skipback',
			'controls-skipforward',
			'controls-volumeoff',
			'controls-volumeon',
			'dashboard',
			'desktop',
			'dismiss',
			'download',
			'editor-aligncenter',
			'editor-alignleft',
			'editor-alignright',
			'editor-bold',
			'editor-break',
			'editor-code',
			'editor-contract',
			'editor-customchar',
			'editor-distractionfree',
			'editor-expand',
			'editor-help',
			'editor-indent',
			'editor-insertmore',
			'editor-italic',
			'editor-justify',
			'editor-kitchensink',
			'editor-ol',
			'editor-outdent',
			'editor-paragraph',
			'editor-paste-text',
			'editor-paste-word',
			'editor-quote',
			'editor-removeformatting',
			'editor-rtl',
			'editor-spellcheck',
			'editor-strikethrough',
			'editor-textcolor',
			'editor-ul',
			'editor-underline',
			'editor-unlink',
			'editor-video',
			'edit',
			'email-alt',
			'email',
			'excerpt-view',
			'exerpt-view',
			'external',
			'facebook-alt',
			'facebook',
			'feedback',
			'flag',
			'format-aside',
			'format-audio',
			'format-chat',
			'format-gallery',
			'format-image',
			'format-links',
			'format-quote',
			'format-standard',
			'format-status',
			'format-video',
			'forms',
			'googleplus',
			'grid-view',
			'groups',
			'hammer',
			'heart',
			'id-alt',
			'id',
			'images-alt2',
			'images-alt',
			'image-crop',
			'image-flip-horizontal',
			'image-flip-vertical',
			'image-rotate-left',
			'image-rotate-right',
			'index-card',
			'info',
			'leftright',
			'lightbulb',
			'list-view',
			'location-alt',
			'location',
			'lock',
			'marker',
			'media-archive',
			'media-audio',
			'media-code',
			'media-default',
			'media-document',
			'media-interactive',
			'media-spreadsheet',
			'media-text',
			'media-video',
			'megaphone',
			'menu',
			'microphone',
			'migrate',
			'minus',
			'money',
			'nametag',
			'networking',
			'no-alt',
			'no',
			'palmtree',
			'performance',
			'phone',
			'playlist-audio',
			'playlist-video',
			'plus-alt',
			'plus',
			'portfolio',
			'post-status',
			'post-trash',
			'pressthis',
			'products',
			'randomize',
			'redo',
			'rss',
			'schedule',
			'screenoptions',
			'search',
			'share1',
			'share-alt2',
			'share-alt',
			'share',
			'shield-alt',
			'shield',
			'slides',
			'smartphone',
			'smiley',
			'sort',
			'sos',
			'star-empty',
			'star-filled',
			'star-half',
			'store',
			'tablet',
			'tagcloud',
			'tag',
			'testimonial',
			'text',
			'tickets-alt',
			'tickets',
			'translation',
			'trash',
			'twitter',
			'undo',
			'universal-access-alt',
			'universal-access',
			'update',
			'upload',
			'vault',
			'video-alt2',
			'video-alt3',
			'video-alt',
			'visibility',
			'welcome-add-page',
			'welcome-comments',
			'welcome-edit-page',
			'welcome-learn-more',
			'welcome-view-site',
			'welcome-widgets-menus',
			'welcome-write-blog',
			'wordpress-alt',
			'wordpress',
		];
	}

	private function get_show_in_menu_options() {
		global $menu;
		$options = [
			[
				'value' => 'true',
				'label' => esc_html__( 'Show as top-level menu', 'mb-custom-post-type' ),
			],
			[
				'value' => 'false',
				'label' => esc_html__( 'Do not show in the admin menu', 'mb-custom-post-type' ),
			],
		];
		foreach ( $menu as $params ) {
			if ( ! empty( $params[0] ) && ! empty( $params[2] ) ) {
				// Translators: %s is the main menu label.
				$options[] = [
					'value' => $params[2],
					'label' => sprintf( __( 'Show as sub-menu of %s', 'mb-custom-post-type' ), $this->strip_span( $params[0] ) ),
				];
			}
		}
		return $options;
	}

	private function get_menu_position_options() {
		global $menu;
		$positions = [];
		foreach ( $menu as $position => $params ) {
			if ( ! empty( $params[0] ) ) {
				$positions[] = [
					'value' => $position,
					'label' => $this->strip_span( $params[0] ),
				];
			}
		}
		return $positions;
	}

	private function strip_span( $html ) {
		return preg_replace( '@<span .*>.*</span>@si', '', $html );
	}
}
