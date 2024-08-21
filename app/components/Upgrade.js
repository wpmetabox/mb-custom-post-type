import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Icon, arrowRight, check } from '@wordpress/icons';

const Upgrade = () => MBCPT.upgrade &&
	<div className="mb-cpt-upgrade">
		<h2>
			{ __( 'Get Meta Box Premium', 'mb-custom-post-type' ) }
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M11.219 3.375 8 7.399 4.781 3.375A1.002 1.002 0 0 0 3 4v15c0 1.103.897 2 2 2h14c1.103 0 2-.897 2-2V4a1.002 1.002 0 0 0-1.781-.625L16 7.399l-3.219-4.024c-.381-.474-1.181-.474-1.562 0zM5 19v-2h14.001v2H5zm10.219-9.375c.381.475 1.182.475 1.563 0L19 6.851 19.001 15H5V6.851l2.219 2.774c.381.475 1.182.475 1.563 0L12 5.601l3.219 4.024z"></path></svg>
		</h2>
		<p>{ __( 'Get the powerful framework to build custom fields & custom data for WordPress with the expert support.', 'mb-custom-post-type' ) }</p>
		<div className="mb-cpt-upgrade__features">
			<ul>
				<li><Icon icon={ check } size={ 16 } />{ __( '50+ custom field types', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Building relationships between posts, terms & users', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Submiting posts from frontend', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Creating custom Gutenberg blocks', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Creating user profile forms', 'mb-custom-post-type' ) }</li>
			</ul>
			<ul>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Creating custom settings pages', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Creating Customizer panels & sections', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Saving data in custom tables', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Integrations with all page builders', 'mb-custom-post-type' ) }</li>
				<li><Icon icon={ check } size={ 16 } />{ __( 'Life-time deals available', 'mb-custom-post-type' ) }</li>
			</ul>
		</div>
		<Button href="https://elu.to/mbcptsp" target="_blank">{ __( 'Upgrade now', 'mb-custom-post-type' ) } <Icon icon={ arrowRight } /></Button>
	</div>;

export default Upgrade;