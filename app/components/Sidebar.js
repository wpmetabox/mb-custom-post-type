import { Panel, PanelBody, PanelRow } from '@wordpress/components';
import { __ } from "@wordpress/i18n";

const Sidebar = () => (
	<Panel className="mb-cpt-sidebar">
		<PanelBody title={ __( 'Summary', 'mb-custom-post-type' ) } initialOpen={ true }>
			<PanelRow className="summary">
				<p className="status"><label>{ __( 'Status', 'mb-custom-post-type' ) }</label> { MBCPT.status }</p>
				<p><label>{ __( 'Published', 'mb-custom-post-type' ) }</label> { MBCPT.published }</p>
				{ MBCPT.modifiedtime && <p><label>{ __( 'Last modified', 'mb-custom-post-type' ) }</label> { MBCPT.modifiedtime }</p> }
				<p><label>{ __( 'Author', 'mb-custom-post-type' ) }</label> { MBCPT.author }</p>
				<p><a href={ MBCPT.trash }><button type="button" className="components-button is-secondary is-destructive">{ __( 'Move to trash', 'mb-custom-post-type' ) }</button></a></p>
			</PanelRow>
		</PanelBody>
		<PanelBody title={ __( 'Our WordPress Plugins', 'mb-custom-post-type' ) } initialOpen={ true }>
			<PanelRow>
				<p>{ __( 'Like this plugin? Check out our other WordPress plugins:', 'mb-custom-post-type' ) }</p>
				<p><a href="https://elu.to/mbcptss" target="_blank"><strong>Slim SEO</strong></a> - { __( 'A fast, lightweight and full-featured SEO plugin for WordPress with minimal configuration.', 'mb-custom-post-type' ) }</p>
				<p><a href="https://elu.to/mbcptsss" target="_blank"><strong>Slim SEO Schema</strong></a> - { __( 'An advanced, powerful and flexible plugin to add schemas to WordPress', 'mb-custom-post-type' ) }</p>
				<p><a href="https://elu.to/mbcptssl" target="_blank"><strong>Slim SEO Link Manager</strong></a> - { __( 'Build internal link easier in WordPress with real-time reports.', 'mb-custom-post-type' ) }</p>
			</PanelRow>
		</PanelBody>
		<PanelBody title={ __( 'Write a review', 'mb-custom-post-type' ) } initialOpen={ true }>
			<PanelRow>
				<p>{ __( 'If you like this plugin, please write a review on WordPress.org to help us spread the word. We really appreciate that!', 'mb-custom-post-type' ) }</p>
				<p><a href="https://wordpress.org/support/plugin/mb-custom-post-type/reviews/" target="_blank">{ __( 'Write a review', 'mb-custom-post-type' ) } &rarr;</a></p>
			</PanelRow>
		</PanelBody>
	</Panel>
);

export default Sidebar;