<?php
namespace MBCPT;

use MetaBox\Support\Data;
use MetaBox\Support\Arr;

class EditSettingRegister {

	public function __construct() {
		add_action( 'init', [ $this, 'edit_post_type_register' ] );
		add_action( 'init', [ $this, 'edit_taxonomies_register' ] );
		add_action( 'pre_get_posts', [ $this, 'sort_post_type_query' ] );
		add_action( 'pre_get_posts', [ $this, 'sort_taxonomies_query' ] );
		add_filter( 'post_row_actions', [ $this, 'remove_trash_post_type' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'remove_trash_taxonomies' ], 10, 2 );
		add_filter( 'post_class', [ $this, 'add_class_post_type' ] );
		add_filter( 'post_class', [ $this, 'add_class_taxonomies' ] );
		add_action( 'admin_head', [ $this, 'add_css_post_type' ] );
	}

	public function edit_post_type_register() {
		$unsupported = [ 'attachment' ];
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
			$value              = get_object_vars( $post_type );
			$value['slug']      = $slug;
			$value['icon_type'] = 'dashicons';
			$value['icon']      = Arr::get( $value, 'menu_icon' );
			$labels             = get_object_vars( Arr::get( $value, 'labels' ) );
			$value['labels']    = array_filter( $labels );
			$value['query_var'] = Arr::get( $value, 'query_var' ) ? true : false;
			$value['rewrite']   = Arr::get( $value, 'rewrite' ) ?: [ 'with_front' => false ];
			unset( $value['name'] );
			unset( $value['_edit_link'] );
			unset( $value['rest_namespace'] );
			unset( $value['rest_controller'] );
			unset( $value['rest_controller_class'] );
			unset( $value['cap'] );
			$value['supports'] = [];
			$supports          = [ 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats' ];
			foreach ( $supports as $support ) {
				if ( post_type_supports( $slug, $support ) ) {
					$value['supports'][] = $support;
				}
			}
			if ( $slug == 'post' ) {
				$value['taxonomies'] = [ 'category', 'post_tag' ];
			}
			$content = wp_json_encode( $value, JSON_UNESCAPED_UNICODE );
			wp_insert_post( [
				'post_content' => $content,
				'post_type'    => 'mb-post-type',
				'post_title'   => Arr::get( $labels, 'singular_name' ),
				'post_status'  => 'publish',
				'post_name'    => $slug,
				'menu_order'   => 1,
			] );
		}

	}

	public function edit_taxonomies_register() {
		$taxonomies = Data::get_taxonomies();
		if ( empty( $taxonomies ) ) {
			return;
		}
		foreach ( $taxonomies as $slug => $taxonomy ) {
			$post_obj = get_page_by_path( $slug, OBJECT, 'mb-taxonomy' );
			if ( $post_obj ) {
				continue;
			}
			$value              = get_object_vars( $taxonomy );
			$value['slug']      = $slug;
			$labels             = get_object_vars( Arr::get( $value, 'labels' ) );
			$value['labels']    = array_filter( $labels );
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
				'menu_order'   => 1,
			] );
		}

	}


	public function sort_post_type_query( $query ) {
		global $pagenow;
		if ( 'edit.php' == $pagenow && ! empty( $_GET['post_type'] ) && 'mb-post-type' == $_GET['post_type'] ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}

	public function sort_taxonomies_query( $query ) {
		global $pagenow;
		if ( 'edit.php' == $pagenow && ! empty( $_GET['post_type'] ) && 'mb-taxonomy' == $_GET['post_type'] ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
	}

	public function remove_trash_post_type( $actions, $post ) {
		if ( $post->post_type == 'mb-post-type' && $post->menu_order == 1 ) {
			unset( $actions['trash'] );
		}
		return $actions;
	}

	public function remove_trash_taxonomies( $actions, $post ) {
		if ( $post->post_type == 'mb-taxonomy' && $post->menu_order == 1 ) {
			unset( $actions['trash'] );
		}
		return $actions;
	}

	public function add_class_post_type( $classes ) {
		global $post;
		if ( $post->post_type == 'mb-post-type' && $post->menu_order == 1 ) {
			$classes[] = 'mbcpt-register-outside';
		}
		return $classes;
	}

	public function add_class_taxonomies( $classes ) {
		global $post;
		if ( $post->post_type == 'mb-taxonomy' && $post->menu_order == 1 ) {
			$classes[] = 'mbcpt-register-outside';
		}
		return $classes;
	}

	public function add_css_post_type() {
		?>
		<style type="text/css">
			.mbcpt-register-outside{
				opacity: 0.6;
			}
		</style>
		<?php

	}


}
