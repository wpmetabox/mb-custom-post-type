import { Button, Flex, TabPanel, Tooltip, Icon } from '@wordpress/components';
import { useContext, useReducer } from "@wordpress/element";
import { __ } from '@wordpress/i18n';
import { code, external } from "@wordpress/icons";
import { SettingsContext } from '../SettingsContext';
import Upgrade from '../components/Upgrade';
import CheckboxList from '../controls/CheckboxList';
import Control from '../controls/Control';
import { ReactComponent as Logo } from '../controls/logo.svg';
import Result from './Result';
import { AdvancedControls, BasicControls, CodeControls, LabelControls, PermissionsControls } from './constants/Data';

const tabs = [
	{
		name: 'general',
		title: __( 'General', 'mb-custom-post-type' ),
	},
	{
		name: 'labels',
		title: __( 'Labels', 'mb-custom-post-type' ),
	},
	{
		name: 'advanced',
		title: __( 'Advanced', 'mb-custom-post-type' ),
	},
	{
		name: 'types',
		title: __( 'Post Types', 'mb-custom-post-type' ),
	},
	{
		name: 'permissions',
		title: __( 'Permissions', 'mb-custom-post-type' ),
	},
	{
		name: 'code',
		title: <span title={ __( 'Get PHP Code', 'mb-custom-post-type' ) }>
				<Icon icon={ code } />
			</span>,
		className: 'mb-cpt-code components-button is-small has-icon'
	}
];

let autoFills = [ ...LabelControls, ...BasicControls ];
autoFills.push( { name: 'label', default: '%name%', updateFrom: 'labels.name' } );

const panels = {
	general: BasicControls.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ autoFills.filter( f => f.updateFrom === field.name ) } /> ),
	labels: LabelControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	advanced: AdvancedControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	permissions: PermissionsControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	types: <CheckboxList name="types" options={ MBCPT.types } description={ __( 'Post types for the taxonomy:', 'mb-custom-post-type' ) } />,
	code: (
		<>
			{ CodeControls.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			<Result />
		</>
	)
};

const MainTabs = () => {
	const { settings } = useContext( SettingsContext );

	return <>
		<Flex className="mb-cpt-header mb-header">
			<Flex gap={ 2 } expanded={ false }>
				<Tooltip text={ __( 'Back to all taxonomies', 'mb-custom-post-type' ) } position={ 'bottom right' }>
					<a className="logo mb-header__left" href={ MBCPT.url }><Logo /></a>
				</Tooltip>
				<h1 className="mb-header__title">{ MBCPT.action === 'add' ? __( 'Add Taxonomies', 'mb-custom-post-type' ) : __( 'Edit Taxonomies', 'mb-custom-post-type' ) }</h1>
				{ MBCPT.action !== 'add' && <a className="page-title-action" href={ MBCPT.add }>{ __( 'Add New', 'mb-custom-post-type' ) }</a> }
			</Flex>
			<Flex gap={ 3 } expanded={ false } className="mb-cpt-action">
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
				<input
					type="submit"
					data-status="publish"
					className="mb-cpt-submit button button-primary btn-php"
					value={ __( 'Save Changes', 'mb-custom-post-type' ) }
				/>
			</Flex>
		</Flex>
		<Flex gap={ 0 } align="flex-start" className="mb-cpt-body">
			<div className="mb-cpt-content">
				<div className="mb-cpt-tabs-wrapper mb-main">
					<div className="wp-header-end" />

					<TabPanel className="mb-box" tabs={ tabs }>
						{ tab => panels[ tab.name ] }
					</TabPanel>

					<Upgrade />
				</div>
			</div>
		</Flex>
		<input type="hidden" name="post_title" value={ settings.labels.singular_name } />
		<input type="hidden" name="content" value={ JSON.stringify( settings ) } />
		<input type="hidden" name="post_status" value={ MBCPT.status } />
		<input type="hidden" name="messages" value="" />
	</>;
};

export default MainTabs;