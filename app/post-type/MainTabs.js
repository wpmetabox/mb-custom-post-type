import { __ } from '@wordpress/i18n';
import { Icon } from '@wordpress/components';
import { Tooltip } from '@wordpress/components';
import { useState } from "@wordpress/element";
import CheckboxList from '../controls/CheckboxList';
import Control from '../controls/Control';
import Sidebar from '../controls/Sidebar';
import Logo from '../controls/logo.svg';
import { SettingsContext } from '../SettingsContext';
import { AdvancedControls, BasicControls, CodeControls, LabelControls, SupportControls } from './constants/Data';
import Result from './Result';
const { useContext } = wp.element;
const { TabPanel } = wp.components;
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
		name: 'supports',
		title: __( 'Supports', 'mb-custom-post-type' ),
	},
	{
		name: 'taxonomies',
		title: __( 'Taxonomies', 'mb-custom-post-type' ),
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
	supports: <CheckboxList name="supports" options={ SupportControls } description={ __( 'Core features the post type supports:', 'mb-custom-post-type' ) } />,
	taxonomies: <CheckboxList name="taxonomies" options={ MBCPT.taxonomies } description={ __( 'Taxonomies that will be registered for the post type:', 'mb-custom-post-type' ) } />,
	code: (
		<>
			{ CodeControls.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			<Result />
		</>
	)
};


const MainTabs = () => {
	const { settings } = useContext( SettingsContext );
	const [ toggle, setToggle ] = useState( false );

	return <>
		<div className="mb-cpt">
			<div className="mb-cpt-header">
				<div className="mb-cpt-logo">
					<Tooltip text={ __( 'Back all post type', 'mb-custom-post-type' ) } position={ 'bottom right' }>
						<a href={ MBCPT.url }><Logo /></a>
					</Tooltip>
					<h1>{ ( MBCPT.action == 'add' ) ? __( 'Add Post Type', 'mb-custom-post-type' ) : __( 'Edit Post Type', 'mb-custom-post-type' ) }</h1>
				</div>
				<div className="mb-cpt-action">
					<input type="submit" name="draft" className="components-button is-compact is-tertiary mb-cpt-draft" value={ ( MBCPT.status == 'publish' ) ? __( 'Switch to draft', 'mb-custom-post-type' ) : __( 'Save draft', 'mb-custom-post-type' ) } />
					<input type="submit" name="publish" className="mb-cpt-publish button button-primary button-large" value={ ( MBCPT.status == 'publish' ) ? __( 'Update', 'mb-custom-post-type' ) : __( 'Publish', 'mb-custom-post-type' ) } />
					<Tooltip text={ __( 'Toggle sidebar', 'mb-custom-post-type' ) }>
						<Icon icon="format-aside" onClick={ () => setToggle( !toggle ) } className="toggle-sidebar" />
					</Tooltip>
				</div>
			</div>
			<div className="mb-cpt-tabs">
				<TabPanel className="mb-cpt-content" tabs={ tabs }>
					{ tab => panels[ tab.name ] }
				</TabPanel>
				{ toggle && <Sidebar /> }
			</div>
			<div className="mb-cpt-message hidden"></div>
			<input type="hidden" name="post_title" value={ settings.labels.singular_name } />
			<input type="hidden" name="content" value={ JSON.stringify( settings ) } />
		</div>
	</>;
};

export default MainTabs;