import React from 'react';
import { Tab, Tabs, TabList, TabPanel } from 'react-tabs';
import 'react-tabs/style/react-tabs.css';
import { BasicDatas, LabelDatas, TaxonomyDatas, SupportDatas, AdvancedDatas } from '../constants/Data';
import Control from './controls/Control';

const MainTabs = () => (
	<>
		<Tabs forceRenderTabPanel={true}>
			<TabList>
				<Tab><i className="dashicons-admin-generic dashicons"></i> General</Tab>
				<Tab><i className="dashicons-tag dashicons"></i> Labels</Tab>
				<Tab><i className="dashicons-admin-settings dashicons"></i> Advanced</Tab>
				<Tab><i className="dashicons-edit-large dashicons"></i> Supports</Tab>
				<Tab><i className="dashicons-category dashicons"></i> Taxonomies</Tab>
			</TabList>

			<TabPanel>
				{ Object.keys( BasicDatas ).map( key => <Control key={key} props={BasicDatas[key]} autoFills={[...LabelDatas, ...BasicDatas]} /> ) }
			</TabPanel>
			<TabPanel>
				{ Object.keys( LabelDatas ).map( key => <Control key={key} props={LabelDatas[key]} /> ) }
			</TabPanel>
			<TabPanel>
				{ Object.keys( AdvancedDatas ).map( key => <Control key={key} props={AdvancedDatas[key]} /> ) }
			</TabPanel>
			<TabPanel>
				<Control name="supports" values={SupportDatas} props={SupportDatas} />
			</TabPanel>
			<TabPanel>
				<Control name="taxonomies" values={TaxonomyDatas} props={TaxonomyDatas} />
			</TabPanel>
		</Tabs>
	</>
);

export default MainTabs;