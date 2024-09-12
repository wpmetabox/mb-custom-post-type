import { Button, Flex, TabPanel, Tooltip } from '@wordpress/components';
import { useContext, useState } from "@wordpress/element";
import { __ } from '@wordpress/i18n';
import { drawerRight } from "@wordpress/icons";
import { SettingsContext } from '../SettingsContext';
import Sidebar from '../components/Sidebar';
import Upgrade from '../components/Upgrade';
import CheckboxList from '../controls/CheckboxList';
import Control from '../controls/Control';
import { ReactComponent as Logo } from '../controls/logo.svg';
import Result from './Result';
import { AdvancedControls, BasicControls, CodeControls, LabelControls } from './constants/Data';

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
		name: 'code',
		title: __( 'Get PHP Code', 'mb-custom-post-type' ),
		className: 'mb-cpt-code button button-small'
	}
];

let autoFills = [ ...LabelControls, ...BasicControls ];
autoFills.push( { name: 'label', default: '%name%', updateFrom: 'labels.name' } );

const panels = {
	general: BasicControls.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ autoFills.filter( f => f.updateFrom === field.name ) } /> ),
	labels: LabelControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	advanced: AdvancedControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
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
	const [ toggle, setToggle ] = useState( true );

	return <>
		<Flex className="mb-cpt-header">
			<Flex gap={ 2 } expanded={ false }>
				<Tooltip text={ __( 'Back to all taxonomies', 'mb-custom-post-type' ) } position={ 'bottom right' }>
					<a className="logo" href={ MBCPT.url }><Logo /></a>
				</Tooltip>
				<h1>{ ( MBCPT.action == 'add' ) ? __( 'Add Taxonomies', 'mb-custom-post-type' ) : __( 'Edit Taxonomies', 'mb-custom-post-type' ) }</h1>
				{ !( MBCPT.action == 'add' ) && <a className="page-title-action" href={ MBCPT.add }>{ __( 'Add New', 'mb-custom-post-type' ) }</a> }
			</Flex>
			<Flex gap={ 3 } expanded={ false } className="mb-cpt-action">
				<input type="submit" name="draft" className="components-button is-compact is-tertiary mb-cpt-draft" value={ ( MBCPT.status == 'publish' ) ? __( 'Switch to draft', 'mb-custom-post-type' ) : __( 'Save draft', 'mb-custom-post-type' ) } />
				<input type="submit" name="publish" className="mb-cpt-publish components-button is-primary" value={ ( MBCPT.status == 'publish' ) ? __( 'Update', 'mb-custom-post-type' ) : __( 'Publish', 'mb-custom-post-type' ) } />
				<Button onClick={ () => setToggle( !toggle ) } className="is-compact" icon={ drawerRight } size="compact" label={ __( 'Toggle sidebar', 'mb-custom-post-type' ) } showTooltip={ true } isPressed={ toggle } />
			</Flex>
		</Flex>
		<Flex gap={ 0 } align="flex-start" className="mb-cpt-body">
			<div className="mb-cpt-content">
				<div className="mb-cpt-tabs-wrapper">
					<div className="wp-header-end" />

					<TabPanel className="mb-cpt-tabs" tabs={ tabs }>
						{ tab => panels[ tab.name ] }
					</TabPanel>

					<Upgrade />
				</div>
			</div>
			{ toggle && <Sidebar /> }
		</Flex>
		<input type="hidden" name="post_title" value={ settings.labels.singular_name } />
		<input type="hidden" name="content" value={ JSON.stringify( settings ) } />
		<input type="hidden" className="post_status" name="post_status" value={ MBCPT.status || 'draft' } />
		<input type="hidden" name="messages" className="mb-cpt-messages" value="" />
	</>;
};

export default MainTabs;