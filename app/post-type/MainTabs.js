import { Flex, Icon, TabPanel, Tooltip } from '@wordpress/components';
import { useContext } from "@wordpress/element";
import { __ } from '@wordpress/i18n';
import { code } from "@wordpress/icons";
import { SettingsContext } from '../SettingsContext';
import Upgrade from '../components/Upgrade';
import CheckboxList from '../controls/CheckboxList';
import Control from '../controls/Control';
import { ReactComponent as Logo } from '../controls/logo.svg';
import Result from './Result';
import { AdvancedControls, BasicControls, CodeControls, FeatureControls, LabelControls, SupportControls } from './constants/Data';

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
		name: 'features',
		title: __( 'Features', 'mb-custom-post-type' ),
	},
	{
		name: 'code',
		icon: <Icon icon={ code } />,
		title: __( 'Get PHP Code', 'mb-custom-post-type' ),
		className: 'mb-cpt-code components-button is-small has-icon'
	}
];

let autoFills = [ ...LabelControls, ...BasicControls ];
autoFills.push( { name: 'label', default: '%name%', updateFrom: 'labels.name' } );

// Panels
const panels = {
	general: BasicControls.map( ( field, key ) => <Control key={ key } field={ field } autoFills={ autoFills.filter( f => f.updateFrom === field.name ) } /> ),
	labels: LabelControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	advanced: AdvancedControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	supports: <CheckboxList name="supports" options={ SupportControls } description={ __( 'Core features the post type supports:', 'mb-custom-post-type' ) } />,
	taxonomies: <CheckboxList name="taxonomies" options={ MBCPT.taxonomies } description={ __( 'Taxonomies that will be registered for the post type:', 'mb-custom-post-type' ) } />,
	features: FeatureControls.map( ( field, key ) => <Control key={ key } field={ field } /> ),
	code: (
		<>
			{ CodeControls.map( ( field, key ) => <Control key={ key } field={ field } /> ) }
			<Result />
		</>
	)
};

const MainTabs = () => {
	const { settings } = useContext( SettingsContext );

	return <>
		<Flex className="mb-header">
			<Flex gap={ 2 } expanded={ false }>
				<Tooltip text={ __( 'Back to all post types', 'mb-custom-post-type' ) } position={ 'bottom right' }>
					<a className="mb-header__logo" href={ MBCPT.url }><Logo /></a>
				</Tooltip>
				<h1>{ MBCPT.action === 'add' ? __( 'Add Post Type', 'mb-custom-post-type' ) : __( 'Edit Post Type', 'mb-custom-post-type' ) }</h1>
			</Flex>
			<Flex gap={ 1 } expanded={ false }>
				<Upgrade />
				<input
					type="submit"
					data-status="publish"
					className="mb-cpt-submit components-button is-primary"
					value={ __( 'Save Changes', 'mb-custom-post-type' ) }
				/>
			</Flex>
		</Flex>
		<div className="mb-cpt-body mb-body">
			<div className="mb-body__inner">
				<div className="mb-main">
					<div className="wp-header-end" />

					<TabPanel className="mb-box" tabs={ tabs }>
						{ tab => panels[ tab.name ] }
					</TabPanel>
				</div>
			</div>
		</div>
		<input type="hidden" name="post_title" value={ settings.labels.singular_name } />
		<input type="hidden" name="content" value={ JSON.stringify( settings ) } />
		<input type="hidden" name="post_status" value={ MBCPT.status } />
		<input type="hidden" name="messages" value="" />
	</>;
};

export default MainTabs;