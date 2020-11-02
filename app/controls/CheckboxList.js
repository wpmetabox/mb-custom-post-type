import dotProp from 'dot-prop';
import PhpSettings from '../PhpSettings';
const { useContext } = wp.element;

const CheckboxList = ( { name, options, description } ) => {
	const [ state, setState ] = useContext( PhpSettings );
	const saved = dotProp.get( state, name, [] );

	const onChange = e => {
		const { value, checked } = e.target;
		let newSaved = [...saved];
		if ( checked ) {
			newSaved.push( value );
		} else {
			newSaved = newSaved.filter( option => option !== value );
		}
		setState( { ...state, [ name ]: newSaved } );
	};

	return (
		<div className="mb-cpt-field">
			<div className="mb-cpt-input">
				{ description && <div className="mb-cpt-description">{ description }</div> }
				<ul className="mb-cpt-input-list">
					{ Object.entries( options ).map( ( [ value, label ] ) => (
						<li key={ value }>
							<label>
								<input type="checkbox" value={ value } checked={ saved.includes( value ) } onChange={ onChange } />
								{ label }
							</label>
						</li>
					) ) }
				</ul>
			</div>
		</div>
	);
};

export default CheckboxList;