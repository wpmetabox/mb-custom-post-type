<?php
/**
 * Meta Box section.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

?>
<div id="meta-box" class="gt-tab-pane meta-box">
	<div class="feature-section two-col">
		<div class="col">
			<h3><?php esc_html_e( 'What is Meta Box?', 'mb-custom-post-type' ); ?></h3>
			<p>
				<?php
				echo wp_kses_post( sprintf(
					// Translators: %s - link to Meta Box website.
					__( '<a href="%s" target="_blank">Meta Box</a> is a lightweight and feature-rich WordPress plugin that helps developers to save time building custom meta boxes and custom fields in WordPress.', 'mb-custom-post-type' ),
					'https://metabox.io/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page'
				) );
				?>
			</p>
			<p><?php esc_html_e( 'It is simple, easy to use, powerful and developer friendly.', 'mb-custom-post-type' ); ?></p>
			<p>
				<?php
				echo wp_kses_post( sprintf(
					// Translators: %1$s - link to plugin website, %2$s - plugin name.
					__( '<strong>Meta Box</strong> is the foundation for <a href="%1$s" target="_blank">%2$s</a> plugin.', 'mb-custom-post-type' ),
					'https://metabox.io/plugins/custom-post-type/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page',
					$this->plugin['Name']
				) );
				?>
			</p>
			<p>&nbsp;</p>
			<p><a target="_blank" class="button button-primary" href="https://metabox.io/plugins/custom-post-type/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><?php esc_html_e( 'Visit MetaBox.IO', 'mb-custom-post-type' ); ?></a>
		</div>
		<div class="col">
			<h3><?php esc_html_e( 'Extensions', 'mb-custom-post-type' ); ?></h3>
			<p><?php esc_html_e( 'Wanna more features for your custom post types and custom fields that transform your WordPress website into a powerful CMS? Check out some extensions below:', 'mb-custom-post-type' ); ?></p>
			<ul>
				<li><a target="_blank" href="https://metabox.io/plugins/meta-box-group/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><span class="dashicons dashicons-welcome-widgets-menus"></span> <?php esc_html_e( 'Meta Box Group', 'mb-custom-post-type' ); ?></a></li>
				<li><a target="_blank" href="https://metabox.io/plugins/meta-box-conditional-logic/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Meta Box Conditional Logic', 'mb-custom-post-type' ); ?></a></li>
				<li><a target="_blank" href="https://metabox.io/plugins/mb-settings-page/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'MB Settings Page', 'mb-custom-post-type' ); ?></a></li>
				<li><a target="_blank" href="https://metabox.io/plugins/mb-term-meta/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><span class="dashicons dashicons-image-filter"></span> <?php esc_html_e( 'MB Term Meta', 'mb-custom-post-type' ); ?></a></li>
			</ul>
			<p><a target="_blank" class="button button-primary" href="https://metabox.io/plugins/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><?php esc_html_e( 'More Extensions', 'mb-custom-post-type' ); ?></a>
		</div>
	</div>
</div>
