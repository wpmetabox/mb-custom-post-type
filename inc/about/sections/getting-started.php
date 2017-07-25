<?php
/**
 * Getting started section.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

?>
<div id="getting-started" class="gt-tab-pane gt-is-active">
	<div class="feature-section two-col">
		<div class="col">
			<h3><?php esc_html_e( 'Create Custom Post Type', 'mb-custom-post-type' ); ?></h3>
			<p><?php esc_html_e( 'Create your first custom post type to add more custom content (which is not post or page) to your WordPress website.', 'mb-custom-post-type' ); ?><p>
			<p><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-post-type' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Start Now', 'mb-custom-post-type' ); ?></a></p>
			<h3><?php esc_html_e( 'Create Custom Taxonomy', 'mb-custom-post-type' ); ?></h3>
			<p><?php esc_html_e( 'Create your first custom taxonomy to organize your content into groups that you can query to show them in the frontend.', 'mb-custom-post-type' ); ?><p>
			<p><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-taxonomy' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Start Now', 'mb-custom-post-type' ); ?></a></p>
			<p>
				<?php
				echo wp_kses_post( sprintf(
					// Translators: %s is the link to the documentation.
					__( 'Confused when to use custom taxonomy vs. custom fields? <a href="%s" target="_blank">Read here</a>.', 'mb-custom-post-type' ),
					'https://metabox.io/custom-fields-vs-custom-taxonomies/?utm_source=plugin_about_page&utm_medium=blog_link&utm_campaign=mb_custom_post_type_link'
				) );
				?>
			</p>
		</div>

		<div class="col">
			<iframe width="493" height="277" src="https://www.youtube.com/embed/KG_8MF9xw6E?rel=0" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>
	<hr>
	<h2><?php esc_html_e( 'About Meta Box', 'mb-custom-post-type' ); ?></h2>
	<div class="meta-box feature-section two-col">
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
			<p><a target="_blank" class="button" href="https://metabox.io/plugins/custom-post-type/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><?php esc_html_e( 'Visit MetaBox.IO', 'mb-custom-post-type' ); ?></a>
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
			<p><a target="_blank" class="button" href="https://metabox.io/plugins/?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page"><?php esc_html_e( 'More Extensions', 'mb-custom-post-type' ); ?></a>
		</div>
	</div>
	<hr>
	<h2><?php esc_html_e( 'You might also like', 'mb-custom-post-type' ); ?></h2>
	<div class="products feature-section three-col">
			<div class="col">
				<div class="project">
					<a href="https://gretathemes.com?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page" title="GretaThemes">
						<img class="project__image" src="<?php echo esc_url( MB_CPT_URL . 'inc/about/images/gretathemes.png' ); ?>" alt="gretathemes" width="96" height="96">
					</a>
					<div class="project__body">
						<h3 class="project__title"><a href="https://gretathemes.com?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page" title="GretaThemes">GretaThemes</a></h3>
						<p class="project__description">Modern, clean, responsive <strong>premium WordPress themes</strong> for all your needs. Fast loading, easy to use and optimized for SEO.</p>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="project">
					<a href="https://metabox.io?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page" title="Meta Box">
						<img class="project__image" src="<?php echo esc_url( MB_CPT_URL . 'inc/about/images/meta-box.png' ); ?>" alt="meta box" width="96" height="96">
					</a>
					<div class="project__body">
						<h3 class="project__title"><a href="https://metabox.io?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page" title="Meta Box">Meta Box</a></h3>
						<p class="project__description">The lightweight &amp; feature-rich WordPress plugin that helps developers to save time building <strong>custom meta boxes and custom fields</strong>.</p>
					</div>
				</div>
			</div>
			<div class="col">
				<div class="project">
					<a href="https://prowcplugins.com?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page" title="Professional WooCommerce Plugins">
						<img class="project__image" src="<?php echo esc_url( MB_CPT_URL . 'inc/about/images/prowcplugins.png' ); ?>" alt="professional woocommerce plugins" width="96" height="96">
					</a>
					<div class="project__body">
						<h3 class="project__title"><a href="https://prowcplugins.com?utm_source=plugin_about_page&utm_medium=product_link&utm_campaign=mb_custom_post_type_about_page" title="Professional WooCommerce Plugins">ProWCPlugins</a></h3>
						<p class="project__description">Professional <strong>WordPress plugins for WooCommerce</strong> that help you empower your e-commerce sites and grow your business.</p>
					</div>
				</div>
			</div>
		</div>
</div>
