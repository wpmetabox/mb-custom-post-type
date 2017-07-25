<?php
/**
 * More products section.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

?>
<div id="products" class="gt-tab-pane products">
	<h3>
		<?php
		echo esc_html( sprintf(
			// Translators: %s is the plugin name.
			__( 'If you like %s, you might also like', 'mb-custom-post-type' ),
			$this->plugin['Name']
		) );
		?>
	</h3>
	<div class="feature-section three-col">
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
