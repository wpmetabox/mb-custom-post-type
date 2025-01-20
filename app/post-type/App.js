import { render } from "@wordpress/element";
import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const App = () => <SettingsProvider value={ MBCPT.settings || DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

const container = document.getElementById( 'poststuff' );
container.classList.add( 'mb-cpt' );
container.id = 'mb-cpt-app';

// Use React 17 to make the rendering synchronous to make sure WordPress's JS (like detecting #submitdiv or .wp-header-end)
// runs after the app is rendered.
render( <App />, container );
