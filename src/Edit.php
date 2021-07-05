<?php
namespace MBCPT;

use MetaBox\Support\Data;

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
			'icons'         => Data::get_dashicons(),
			'settings'      => json_decode( get_post()->post_content, ARRAY_A ),
			'reservedTerms' => $this->get_reserved_terms(),
		];

		if ( 'mb-post-type' === get_current_screen()->id ) {
			$taxonomies = Data::get_taxonomies();
			$options    = [];
			foreach ( $taxonomies as $slug => $taxonomy ) {
				$options[ $slug ] = sprintf( '%s (%s)', $taxonomy->labels->singular_name, $slug );
			}
			$vars['taxonomies'] = $options;
			$vars['icon_type'] = [
				[
					'value' => 'dashicons',
					'label' => esc_html__( 'Dashicons', 'mb-custom-post-type' ),
				],
				[
					'value' => 'svg',
					'label' => esc_html__( 'SVG', 'mb-custom-post-type' ),
				],
				[
					'value' => 'custom',
					'label' => esc_html__( 'Custom URL', 'mb-custom-post-type' ),
				],
			];
			$vars['menu_position_options'] = $this->get_menu_position_options();
			$vars['show_in_menu_options'] = $this->get_show_in_menu_options();
		}

		if ( 'mb-taxonomy' === get_current_screen()->id ) {
			$post_types = Data::get_post_types();
			$options    = [];
			foreach ( $post_types as $slug => $post_type ) {
				$options[ $slug ] = sprintf( '%s (%s)', $post_type->labels->singular_name, $slug );
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
				$options[] = [
					'value' => $params[2],
					// Translators: %s is the main menu label.
					'label' => sprintf( __( 'Show as sub-menu of %s', 'mb-custom-post-type' ), $this->strip_span( $params[0] ) ),
				];
			}
		}
		return $options;
	}

	private function get_menu_position_options() {
		global $menu;
		$positions = [
			[
				'value' => '',
				'label' => __( 'Default', 'mb-custom-post-type' ),
			]
		];
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

	private function get_reserved_terms() {
		return [
			'action',
			'attachment',
			'attachment_id',
			'author',
			'author_name',
			'calendar',
			'cat',
			'category',
			'category__and',
			'category__in',
			'category__not_in',
			'category_name',
			'comments_per_page',
			'comments_popup',
			'custom',
			'customize_messenger_channel',
			'customized',
			'cpage',
			'day',
			'debug',
			'embed',
			'error',
			'exact',
			'feed',
			'fields',
			'hour',
			'link_category',
			'm',
			'minute',
			'monthnum',
			'more',
			'name',
			'nav_menu',
			'nonce',
			'nopaging',
			'offset',
			'order',
			'orderby',
			'p',
			'page',
			'page_id',
			'paged',
			'pagename',
			'pb',
			'perm',
			'post',
			'post__in',
			'post__not_in',
			'post_format',
			'post_mime_type',
			'post_status',
			'post_tag',
			'post_type',
			'posts',
			'posts_per_archive_page',
			'posts_per_page',
			'preview',
			'robots',
			's',
			'search',
			'second',
			'sentence',
			'showposts',
			'static',
			'status',
			'subpost',
			'subpost_id',
			'tag',
			'tag__and',
			'tag__in',
			'tag__not_in',
			'tag_id',
			'tag_slug__and',
			'tag_slug__in',
			'taxonomy',
			'tb',
			'term',
			'terms',
			'theme',
			'title',
			'type',
			'types',
			'w',
			'withcomments',
			'withoutcomments',
			'year',
		];
	}
}
