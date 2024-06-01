import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render } = wp.element;

const App = () => <SettingsProvider value={ MBCPT.settings || DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

const submit = e => {
	const submitButton = e.submitter;
	const status = submitButton.getAttribute( 'name' );
	submitButton.disabled = true;
	submitButton.setAttribute( 'value', MBCPT.saving );
	document.querySelector( '.post_status' ).setAttribute( 'value', status );
};

document.querySelector( '.wp-header-end' ).remove();

render( <App />, document.getElementById( 'poststuff' ) );

document.querySelector( '#post' ).addEventListener( 'submit', submit );