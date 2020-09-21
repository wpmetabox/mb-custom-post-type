export const BasicDatas = [
	{ type: 'text', name: 'name', label: 'Plural name', required: true },
	{ type: 'text', name: 'singular_name', label: 'Singular name', required: true },
	{ type: 'text', name: 'slug', label: 'Slug', required: true, updateFrom: 'singular_name' },
];

export const CodeDatas = [
	{ type: 'text', name: 'function_name', label: 'Function name' },
	{ type: 'text', name: 'text_domain', label: 'Text domain' },
];

export const LabelDatas = [
	{ type: 'text', name: 'menu_name', label: 'Menu name', defaultValue: '%name%', updateFrom: 'name' },
	{ type: 'text', name: 'all_items', label: 'All items', defaultValue: 'All %name%', updateFrom: 'name' },
	{ type: 'text', name: 'edit_item', label: 'Edit item', defaultValue: 'Edit %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'view_item', label: 'View item', defaultValue: 'View %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'update_item', label: 'Update item', defaultValue: 'Update %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'add_new_item', label: 'Add new item', defaultValue: 'Add new %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'new_item_name', label: 'New item name', defaultValue: 'New %singular_name% name', updateFrom: 'singular_name' },
	{ type: 'text', name: 'parent_item', label: 'Parent item', defaultValue: 'Parent %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'parent_item_colon', label: 'Parent item Colon', defaultValue: 'Parent %singular_name%', updateFrom: 'singular_name' },
	{ type: 'text', name: 'search_items', label: 'Search items', defaultValue: 'Search %name%', updateFrom: 'name' },
	{ type: 'text', name: 'popular_items', label: 'Popular items', defaultValue: 'Popular %name%', updateFrom: 'name' },
	{ type: 'text', name: 'separate_items_with_commas', label: 'Separate items with commas', defaultValue: 'Separate %name% with commas', updateFrom: 'name' },
	{ type: 'text', name: 'add_or_remove_items', label: 'Add or remove items', defaultValue: 'Add or remove %name%', updateFrom: 'name' },
	{ type: 'text', name: 'choose_from_most_used', label: 'Choose from most used', defaultValue: 'Choose most used %name%', updateFrom: 'name' },
	{ type: 'text', name: 'not_found', label: 'Not found', defaultValue: 'No %name% found', updateFrom: 'name' },
	{ type: 'text', name: 'back_to_items', label: 'Back to items', defaultValue: 'Back to %name%', updateFrom: 'name' },
];

const defaultPostTypes = [
	{ name: 'post', description: 'Post', checked: true },
	{ name: 'page', description: 'Page', checked: false },
];
const i18n = MbTaxonomy;
let postTypeOptions = i18n.postTypeOptions;
let temp = [];
let supportPostTypes = postTypeOptions ? postTypeOptions : defaultPostTypes;
Object.keys( postTypeOptions ).forEach( e => {
	temp.push( { name: e, description: i18n.postTypeOptions[e], checked: supportPostTypes.hasOwnProperty( e ) ? true : false } )
} );

export const PostTypeDatas = defaultPostTypes;

export const AdvancedDatas = [
	{ type: 'checkbox', name: 'public', label: 'Public?', description: 'If the taxonomy should be publicly queryable.', checked: true },
	{ type: 'checkbox', name: 'show_ui', label: ' Show UI? ', description: 'Whether to generate a default UI for managing this taxonomy.', checked: true },
	{ type: 'checkbox', name: 'show_in_menu', label: 'Show in menu?', description: 'Where to show the taxonomy in the admin menu. show_ui must be true.', checked: true },
	{ type: 'checkbox', name: 'show_in_nav_menus', label: 'Show in nav menus?', description: 'Whether taxonomy is available for selection in navigation menus.', checked: true },
	{ type: 'checkbox', name: 'show_in_quick_edit', label: 'Show on edit page?', description: 'Whether to show the taxonomy on the edit page.', checked: true },
	{ type: 'checkbox', name: 'show_tag_cloud', label: 'Show tag cloud?', description: 'Whether to allow the Tag Cloud widget to use this taxonomy.', checked: true },
	{ type: 'checkbox', name: 'show_admin_column', label: 'Show admin column?', description: 'Whether to allow automatic creation of taxonomy columns on associated post-types table.', checked: false },
	{ type: 'checkbox', name: 'show_in_rest', label: 'Show in REST?', description: 'Whether to include the taxonomy in the REST API.', checked: true },
	{ type: 'text', name: 'rest_base', label: 'REST API base slug', description: 'Leave empty to use the taxonomy slug.', placeholder: 'Slug to use in REST API URL' },
	{ type: 'checkbox', name: 'hierarchical', label: 'Hierarchical?', description: 'Is this taxonomy hierarchical (have descendants) like categories or not hierarchical like tags.', checked: false },
	{ type: 'checkbox', name: 'query_var', label: 'Query var', description: 'Uncheck to disable the query var, check to use the taxonomy\'s "name" as query var.', checked: true },
	{ type: 'checkbox', name: 'sort', label: 'Sort?', description: 'Whether this taxonomy should remember the order in which terms are added to objects.', checked: false },
	{ type: 'text', name: 'rewrite_slug', label: 'Custom rewrite slug', description: 'Leave empty to use the taxonomy slug.' },
	{ type: 'checkbox', name: 'rewrite_no_front', label: 'No prepended permalink structure?', description: 'Do not prepend the permalink structure with the front base.', checked: false },
	{ type: 'checkbox', name: 'rewrite_hierarchical', label: 'Hierarchical URL', checked: false },
];