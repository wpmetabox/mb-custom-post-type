<?php
namespace MBCPT\Abilities;

class PostTypeDefinitionAbilities extends DefinitionAbilities {

	protected function post_type(): string {
		return 'mb-post-type';
	}

	protected function ability_slug(): string {
		return 'post-type';
	}

	protected function ability_slug_plural(): string {
		return 'post-types';
	}

	protected function settings_description(): string {
		return __( 'The %s settings object (slug, labels, supports, etc.).', 'mb-custom-post-type' );
	}

	protected function label_singular(): string {
		return __( 'post type', 'mb-custom-post-type' );
	}

	protected function label_plural(): string {
		return __( 'post types', 'mb-custom-post-type' );
	}
}
