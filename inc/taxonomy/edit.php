<?php
class MB_CPT_Taxonomy_Edit extends MB_CPT_Base_Edit {
	public function js_vars() {
		$options    = [];
		$post_types = get_post_types( '', 'objects' );
		unset( $post_types['mb-taxonomy'], $post_types['revision'], $post_types['nav_menu_item'] );
		foreach ( $post_types as $post_type => $post_type_object ) {
			$options[ $post_type ] = $post_type_object->labels->singular_name;
		}

		$vars = parent::js_vars();
		$vars['postTypeOptions'] = $options;
	}

	public function register_meta_boxes( $meta_boxes ) {
		$meta_boxes[] = [
			'title'      => __( 'Taxonomy Editor', 'mb-custom-post-type' ),
			'id'         => 'ctg',
			'post_types' => [ 'mb-taxonomy' ],
			'style'      => 'seamless',
			'fields'     => [
				[
					'type' => 'custom_html',
					'std'  => '<div id="root" class="mb-cpt"></div>',
				],
				[
					'id'   => 'post_title',
					'type' => 'hidden',
				],
				[
					'id'   => 'content',
					'type' => 'hidden',
				],
			],
		];

		return $meta_boxes;
	}

	/**
	 * Display upgrade message.
	 *
	 * @return string
	 */
	public function upgrade_message() {
		$output  = '<ul>';
		$output .= '<li>' . __( 'Create custom fields with drag-n-drop interface - no coding knowledge required!', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Add custom fields to taxonomies or user profile.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Create custom settings pages.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'Create frontend submission forms.', 'mb-custom-post-type' ) . '</li>';
		$output .= '<li>' . __( 'And much more!', 'mb-custom-post-type' ) . '</li>';
		$output .= '</ul>';
		$output .= '<a href="https://metabox.io/pricing/?utm_source=plugin_cpt&utm_medium=btn_upgrade&utm_campaign=cpt_upgrade" class="button button-primary">' . esc_html__( 'Get Meta Box Premium now', 'mb-custom-post-type' ) . '</a>';

		return $output;
	}
}