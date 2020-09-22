import { CodeDatas, BasicDatas, LabelDatas, TaxonomyDatas, SupportDatas, AdvancedDatas } from './constants/Data';
import PhpSettings from '../PhpSettings';
import Control from '../controls/Control';
import Result from './Result';
const { useContext } = wp.element;
const { TabPanel } = wp.components;
const { __ } = wp.i18n;

const MainTabs = () => {
	const [state, setState] = useContext( PhpSettings );
	let _codeDatas = [];
	// Object.keys( CodeDatas ).forEach( e => {
	// 	temp.push( { name: e, description: postTypes[e], checked: supportPostTypes.includes( e ) ? true : false } )
	// } );

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
		general: Object.keys( BasicDatas ).map( key => <Control key={key} props={BasicDatas[key]} autoFills={[...LabelDatas, ...BasicDatas]} /> ),
		labels: Object.keys( LabelDatas ).map( key => <Control key={key} props={LabelDatas[key]} /> ),
		advanced: Object.keys( AdvancedDatas ).map( key => <Control key={key} props={AdvancedDatas[key]} /> ),
		supports: <Control name="supports" values={SupportDatas} props={SupportDatas} />,
		taxonomies: <Control name="taxonomies" values={TaxonomyDatas} props={TaxonomyDatas} />,
		code: (
			<>
				{ Object.keys( CodeDatas ).map( key => <Control key={key} props={CodeDatas[key]} /> ) }
				<Result />
			</>
		)
	}

	return <TabPanel className="mb-cpt-tabs" tabs={ tabs }>{ tab => panels[tab.name] }</TabPanel>
}

export default MainTabs;