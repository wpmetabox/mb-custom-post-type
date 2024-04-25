import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render } = wp.element;

const App = () => <SettingsProvider value={ MBCPT.settings ? MBCPT.settings : DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

const submit = ( e ) => {
	e.preventDefault();
	const message = document.querySelector( '.mb-cpt-message' );
	const submitButton = e.submitter;
	const status = submitButton.getAttribute( 'name' );

	submitButton.disabled = true;
	submitButton.setAttribute( 'value', MBCPT.saving );

	let formData = new FormData( e.target );
	formData.append( 'action', 'mbcpt_save_post_type' );
	formData.append( 'status', status );
	fetch( ajaxurl, {
		method: 'POST',
		body: formData
	} )
		.then( response => response.json() )
		.then( response => {
			submitButton.disabled = false;
			document.querySelector( '.mb-cpt-publish' ).setAttribute( 'value', response.data.publish );
			document.querySelector( '.mb-cpt-draft' ).setAttribute( 'value', response.data.draft );

			message.textContent = response.data.message;
			message.classList.remove( 'hidden' );

			setTimeout( () => {
				message.classList.add( 'hidden' );
			}, 3000 );
		} );
};

render( <App />, document.getElementById( 'poststuff' ) );
document.querySelector( '#post' ).addEventListener( 'submit', submit );