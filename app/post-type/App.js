import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render } = wp.element;

const App = () => <SettingsProvider value={ MBCPT.settings ? MBCPT.settings : DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

const submit = ( e ) => {
	const message = document.querySelector( '.mb-cpt-message' );
	const submitButton = e.submitter;
	const status = submitButton.getAttribute( 'name' );
	submitButton.disabled = true;
	submitButton.setAttribute( 'value', MBCPT.saving );
	document.querySelector( '.post_status' ).setAttribute( 'value', status );
	message.textContent = MBCPT.message;
	message.classList.remove( 'hidden' );
	setTimeout( () => {
		message.classList.add( 'hidden' );
	}, 3000 );
};

const sidebar = () => {
	document.querySelector( '.toggle-sidebar' ).classList.toggle( 'is-active' );
};

document.querySelector('.wp-header-end').remove();

render( <App />, document.getElementById( 'poststuff' ) );
document.querySelector( '#post' ).addEventListener( 'submit', submit );
document.querySelector( '.toggle-sidebar' ).addEventListener( 'click', sidebar );