import { createRoot } from "@wordpress/element";
import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const normalizeSettings = settings => {
	const { show_in_menu } = settings;
	if ( [ true, false, 'true', 'false' ].includes( show_in_menu ) ) {
		return {
			...settings,
			show_in_menu: String( show_in_menu ),
		};
	}
	if ( show_in_menu && show_in_menu !== 'custom' ) {
		return {
			...settings,
			show_in_menu: 'custom',
			parent: show_in_menu,
		};
	}
	return settings;
};

const App = () => <SettingsProvider value={ { ...DefaultSettings, ...normalizeSettings( MBCPT.settings ) } }>
	<MainTabs />
</SettingsProvider>;

const container = document.getElementById( 'poststuff' );
container.classList.add( 'mb-cpt' );
container.id = 'mb-cpt-app';

createRoot( container ).render( <App /> );
