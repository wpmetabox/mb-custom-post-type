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
			<iframe width="560" height="315" src="https://www.youtube.com/embed/KG_8MF9xw6E" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>
</div>
