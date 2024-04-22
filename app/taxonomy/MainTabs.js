import { __ } from '@wordpress/i18n';
import CheckboxList from '../controls/CheckboxList';
import Control from '../controls/Control';
import Sidebar from '../controls/Sidebar';
import { SettingsContext } from '../SettingsContext';
import { AdvancedControls, BasicControls, CodeControls, LabelControls } from './constants/Data';
import Result from './Result';
const { useContext } = wp.element;
const { TabPanel } = wp.components;
const tabs = [
	{
		name: 'logo',
		title: ( MBCPT.action == 'add' ) ? __( 'Add Taxonomy', 'mb-custom-post-type' ) : __( 'Edit Taxonomy', 'mb-custom-post-type' ),
		className: 'mb-cpt-logo',
		disabled: true
	},
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
	},
	{
		name: 'draft',
		title: __( 'Save Draft', 'mb-custom-post-type' ),
		className: 'mb-cpt-draft button button-small'
	},
	{
		name: 'publish',
		title: __( 'Publish', 'mb-custom-post-type' ),
		className: 'mb-cpt-publish button button-primary button-large'
	}
];

let autoFills = [ ...LabelControls, ...BasicControls ];
autoFills.push( { name: 'label', default: '%name%', updateFrom: 'labels.name' } );

const panels = {
	general: (
		<>
			<div className="mb-cpt-content">
				{ BasicControls.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ autoFills.filter( f => f.updateFrom === field.name ) } /> ) }
			</div>
			<Sidebar />
		</>
	),
	labels: (
		<>
			<div className="mb-cpt-content">
				{ LabelControls.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			</div>
			<Sidebar />
		</>
	),
	advanced: (
		<>
			<div className="mb-cpt-content">
				{ AdvancedControls.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			</div>
			<Sidebar />
		</>
	),
	types: (
		<>
			<div className="mb-cpt-content">
				<CheckboxList name="types" options={ MBCPT.types } description={ __( 'Post types for the taxonomy:', 'mb-custom-post-type' ) } />
			</div>
			<Sidebar />
		</>
	),
	code: (
		<>
			<div className="mb-cpt-content">
				{ CodeControls.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
				<Result />
			</div>
			<Sidebar />
		</>
	)
};

const MainTabs = () => {
	const { settings } = useContext( SettingsContext );

	return <>
		<div className="mb-cpt">
			<TabPanel className="mb-cpt-tabs" tabs={ tabs }>
				{ tab => panels[ tab.name ] }
			</TabPanel>
			<div className="mb-cpt-message hidden"></div>
			<input type="hidden" name="post_title" value={ settings.labels.singular_name } />
			<input type="hidden" name="content" value={ JSON.stringify( settings ) } />
		</div>
	</>;
};

export default MainTabs;