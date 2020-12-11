import { __ } from '@wordpress/i18n';
import CheckboxList from '../controls/CheckboxList';
import Control from '../controls/Control';
import { SettingsContext } from '../SettingsContext';
import { AdvancedDatas, BasicDatas, CodeDatas, LabelDatas } from './constants/Data';
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
		name: 'types',
		title: __( 'Post Types', 'mb-custom-post-type' ),
	},
	{
		name: 'code',
		title: __( 'Get PHP Code', 'mb-custom-post-type' ),
		className: 'mb-cpt-code button button-small'
	}
];

let autoFills = [ ...LabelDatas, ...BasicDatas ];
autoFills.push( { name: 'label', default: '%name%', updateFrom: 'labels.name' } );

const panels = {
	general: BasicDatas.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ autoFills.filter( f => f.updateFrom === field.name ) } /> ),
	labels: LabelDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	advanced: AdvancedDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	types: <CheckboxList name="types" options={ MBCPT.types } description={ __( 'Post types for the taxonomy:', 'mb-custom-post-type' ) } />,
	code: (
		<>
			{ CodeDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			<Result />
		</>
	)
};

const MainTabs = () => {
	const { settings } = useContext( SettingsContext );

	return <>
		<TabPanel className="mb-cpt-tabs" tabs={ tabs }>{ tab => panels[ tab.name ] }</TabPanel>
		<input type="hidden" name="post_title" value={ settings.labels.singular_name } />
		<input type="hidden" name="content" value={ JSON.stringify( settings ) } />
	</>;
};

export default MainTabs;