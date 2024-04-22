import { SettingsProvider } from '../SettingsContext';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';

const { render } = wp.element;

const App = () => <SettingsProvider value={ MBCPT.settings ? MBCPT.settings : DefaultSettings }>
	<MainTabs />
</SettingsProvider>;

const submit = ( e, submitButton, status, save ) => {
	e.preventDefault();
	const message = document.querySelector( '.mb-cpt-message' );

	submitButton.disabled = true;
	submitButton.textContent = MBCPT.saving;

	let formData = new FormData();
	formData.append( 'action', 'mbcpt_save_post_type' );
	formData.append( 'post_ID', document.querySelector( '#post_ID' ).value );
	formData.append( 'title', document.getElementsByName( "post_title" )[ 0 ].value );
	formData.append( 'content', document.getElementsByName( "content" )[ 0 ].value );
	formData.append( 'status', status );
	fetch( ajaxurl, {
		method: 'POST',
		body: formData
	} )
		.then( response => response.json() )
		.then( response => {
			submitButton.disabled = false;
			submitButton.textContent = save;

			message.textContent = response.data;
			message.classList.remove( 'hidden' );

			setTimeout( () => {
				message.classList.add( 'hidden' );
			}, 3000 );
		} );
};

const submitPublish = ( e ) => {
	const publicButton = document.querySelector( '.mb-cpt-publish' );
	submit( e, publicButton, 'publish', MBCPT.publish );
};

const submitDraft = ( e ) => {
	const draftButton = document.querySelector( '.mb-cpt-draft' );
	submit( e, draftButton, 'draft', MBCPT.draft );
};

render( <App />, document.getElementById( 'poststuff' ) );
document.querySelector( '.mb-cpt-publish' ).addEventListener( 'click', submitPublish );
document.querySelector( '.mb-cpt-draft' ).addEventListener( 'click', submitDraft );