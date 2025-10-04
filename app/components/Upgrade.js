import { Tooltip, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { external } from '@wordpress/icons';

const Upgrade = () => MBCPT.upgrade &&
	<Tooltip delay={ 0 } text={ __( 'Get access to premium features like creating custom fields, conditional logic, custom table, frontend forms, settings pages, and more.', 'mb-custom-post-type' ) }>
		<Button
			variant="link"
			href="https://metabox.io/aio/?utm_source=header&utm_medium=link&utm_campaign=cpt"
			target="_blank"
			icon={ external }
			iconPosition="right"
			iconSize={ 18 }
			text={ __( 'Upgrade', 'mb-custom-post-type' ) }
		/>
	</Tooltip>

export default Upgrade;