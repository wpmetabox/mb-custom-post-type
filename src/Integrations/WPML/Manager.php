<?php
namespace MBCPT\Integrations\WPML;

class Manager {
	public function __construct() {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return;
		}

		new PostType();
		new Taxonomy();
	}
}
