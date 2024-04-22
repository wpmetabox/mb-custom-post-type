import { __ } from "@wordpress/i18n";

const Sidebar = () => {
	return (
		<div className="mb-cpt-sidebar">
			<div className="mb-cpt-box">
				<h2 className="title">{ __( 'Our WordPress Plugins', 'mb-custom-post-type' ) }</h2>
				<p>{ __( 'Like this plugin? Check out our other WordPress plugins:', 'mb-custom-post-type' ) }</p>
				<p><a href="https://elu.to/fsm" target="_blank"><strong>Meta Box</strong></a> - { __( 'A powerful WordPress plugin for creating custom post types and custom fields.', 'mb-custom-post-type' ) }</p>
				<p><a href="https://elu.to/fss" target="_blank"><strong>Slim SEO</strong></a> - { __( 'A fast, lightweight and full-featured SEO plugin for WordPress with minimal configuration.', 'mb-custom-post-type' ) }</p>
			</div>
		</div>
	);
};

export default Sidebar;