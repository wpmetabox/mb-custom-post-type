import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './components/MainTabs';
import Result from './components/Result';
const { useEffect, useState } = wp.element;
const { Button } = wp.components;
const i18n = MbTaxonomy;

const App = () => {
	let data = i18n.settings ? JSON.parse( i18n.settings ) : DefaultSettings;

	const [state, setState] = useState( data );
	const [showCode, setShowCode] = useState( false );

	if ( state['taxonomy'] && ! state['args_taxonomy'] ) {
		state['args_taxonomy'] = state['taxonomy'];
	}

	const handleShowCode = e => {
		e.preventDefault();
		setShowCode( true );
	}

	useEffect( () => {
		document.getElementById( 'post_title' ).value = state.singular_name;
		content = document.getElementById( 'content' ).value = JSON.stringify( state );
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