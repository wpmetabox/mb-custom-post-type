import { createRoot } from "@wordpress/element";
import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const App = () => <SettingsProvider value={ { ...DefaultSettings, ...MBCPT.settings } }>
	<MainTabs />
</SettingsProvider>;

const container = document.getElementById( 'poststuff' );
container.classList.add( 'mb-cpt' );
container.id = 'mb-cpt-app';

createRoot( container ).render( <App /> );
