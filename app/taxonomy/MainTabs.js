import { CodeDatas, BasicDatas, LabelDatas, PostTypeDatas, AdvancedDatas } from './constants/Data';
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
		name: 'post_types',
		title: 'Post Types',
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
	post_types: <Control name="supports" values={PostTypeDatas} props={PostTypeDatas} />,
	code: (
		<>
			{ Object.keys( CodeDatas ).map( key => <Control key={key} props={CodeDatas[key]} /> ) }
			<Result />
		</>
	)
}

const MainTabs = () => <TabPanel className="mb-cpt-tabs" tabs={ tabs }>{ tab => panels[tab.name] }</TabPanel>

export default MainTabs;