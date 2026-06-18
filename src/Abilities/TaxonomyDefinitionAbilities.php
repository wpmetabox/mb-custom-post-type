<?php
namespace MBCPT\Abilities;

class TaxonomyDefinitionAbilities extends DefinitionAbilities {

	protected function post_type(): string {
		return 'mb-taxonomy';
	}

	protected function ability_slug(): string {
		return 'taxonomy';
	}

	protected function ability_slug_plural(): string {
		return 'taxonomies';
	}

	protected function settings_description(): string {
		return __( 'The %s settings object (slug, labels, types, etc.).', 'mb-custom-post-type' );
	}

	protected function label_singular(): string {
		return __( 'taxonomy', 'mb-custom-post-type' );
	}

	protected function label_plural(): string {
		return __( 'taxonomies', 'mb-custom-post-type' );
	}
}
