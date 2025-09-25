import { Tooltip, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { external } from '@wordpress/icons';

const Upgrade = () => MBCPT.upgrade &&
	<Tooltip delay={ 0 } text={ __( 'Get access to premium features like conditional logic, custom table, frontend forms, settings pages, and more.', 'meta-box-builder' ) }>
		<Button
			variant="link"
			href="https://metabox.io/aio/?utm_source=header&utm_medium=link&utm_campaign=builder"
			target="_blank"
			icon={ external }
			iconPosition="right"
			iconSize={ 18 }
			text={ __( 'Upgrade', 'meta-box-builder' ) }
		/>
	</Tooltip>

export default Upgrade;