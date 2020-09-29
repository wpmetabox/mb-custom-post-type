import { CodeDatas, BasicDatas, LabelDatas, TaxonomyDatas, SupportDatas, AdvancedDatas } from './constants/Data';
import Control from '../controls/Control';
import CheckboxList from '../controls/CheckboxList';
import Result from './Result';
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
const panels = {
	general: BasicDatas.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ [ ...LabelDatas, ...BasicDatas ].filter( f => f.updateFrom === field.name ) } /> ),
	labels: LabelDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	advanced: AdvancedDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	supports: <CheckboxList name="supports" options={ SupportDatas } description={ __( 'Core features the post type supports:', 'mb-custom-post-type' ) } />,
	taxonomies: <CheckboxList name="taxonomies" options={ TaxonomyDatas } description={ __( 'Taxonomies that will be registered for the post type:', 'mb-custom-post-type' ) } />,
	code: (
		<>
			{ CodeDatas.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			<Result />
		</>
	)
};

const MainTabs = () => <TabPanel className="mb-cpt-tabs" tabs={ tabs }>{ tab => panels[ tab.name ] }</TabPanel>;

export default MainTabs;