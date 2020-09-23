const BasicSettings = {
	'name'         : '',
	'slug'         : '',
	'function_name': 'your_prefix_register_post_type',
	'text_domain'  : 'your-textdomain',
}

const LabelSettings = {
	labels: {
		'singular_name'     : '',
		'menu_name'         : '',
		'name_admin_bar'    : '',
		'add_new'           : '',
		'add_new_item'      : '',
		'new_item'          : '',
		'edit_item'         : '',
		'view_item'         : '',
		'update_item'       : '',
		'all_items'         : '',
		'search_items'      : '',
		'parent_item_colon' : '',
		'not_found'         : '',
		'not_found_in_trash': '',
	}
}

export const SupportSettings = {
	'title'          : true,
	'editor'         : true,
	'author'         : false,
	'thumbnail'      : true,
	'excerpt'        : false,
	'trackbacks'     : false,
	'custom-fields'  : false,
	'comments'       : false,
	'revisions'      : false,
	'page-attributes': false,
}

export const TaxonomySettings = {
	'category': false,
	'tag'     : false,
}

const AdvancedSettings = {
	'description'        : '',
	'public'             : true,
	'exclude_from_search': false,
	'publicly_queryable' : true,
	'show_ui'            : true,
	'show_in_nav_menus'  : true,
	'show_in_admin_bar'  : true,
	'show_in_rest'       : true,
	'capability_type'    : 'post',
	'hierarchical'       : false,
	'has_archive'        : true,
	'query_var'          : true,
	'can_export'         : true,
	'rewrite_no_front'   : false,
}

const DefaultSettings = {
	...BasicSettings,
	...LabelSettings,
	...SupportSettings,
	...TaxonomySettings,
	...AdvancedSettings
};

export default DefaultSettings;