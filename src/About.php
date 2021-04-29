<?php
namespace MBCPT;

class About {
	public function __construct() {
		add_action( 'rwmb_about_tabs', array( $this, 'add_tabs' ) );
		add_action( 'rwmb_about_tabs_content', array( $this, 'add_tabs_content' ) );
	}

	public function add_tabs() {
		?>
		<a href="#types-taxonomies" class="nav-tab"><?php esc_html_e( 'Post Types & Taxonomies', 'mb-custom-post-type' ); ?></a>
		<?php
	}

	public function add_tabs_content() {
		?>
		<div id="types-taxonomies" class="gt-tab-pane">
			<div class="two">
				<div class="col">
					<h3><?php esc_html_e( 'Create Custom Post Types', 'mb-custom-post-type' ); ?></h3>
					<p><?php esc_html_e( 'Create your first custom post type to add more custom content (which is not post or page) to your WordPress website.', 'mb-custom-post-type' ); ?><p>
					<p style="display: flex; justify-content: space-between; align-items: center">
						<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-post-type' ) ); ?>" class="button"><?php esc_html_e( 'Start Now', 'mb-custom-post-type' ); ?></a>
						<small><a href="https://docs.metabox.io/creating-post-types/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin"><?php esc_html_e( 'View documentation', 'mb-custom-post-type' ); ?></a></small>
					</p>
					<h3><?php esc_html_e( 'Create Custom Taxonomies', 'mb-custom-post-type' ); ?></h3>
					<p><?php esc_html_e( 'Create your first custom taxonomy to organize your content into groups that you can query to show them in the frontend.', 'mb-custom-post-type' ); ?><p>
					<p style="display: flex; justify-content: space-between; align-items: center">
					<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=mb-taxonomy' ) ); ?>" class="button"><?php esc_html_e( 'Start Now', 'mb-custom-post-type' ); ?></a>
						<small><a href="https://docs.metabox.io/creating-taxonomies/?utm_source=WordPress&utm_medium=link&utm_campaign=plugin"><?php esc_html_e( 'View documentation', 'mb-custom-post-type' ); ?></a></small>
					</p>
				</div>

				<div class="col">
					<div class="youtube-video-container">
						<iframe width="560" height="315" src="https://www.youtube.com/embed/-oYrHGOri4w" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
					</div>
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
		<?php
	}
}
