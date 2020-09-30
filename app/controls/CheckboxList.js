import PhpSettings from '../PhpSettings';
const { useContext } = wp.element;

const CheckboxList = ( { name, options, description } ) => {
	const [ state, setState ] = useContext( PhpSettings );

	const onChange = e => {
		const { value, checked } = e.target;
		let options = state[ name ];
		if ( checked ) {
			options.push( value );
		} else {
			options = options.filter( option => option !== value );
		}
		setState( { ...state, [ name ]: options } );
	};

	return (
		<div className="mb-cpt-field">
			<div className="mb-cpt-input">
				{ description && <div className="mb-cpt-description">{ description }</div> }
				<ul className="mb-cpt-input-list">
					{ options.map( ( option, key ) => (
						<li key={ key }>
							<label>
								<input type="checkbox" value={ option.value } checked={ state[ name ].includes( option.value ) } onChange={ onChange } />
								{ option.label }
							</label>
						</li>
					) ) }
				</ul>
			</div>
		</div>
	);
};

export default CheckboxList;