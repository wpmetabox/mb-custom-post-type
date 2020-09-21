const { __ } = wp.i18n;

export const BasicDatas = [
	{ type: 'text', name: 'name', label: __( 'Plural name', 'mb-custom-post-type' ), required: true },
	{ type: 'text', name: 'singular_name', label: __( 'Singular name', 'mb-custom-post-type' ), required: true },
	{ type: 'text', name: 'slug', label: __( 'Slug', 'mb-custom-post-type' ), required: true, updateFrom: 'singular_name' },
];

export const CodeDatas = [
	{ type: 'text', name: 'function_name', label: __( 'Function name', 'mb-custom-post-type' ) },
	{ type: 'text', name: 'text_domain', label: __( 'Text domain', 'mb-custom-post-type' ) },
];

export const LabelDatas = [
	{ type: 'text', name: 'menu_name', label: __( 'Menu name', 'mb-custom-post-type' ), defaultValue: '%name%', updateFrom: 'name' },
	{ type: 'text', name: 'all_items', label: __( 'All items', 'mb-custom-post-type' ), defaultValue: 'All %name%', updateFrom: 'name' },
	{ type: 'text', name: 'edit_item', label: __( 'Edit item', 'mb-custom-post-type' ), defaultValue: 'Edit %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'view_item', label: __( 'View item', 'mb-custom-post-type' ), defaultValue: 'View %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'update_item', label: __( 'Update item', 'mb-custom-post-type' ), defaultValue: 'Update %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'add_new_item', label: __( 'Add new item', 'mb-custom-post-type' ), defaultValue: 'Add new %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'new_item_name', label: __( 'New item name', 'mb-custom-post-type' ), defaultValue: 'New %singular_name% name', updateFrom: 'singular_name' },
	{ type: 'text', name: 'parent_item', label: __( 'Parent item', 'mb-custom-post-type' ), defaultValue: 'Parent %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'parent_item_colon', label: __( 'Parent item Colon', 'mb-custom-post-type' ), defaultValue: 'Parent %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'search_items', label: __( 'Search items', 'mb-custom-post-type' ), defaultValue: 'Search %name%', updateFrom: 'name' },
	{ type: 'text', name: 'popular_items', label: __( 'Popular items', 'mb-custom-post-type' ), defaultValue: 'Popular %name%', updateFrom: 'name' },
	{ type: 'text', name: 'separate_items_with_commas', label: __( 'Separate items with commas', 'mb-custom-post-type' ), defaultValue: 'Separate %name% with commas', updateFrom: 'name' },
	{ type: 'text', name: 'add_or_remove_items', label: __( 'Add or remove items', 'mb-custom-post-type' ), defaultValue: 'Add or remove %name%', updateFrom: 'name' },
	{ type: 'text', name: 'choose_from_most_used', label: __( 'Choose from most used', 'mb-custom-post-type' ), defaultValue: 'Choose most used %name%', updateFrom: 'name' },
	{ type: 'text', name: 'not_found', label: __( 'Not found', 'mb-custom-post-type' ), defaultValue: 'No %name% found', updateFrom: 'name' },
	{ type: 'text', name: 'back_to_items', label: __( 'Back to items', 'mb-custom-post-type' ), defaultValue: 'Back to %name%', updateFrom: 'name' },
];

const i18n = MbTaxonomy;
let PostTypeDatas = [];
const supportPostTypes = i18n.settings ? [...JSON.parse(i18n.settings).post_types] : [];
let postTypes = i18n.postTypeOptions ? i18n.postTypeOptions : [];
Object.keys( postTypes ).forEach( e => {
	PostTypeDatas.push( { name: e, description: postTypes[e], checked: supportPostTypes.includes( e ) ? true : false } )
} );
export { PostTypeDatas };

export const AdvancedDatas = [
	{ type: 'checkbox', name: 'public', label: __( 'Public?', 'mb-custom-post-type' ), description: __( 'If the taxonomy should be publicly queryable.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_ui', label: __( 'Show UI?', 'mb-custom-post-type' ), description: __( 'Whether to generate a default UI for managing this taxonomy.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_menu', label: __( 'Show in menu?', 'mb-custom-post-type' ), description: __( 'Where to show the taxonomy in the admin menu. show_ui must be true.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_nav_menus', label: __( 'Show in nav menus?', 'mb-custom-post-type' ), description: __( 'Whether taxonomy is available for selection in navigation menus.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_in_quick_edit', label: __( 'Show on edit page?', 'mb-custom-post-type' ), description: __( 'Whether to show the taxonomy on the edit page.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_tag_cloud', label: __( 'Show tag cloud?', 'mb-custom-post-type' ), description: __( 'Whether to allow the Tag Cloud widget to use this taxonomy.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'show_admin_column', label: __( 'Show admin column?', 'mb-custom-post-type' ), description: __( 'Whether to allow automatic creation of taxonomy columns on associated post-types table.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'show_in_rest', label: __( 'Show in REST?', 'mb-custom-post-type' ), description: __( 'Whether to include the taxonomy in the REST API.', 'mb-custom-post-type' ), checked: true },
	{ type: 'text', name: 'rest_base', label: __( 'REST API base slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ), placeholder: __( 'Slug to use in REST API URL', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'hierarchical', label: __( 'Hierarchical?', 'mb-custom-post-type' ), description: __( 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'query_var', label: __( 'Query var', 'mb-custom-post-type' ), description: __( 'Uncheck to disable the query var, check to use the taxonomy\'s "name" as query var.', 'mb-custom-post-type' ), checked: true },
	{ type: 'checkbox', name: 'sort', label: __( 'Sort?', 'mb-custom-post-type' ), description: __( 'Whether this taxonomy should remember the order in which terms are added to objects.', 'mb-custom-post-type' ), checked: false },
	{ type: 'text', name: 'rewrite_slug', label: __( 'Custom rewrite slug', 'mb-custom-post-type' ), description: __( 'Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ) },
	{ type: 'checkbox', name: 'rewrite_no_front', label: __( 'No prepended permalink structure?', 'mb-custom-post-type' ), description: __( 'Do not prepend the permalink structure with the front base.', 'mb-custom-post-type' ), checked: false },
	{ type: 'checkbox', name: 'rewrite_hierarchical', label: __( 'Hierarchical URL', 'mb-custom-post-type' ), checked: false },
];