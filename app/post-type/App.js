import { createRoot } from "@wordpress/element";
import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const App = () => <SettingsProvider value={ MBCPT.settings || DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

const submit = e => {
	const submitButton = e.submitter;
	const status = submitButton.getAttribute( 'name' );
	const originalStatus = document.querySelector( '#original_post_status' ).value;
	if ( originalStatus !== status ) {
		document.querySelector( '.mb-cpt-messages' ).setAttribute( 'name', ( MBCPT.status != 'publish' ) ? 'publish' : 'save' );
	}
	if ( originalStatus == 'auto-draft' && status == 'draft' ) {
		document.querySelector( '.mb-cpt-messages' ).setAttribute( 'name', 'save' );
	}
	submitButton.disabled = true;
	submitButton.setAttribute( 'value', MBCPT.saving );
	document.querySelector( '.post_status' ).setAttribute( 'value', status );
};

document.querySelector( '.wp-header-end' ).remove();

const container = document.getElementById( 'poststuff' );
container.classList.add( 'mb-cpt' );
container.id = 'mb-cpt-app';

const root = createRoot( container );
root.render( <App /> );

document.querySelector( '#post' ).addEventListener( 'submit', submit );