<?php
namespace MBCPT;

use MetaBox\Support\Data;
use MetaBox\Support\Arr;

class EditSettingRegister {

	public function __construct() {
		add_action( 'init', [ $this, 'edit_post_type_register' ] );
		add_action( 'init', [ $this, 'edit_taxonomies_register' ] );
	}

	public function edit_post_type_register() {
		$unsupported = [ 'post', 'page', 'attachment' ];
		$post_types  = Data::get_post_types();
		$post_types  = array_diff_key( $post_types, array_flip( $unsupported ) );
		if ( empty( $post_types ) ) {
			return;
		}
		foreach ( $post_types as $slug => $post_type ) {
			$post_obj = get_page_by_path( $slug, OBJECT, 'mb-post-type' );
			if ( $post_obj ) {
				continue;
			}
			$value              = json_decode( json_encode( $post_type ), true );
			$value['slug']      = $slug;
			$value['icon_type'] = 'dashicons';
			$value['icon']      = Arr::get( $value, 'menu_icon' );
			$labels             = json_decode( json_encode( Arr::get( $value, 'labels' ) ), true );
			$value['labels']    = $labels;
			$value['query_var'] = Arr::get( $value, 'query_var' ) ? true : false;
			unset( $value['name'] );
			unset( $value['_edit_link'] );
			unset( $value['rest_namespace'] );
			unset( $value['cap'] );
			$value['supports'] = [];
			$supports          = [ 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats' ];
			foreach ( $supports as $support ) {
				if ( post_type_supports( $slug, $support ) ) {
					$value['supports'][] = $support;
				}
			}
			$content = wp_json_encode( $value, JSON_UNESCAPED_UNICODE );
			wp_insert_post( [
				'post_content' => $content,
				'post_type'    => 'mb-post-type',
				'post_title'   => Arr::get( $labels, 'singular_name' ),
				'post_status'  => 'publish',
				'post_name'    => $slug,
			] );
		}

	}

	public function edit_taxonomies_register() {
		$unsupported = [ 'category', 'post_tag' ];
		$taxonomies  = Data::get_taxonomies();
		$taxonomies  = array_diff_key( $taxonomies, array_flip( $unsupported ) );
		if ( empty( $taxonomies ) ) {
			return;
		}
		// var_dump( $taxonomies );
		foreach ( $taxonomies as $slug => $taxonomy ) {
			$post_obj = get_page_by_path( $slug, OBJECT, 'mb-taxonomy' );
			if ( $post_obj ) {
				continue;
			}
			$value              = json_decode( json_encode( $taxonomy ), true );
			$value['slug']      = $slug;
			$labels             = json_decode( json_encode( Arr::get( $value, 'labels' ) ), true );
			$value['labels']    = $labels;
			$value['types']     = Arr::get( $value, 'object_type' );
			$value['query_var'] = Arr::get( $value, 'query_var' ) ? true : false;
			unset( $value['name'] );
			unset( $value['rest_namespace'] );
			unset( $value['meta_box_sanitize_cb'] );
			unset( $value['cap'] );
			unset( $value['object_type'] );
			$content = wp_json_encode( $value, JSON_UNESCAPED_UNICODE );
			wp_insert_post( [
				'post_content' => $content,
				'post_type'    => 'mb-taxonomy',
				'post_title'   => Arr::get( $labels, 'singular_name' ),
				'post_status'  => 'publish',
				'post_name'    => $slug,
			] );
		}

	}


}
