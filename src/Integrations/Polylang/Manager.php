<?php
namespace MBCPT\Integrations\Polylang;

class Manager {
	public function __construct() {
		if ( ! defined( 'POLYLANG_VERSION' ) ) {
			return;
		}

		new PostType();
		new Taxonomy();
	}
}