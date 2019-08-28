<?php
/**
 * Getting started section.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

?>
<div id="types-taxonomies" class="gt-tab-pane">
	<div class="two">
		<div class="col">
			<h3><?php esc_html_e( 'Create Custom Post Types', 'mb-custom-post-type' ); ?></h3>
			<p><?php esc_html_e( 'Create your first custom post type to add more custom content (which is not post or page) to your WordPress website.', 'mb-custom-post-type' ); ?><p>
			<p><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-post-type' ) ); ?>" class="button"><?php esc_html_e( 'Start Now', 'mb-custom-post-type' ); ?></a></p>
			<h3><?php esc_html_e( 'Create Custom Taxonomies', 'mb-custom-post-type' ); ?></h3>
			<p><?php esc_html_e( 'Create your first custom taxonomy to organize your content into groups that you can query to show them in the frontend.', 'mb-custom-post-type' ); ?><p>
			<p><a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-taxonomy' ) ); ?>" class="button"><?php esc_html_e( 'Start Now', 'mb-custom-post-type' ); ?></a></p>
		</div>

		<div class="col">
			<iframe width="493" height="277" src="https://www.youtube.com/embed/9c4w5zdeTJI?rel=0" frameborder="0" allowfullscreen></iframe>
			<p>
				<?php
				echo wp_kses_post(
					sprintf(
						// Translators: %s is the link to the documentation.
						__( 'Confused when to use custom taxonomy vs. custom fields? <a href="%s" target="_blank">Read this tutorial</a>.', 'mb-custom-post-type' ),
						'https://metabox.io/custom-fields-vs-custom-taxonomies/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin'
					)
				);
				?>
			</p>
		</div>
	</div>
</div>
