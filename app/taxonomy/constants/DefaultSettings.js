const BasicSettings = {
	'name'         : '',
	'singular_name': '',
	'args_taxonomy': '',
	'function_name': 'your_prefix_register_taxonomy',
	'text_domain'  : 'your-textdomain',
}

const LabelSettings = {
	'menu_name'                 : '',
	'all_items'                 : '',
	'edit_item'                 : '',
	'view_item'                 : '',
	'update_item'               : '',
	'add_new_item'              : '',
	'new_item'                  : '',
	'parent_item'               : '',
	'parent_item_colon'         : '',
	'search_items'              : '',
	'popular_items'             : '',
	'separate_items_with_commas': '',
	'add_or_remove_items'       : '',
	'choose_from_most_used'     : '',
	'not_found'                 : '',
}

export const PostTypeSettings = {
	'post': true,
	'page': true,
}

export const AdvancedSettings = {
	'public'              : true,
	'show_ui'             : true,
	'show_in_menu'        : true,
	'show_in_nav_menus'   : true,
	'show_tagcloud'       : true,
	'show_in_quick_edit'  : true,
	'show_admin_column'   : false,
	'show_in_rest'        : true,
	'hierarchical'        : false,
	'query_var'           : true,
	'sort'                : false,
	'rewrite_no_front'    : false,
	'rewrite_hierarchical': false,
	'rewrite'             : true,
}

const DefaultSettings = {
	...BasicSettings,
	...LabelSettings,
	...PostTypeSettings,
	...AdvancedSettings
};

export default DefaultSettings;