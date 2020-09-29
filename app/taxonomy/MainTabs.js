import { CodeDatas, BasicDatas, LabelDatas, PostTypeDatas, AdvancedDatas } from './constants/Data';
import Control from '../controls/Control';
import Result from './Result';
import CheckboxList from '../controls/CheckboxList';
const { TabPanel } = wp.components;
const { __ } = wp.i18n;
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
		name: 'post_types',
		title: __( 'Post Types', 'mb-custom-post-type' ),
	},
	{
		name: 'code',
		title: __( 'Get PHP Code', 'mb-custom-post-type' ),
		className: 'mb-cpt-code button button-small'
	}
];

let autoFills= [ ...LabelDatas, ...BasicDatas ];
autoFills.push( { name: 'label', default: '%name%', updateFrom: 'labels.name' } );

const panels = {
	general: BasicDatas.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ autoFills.filter( f => f.updateFrom === field.name ) } /> ),
	labels: LabelDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	advanced: AdvancedDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	post_types: <CheckboxList name="post_types" options={ PostTypeDatas } description={ __( 'Post types for the taxonomy:', 'mb-custom-post-type' ) } />,
	code: (
		<>
			{ CodeDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			<Result />
		</>
	)
};

const MainTabs = () => <TabPanel className="mb-cpt-tabs" tabs={ tabs }>{ tab => panels[ tab.name ] }</TabPanel>;

export default MainTabs;