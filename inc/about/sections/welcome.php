<?php
/**
 * Welcome section.
 *
 * @package    Meta Box
 * @subpackage MB Custom Post Type
 */

?>
<h1>
	<?php
	// Translators: %1$s - Plugin name, %2$s - Plugin version.
	echo esc_html( sprintf( __( 'Welcome to %1$s %2$s', 'mb-custom-post-type' ), $this->plugin['Name'], $this->plugin['Version'] ) );
	?>
</h1>
<div class="about-text"><?php esc_html_e( 'This plugin helps you to create custom post types and taxonomies in WordPress quickly with an easy-to-use interface. Follow the instruction below to get started.', 'mb-custom-post-type' ); ?></div>
<a target="_blank" href="<?php echo esc_url( 'https://metabox.io/?utm_source=plugin_about_page&utm_medium=badge_link&utm_campaign=mb_custom_post_type_about_page' ); ?>" class="wp-badge"><?php esc_html_e( 'Meta Box', 'mb-custom-post-type' ); ?></a>
