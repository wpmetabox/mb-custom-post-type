import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render } = wp.element;

const App = () => <SettingsProvider value={ MBCPT.settings ? MBCPT.settings : DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

render( <App />, document.getElementById( 'poststuff' ) );