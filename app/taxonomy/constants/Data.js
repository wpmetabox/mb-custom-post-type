import { __ } from '@wordpress/i18n';

export const BasicControls = [
	{
		type: 'text',
		name: 'labels.name',
		label: __( 'Plural name', 'mb-custom-post-type' ),
		required: true,
		tooltip: __( 'General name for the taxonomy, usually plural', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.singular_name',
		label: __( 'Singular name', 'mb-custom-post-type' ),
		required: true,
		tooltip: __( 'Name for one object of this taxonomy', 'mb-custom-post-type' ),
	},
	{
		type: 'slug',
		name: 'slug',
		label: __( 'Slug', 'mb-custom-post-type' ),
		required: true,
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Taxonomy key, must not exceed 32 characters and may only contain lowercase alphanumeric characters, dashes, and underscores', 'mb-custom-post-type' ),
		limit: 32,
	},
];

export const CodeControls = [
	{
		type: 'text',
		name: 'function_name',
		label: __( 'Function name', 'mb-custom-post-type' ),
		tooltip: __( 'Your function name that registers the taxonomy', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'text_domain',
		label: __( 'Text domain', 'mb-custom-post-type' ),
		tooltip: __( 'Required for multilingual website. Used in the exported code only.', 'mb-custom-post-type' ),
	},
];

export const LabelControls = [
	// name
	// singular_name
	{
		type: 'text',
		name: 'labels.search_items',
		label: __( 'Search items', 'mb-custom-post-type' ),
		default: __( 'Search %name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label for searching items', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.popular_items',
		label: __( 'Popular items', 'mb-custom-post-type' ),
		default: __( 'Popular %name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label for most popular items, only used for non-hierarchical taxonomies', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.all_items',
		label: __( 'All items', 'mb-custom-post-type' ),
		default: __( 'All %name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label to signify all items in a submenu link', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.parent_item',
		label: __( 'Parent item', 'mb-custom-post-type' ),
		default: __( 'Parent %singular_name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Label for parent item, only used for hierarchical taxonomies', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.parent_item_colon',
		label: __( 'Parent item colon', 'mb-custom-post-type' ),
		default: __( 'Parent %singular_name%:', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'The same as parent item, but with colon : in the end', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.edit_item',
		label: __( 'Edit item', 'mb-custom-post-type' ),
		default: __( 'Edit %singular_name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Label for adding a new singular item', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.view_item',
		label: __( 'View item', 'mb-custom-post-type' ),
		default: __( 'View %singular_name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Label for viewing a singular item', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.update_item',
		label: __( 'Update item', 'mb-custom-post-type' ),
		default: __( 'Update %singular_name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Label for updating a singular item', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.add_new_item',
		label: __( 'Add new item', 'mb-custom-post-type' ),
		default: __( 'Add New %singular_name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Label for adding a new singular item', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.new_item_name',
		label: __( 'New item name', 'mb-custom-post-type' ),
		default: __( 'New %singular_name% Name', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'Label for new item name', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.separate_items_with_commas',
		label: __( 'Separate items with commas', 'mb-custom-post-type' ),
		default: __( 'Separate %name_lowercase% with commas', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'This label is only used for non-hierarchical taxonomies. Default "Separate tags with commas", used in the meta box', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.add_or_remove_items',
		label: __( 'Add or remove items', 'mb-custom-post-type' ),
		default: __( 'Add or remove %name_lowercase%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'This label is only used for non-hierarchical taxonomies. Default "Add or remove tags", used in the meta box when JavaScript is disabled', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.choose_from_most_used',
		label: __( 'Choose from most used', 'mb-custom-post-type' ),
		default: __( 'Choose most used %name_lowercase%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'This label is only used on non-hierarchical taxonomies. Default "Choose from the most used tags", used in the meta box', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.not_found',
		label: __( 'Not found', 'mb-custom-post-type' ),
		default: __( 'No %name_lowercase% found.', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label used in the meta box and taxonomy list table', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.no_terms',
		label: __( 'No terms', 'mb-custom-post-type' ),
		default: __( 'No %name_lowercase%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label used in the posts and media list tables', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.filter_by_item',
		label: __( 'Filter by', 'mb-custom-post-type' ),
		default: __( 'Filter by %singular_name_lowercase%', 'mb-custom-post-type' ),
		updateFrom: 'labels.singular_name',
		tooltip: __( 'This label is only used for hierarchical taxonomies, used in the posts list table', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.items_list_navigation',
		label: __( 'Table pagination hidden heading', 'mb-custom-post-type' ),
		default: __( '%name% list pagination', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label for the table pagination hidden heading', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.items_list',
		label: __( 'Table hidden heading', 'mb-custom-post-type' ),
		default: __( '%name% list', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label for the table hidden heading', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.most_used',
		label: __( 'Most used tab', 'mb-custom-post-type' ),
		default: __( 'Most Used', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Title for the Most Used tab', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.back_to_items',
		label: __( 'Back to items', 'mb-custom-post-type' ),
		default: __( '&larr; Go to %name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label displayed after a term has been updated', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'labels.menu_name',
		label: __( 'Menu name', 'mb-custom-post-type' ),
		default: __( '%name%', 'mb-custom-post-type' ),
		updateFrom: 'labels.name',
		tooltip: __( 'Label for the tab in the admin menu', 'mb-custom-post-type' ),
	},
];

export const AdvancedControls = [
	{
		type: 'textarea',
		name: 'description',
		label: __( 'Description', 'mb-custom-post-type' ),
		description: __( 'A short descriptive summary of what the taxonomy is for.', 'mb-custom-post-type' ),
		tooltip: __( 'A short descriptive summary of what the taxonomy is for', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'public',
		label: __( 'Public', 'mb-custom-post-type' ),
		description: __( 'Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'publicly_queryable',
		label: __( 'Public queryable', 'mb-custom-post-type' ),
		description: __( 'Whether the taxonomy is publicly queryable.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether the taxonomy is publicly queryable', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'hierarchical',
		label: __( 'Hierarchical', 'mb-custom-post-type' ),
		description: __( 'Whether the taxonomy is hierarchical.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether the taxonomy is hierarchical', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_ui',
		label: __( 'Show UI', 'mb-custom-post-type' ),
		description: __( 'Whether to generate and allow a UI for managing terms in this taxonomy in the admin.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to generate and allow a UI for managing terms in this taxonomy in the admin', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_in_menu',
		label: __( 'Show in menu', 'mb-custom-post-type' ),
		description: __( 'Whether to show the taxonomy in the admin menu.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to show the taxonomy in the admin menu. If true, the taxonomy is shown as a submenu of the object type menu. If false, no menu is shown. The show UI settings must be true', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_in_nav_menus',
		label: __( 'Show in nav menus', 'mb-custom-post-type' ),
		description: __( 'Makes this taxonomy available for selection in navigation menus.', 'mb-custom-post-type' ),
		tooltip: __( 'Makes this taxonomy available for selection in navigation menus', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'meta_box_cb',
		label: __( 'Show on edit page', 'mb-custom-post-type' ),
		description: __( 'Whether to show the taxonomy on the edit page.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to show the taxonomy meta box on the edit page', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_in_rest',
		label: __( 'Show in REST', 'mb-custom-post-type' ),
		description: __( 'Whether to include the taxonomy in the REST API. Must be true to enable the Gutenberg editor.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to include the taxonomy in the REST API. Set this to true for the taxonomy to be available in the block editor', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'rest_base',
		label: __( 'REST API base slug', 'mb-custom-post-type' ),
		description: __( 'The base URL of the REST API route. Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ),
		tooltip: __( 'To change the base URL of REST API route', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_tagcloud',
		label: __( 'Show tag cloud', 'mb-custom-post-type' ),
		description: __( 'Whether to list the taxonomy in the Tag Cloud Widget controls.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to list the taxonomy in the Tag Cloud Widget controls', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_in_quick_edit',
		label: __( 'Show in quick edit', 'mb-custom-post-type' ),
		description: __( 'Whether to show the taxonomy in the quick/bulk edit panel.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to show the taxonomy in the quick/bulk edit panel', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'show_admin_column',
		label: __( 'Show admin column', 'mb-custom-post-type' ),
		description: __( 'Whether to display a column for the taxonomy on its post type listing screens.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether to display a column for the taxonomy on its post type listing screens', 'mb-custom-post-type' ),
	},
	{
		type: 'text',
		name: 'rewrite.slug',
		label: __( 'Custom rewrite slug', 'mb-custom-post-type' ),
		description: __( 'Leave empty to use the taxonomy slug.', 'mb-custom-post-type' ),
		tooltip: __( 'Customize the permastruct slug', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'rewrite.with_front',
		label: __( 'Prepended permalink structure', 'mb-custom-post-type' ),
		description: __( 'Example: if your permalink structure is /blog/, then your links will be: false -> /news/, true -> /blog/news/.', 'mb-custom-post-type' ),
		tooltip: __( 'Should the permastruct be prepended', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'rewrite.hierarchical',
		label: __( 'Hierarchical URL', 'mb-custom-post-type' ),
		description: __( 'Either hierarchical rewrite tag or not', 'mb-custom-post-type' ),
		tooltip: __( 'Either hierarchical rewrite tag or not', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'query_var',
		label: __( 'Query var', 'mb-custom-post-type' ),
		description: __( 'Uncheck to disable the query var, check to use the taxonomy\'s "name" as query var.', 'mb-custom-post-type' ),
		tooltip: __( 'Sets the query var key (taxonomy slug) for this taxonomy', 'mb-custom-post-type' ),
	},
	{
		type: 'checkbox',
		name: 'sort',
		label: __( 'Sort', 'mb-custom-post-type' ),
		description: __( 'Whether this taxonomy should remember the order in which terms are added to objects.', 'mb-custom-post-type' ),
		tooltip: __( 'Whether terms in this taxonomy should be sorted', 'mb-custom-post-type' ),
	},
];

export const PermissionsControls = [
	{
		type: 'text',
		name: 'capabilities.manage_terms',
		placeholder: 'manage_categories',
		label: __( 'Manage terms', 'mb-custom-post-type' ),
		description: __( 'The capability required for managing terms.', 'mb-custom-post-type' ),
		tooltip: __( 'The capability required for managing terms.', 'mb-custom-post-type' ),
		datalist: MBCPT.allCapabilities,
	},
	{
		type: 'text',
		name: 'capabilities.edit_terms',
		placeholder: 'manage_categories',
		label: __( 'Edit terms', 'mb-custom-post-type' ),
		description: __( 'The capability required for editing terms.', 'mb-custom-post-type' ),
		tooltip: __( 'The capability required for editing terms.', 'mb-custom-post-type' ),
		datalist: MBCPT.allCapabilities,
	},
	{
		type: 'text',
		name: 'capabilities.delete_terms',
		placeholder: 'manage_categories',
		label: __( 'Delete terms', 'mb-custom-post-type' ),
		description: __( 'The capability required for deleting terms.', 'mb-custom-post-type' ),
		tooltip: __( 'The capability required for deleting terms.', 'mb-custom-post-type' ),
		datalist: MBCPT.allCapabilities,
	},
	{
		type: 'text',
		name: 'capabilities.assign_terms',
		placeholder: 'edit_posts',
		label: __( 'Assign terms', 'mb-custom-post-type' ),
		description: __( 'The capability required for assigning terms.', 'mb-custom-post-type' ),
		tooltip: __( 'The capability required for assigning terms.', 'mb-custom-post-type' ),
		datalist: MBCPT.allCapabilities,
	},
];