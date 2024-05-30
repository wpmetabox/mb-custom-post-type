import { __ } from "@wordpress/i18n";
import { Panel, PanelBody, PanelRow } from '@wordpress/components';

const Sidebar = () => {
	return (
		<div className="mb-cpt-sidebar">
			<Panel>
				<PanelBody title={ __( 'Summary', 'mb-custom-post-type' ) } initialOpen={ true }>
					<PanelRow>
						<div className="mb-cpt-box box-summary">
							<p><label>{ __( 'Status', 'mb-custom-post-type' ) }</label> { MBCPT.status }</p>
							<p><label>{ __( 'Published', 'mb-custom-post-type' ) }</label> { MBCPT.published }</p>
							{ MBCPT.modifiedtime && <p><label>{ __( 'Last modified', 'mb-custom-post-type' ) }</label> { MBCPT.modifiedtime }</p> }
							<p><label>{ __( 'Author', 'mb-custom-post-type' ) }</label> { MBCPT.author }</p>
							<p><a href={ MBCPT.trash }><button type="button" className="components-button is-secondary is-destructive">Move to trash</button></a></p>
						</div>
					</PanelRow>
				</PanelBody>
				<PanelBody title={ __( 'Our WordPress Plugins', 'mb-custom-post-type' ) } initialOpen={ true }>
					<PanelRow>
						<div className="mb-cpt-box">
							<p>{ __( 'Like this plugin? Check out our other WordPress plugins:', 'mb-custom-post-type' ) }</p>
							<p><a href="https://wpslimseo.com?utm_source=WordPress&utm_medium=link&utm_campaign=meta-box" target="_blank" rel="noopenner noreferrer">Slim SEO</a> - { __( 'A fast, lightweight and full-featured SEO plugin for WordPress with minimal configuration.', 'mb-custom-post-type' ) }</p>
							<p><a href="https://wpslimseo.com/slim-seo-schema/?utm_source=WordPress&utm_medium=link&utm_campaign=meta-box" target="_blank" rel="noopenner noreferrer">Slim SEO Schema</a> - { __( 'An advanced, powerful and flexible plugin to add schemas to WordPress', 'mb-custom-post-type' ) }</p>
							<p><a href="https://wpslimseo.com/products/slim-seo-link-manager/?utm_source=WordPress&utm_medium=link&utm_campaign=meta-box" target="_blank" rel="noopenner noreferrer">Slim SEO Link Manager</a> - { __( 'Build internal link easier in WordPress with real-time reports.', 'mb-custom-post-type' ) }</p>
						</div>
					</PanelRow>
				</PanelBody>
				<PanelBody title={ __( 'Write a review', 'mb-custom-post-type' ) } initialOpen={ true }>
					<PanelRow>
						<div className="mb-cpt-box">
							<p>{ __( 'If you like MB Custom Post Types & Custom Taxonomies, please write a review on WordPress.org to help us spread the word. We really appreciate that!', 'mb-custom-post-type' ) }</p>
							<p><a href="https://wordpress.org/support/plugin/mb-custom-post-type/reviews/?filter=5" className="button" target="_blank" rel="noopenner noreferrer">{ __( 'Write a review', 'mb-custom-post-type' ) }</a></p>
						</div>
					</PanelRow>
				</PanelBody>
				{ MBCPT.upgrade &&
					<PanelBody title={ __( 'Upgrade', 'mb-custom-post-type' ) } initialOpen={ true }>
						<PanelRow>
							<div className="mb-cpt-box">
								<p>{ __( 'Upgrade now to have more features & speedy technical support:', 'mb-custom-post-type' ) }</p>
								<ul>
									<li><span className="dashicons dashicons-yes"></span>{ __( 'Create custom fields with UI', 'mb-custom-post-type' ) }</li>
									<li><span className="dashicons dashicons-yes"></span>{ __( 'Add custom fields to terms and users', 'mb-custom-post-type' ) }</li>
									<li><span className="dashicons dashicons-yes"></span>{ __( 'Create custom settings pages', 'mb-custom-post-type' ) }</li>
									<li><span className="dashicons dashicons-yes"></span>{ __( 'Create frontend submission forms', 'mb-custom-post-type' ) }</li>
									<li><span className="dashicons dashicons-yes"></span>{ __( 'Create frontend templates', 'mb-custom-post-type' ) }</li>
									<li><span className="dashicons dashicons-yes"></span>{ __( 'And much more!', 'mb-custom-post-type' ) }</li>
								</ul>
								<a href="https://metabox.io/pricing/?utm_source=plugin_cpt&utm_medium=btn_upgrade&utm_campaign=cpt_upgrade" className="button" target="_blank" rel="noopenner noreferer">{ __( 'Upgrade now', 'mb-custom-post-type' ) }</a>
							</div>
						</PanelRow>
					</PanelBody>
				}
			</Panel>
		</div>
	);
};

export default Sidebar;