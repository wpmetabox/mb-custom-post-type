import PhpSettings from '../PhpSettings';
import DefaultSettings from './constants/DefaultSettings';
import MainTabs from './MainTabs';
import { enqueueScript } from '../helper';

const { useEffect, useState } = wp.element;
const { Button } = wp.components;
const i18n = MbTaxonomy;

const App = () => {
	let data = i18n.settings ? JSON.parse( i18n.settings ) : DefaultSettings;

	const [state, setState] = useState( data );

	const showCode = () => enqueueScript( i18n.result );

	useEffect( () => {
		document.getElementById( 'post_title' ).value = state.singular_name;
		content = document.getElementById( 'content' ).value = JSON.stringify( state );
	} );

	return (
		<PhpSettings.Provider value={[state, setState]}>
			<MainTabs />
			<Button isPrimary onClick={ showCode }>Generate Code</Button>
		</PhpSettings.Provider>
	);
}

ReactDOM.render( <App />, document.getElementById( 'root' ) );