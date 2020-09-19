import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';

const { useEffect, useState } = wp.element;
const { Button } = wp.components;
const i18n = MbPostType;

const Spinner = () => <span>Generating code. Please wait...</span>;

const enqueueScript = ( file ) => {
	var script = document.createElement( 'script' );

	script.setAttribute( 'src', file );
	document.getElementsByTagName( "head" )[0].appendChild( script );
};

const App = () => {
	let data = i18n.settings ? JSON.parse( i18n.settings ) : DefaultSettings;

	const [state, setState] = useState( data );

	if ( state['post_type'] && ! state['args_post_type'] ) {
		state['args_post_type'] = state['post_type'];
	}

	const handleShowCode = () => enqueueScript( i18n.result );

	useEffect( () => {
		const title = document.getElementById( 'title' );
		title.value = state.name;

		const name = document.getElementById( 'name' );
		name.value = state.singular_name;

		const content = document.getElementById( 'content' );
		content.value = JSON.stringify( state );
	} );

	return (
		<PhpSettings.Provider value={[state, setState]}>
			<MainTabs />
			<Button isPrimary onClick={ handleShowCode }>Generate Code</Button>
		</PhpSettings.Provider>
	);
}

export default App;