import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';
import Result from './components/Result';
const { useEffect, useState } = wp.element;
const { Button } = wp.components;

const App = () => {
	let data = {};

	if ( MbCpt[0] ) {
		data = JSON.parse( MbCpt[0] );
	} else {
		data = DefaultSettings;
	}

	const [state, setState] = useState( data );
	const [showCode, setShowCode] = useState( false );

	if ( state['post_type'] && ! state['args_post_type'] ) {
		state['args_post_type'] = state['post_type'];
	}

	const handleShowCode = e => {
		e.preventDefault();
		setShowCode( true );
	}

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
			{ showCode && <Result /> }
		</PhpSettings.Provider>
	);
}

export default App;