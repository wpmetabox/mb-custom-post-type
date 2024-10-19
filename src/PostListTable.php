<?php
namespace MBCPT;

class PostListTable {
	public function __construct() {
		add_action( 'admin_head-edit.php', [ $this, 'init' ] );
	}

	public function init() {
		if ( ! in_array( get_current_screen()->id, [ 'edit-mb-post-type', 'edit-mb-taxonomy' ], true ) ) {
			return;
		}

		$this->output_css();
		$this->remove_excerpt();
	}

	private function output_css() {
		?>
		<style>.view-mode{ display: none; }</style>
		<?php
	}

	private function remove_excerpt() {
		add_filter( 'get_the_excerpt', '__return_empty_string' );
	}
}
