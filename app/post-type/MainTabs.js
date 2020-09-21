import { CodeDatas, BasicDatas, LabelDatas, TaxonomyDatas, SupportDatas, AdvancedDatas } from './constants/Data';
import Control from '../controls/Control';
import Result from './Result';
const { TabPanel } = wp.components;

const tabs = [
	{
		name: 'general',
		title: 'General',
	},
	{
		name: 'labels',
		title: 'Labels',
	},
	{
		name: 'advanced',
		title: 'Advanced',
	},
	{
		name: 'supports',
		title: 'Supports',
	},
	{
		name: 'taxonomies',
		title: 'Taxonomies',
	},
	{
		name: 'code',
		title: 'Get PHP Code',
		className: 'mb-cpt-code button button-small'
	}
];
const panels = {
	general: Object.keys( BasicDatas ).map( key => <Control key={key} props={BasicDatas[key]} autoFills={[...LabelDatas, ...BasicDatas]} /> ),
	labels: Object.keys( LabelDatas ).map( key => <Control key={key} props={LabelDatas[key]} /> ),
	advanced: Object.keys( AdvancedDatas ).map( key => <Control key={key} props={AdvancedDatas[key]} /> ),
	supports: <Control name="supports" values={SupportDatas} props={SupportDatas} />,
	taxonomies: <Control name="taxonomies" values={TaxonomyDatas} props={TaxonomyDatas} />,
	code: (
		<>
			{ Object.keys( CodeDatas ).map( key => <Control key={key} props={CodeDatas[key]} /> ) }
			<Result data='customPostType' />
		</>
	)
}

const MainTabs = () => <TabPanel className="mb-cpt-tabs" tabs={ tabs }>{ tab => panels[tab.name] }</TabPanel>

export default MainTabs;