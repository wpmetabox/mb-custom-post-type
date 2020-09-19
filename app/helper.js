export const enqueueScript = url => {
	let script = document.createElement( 'script' );
	script.setAttribute( 'src', url );
	document.body.appendChild( script );
};