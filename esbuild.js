const esbuild = require( "esbuild" );
const { externalGlobalPlugin } = require( "esbuild-plugin-external-global" );

const config = {
	bundle: true,
	minify: true,
	loader: {
		'.js': 'jsx',
	},
	plugins: [
		externalGlobalPlugin( {
			'react': 'React',
			'react-dom': 'ReactDOM',
			'codemirror': 'wp.CodeMirror',
			'clipboard': 'ClipboardJS',
			'@wordpress/i18n': 'wp.i18n',
			'@wordpress/element': 'wp.element',
		} ),
	],
};

esbuild.build( {
	...config,
	entryPoints: [ 'app/post-type/App.js' ],
	outfile: 'assets/post-type.js',
} );

esbuild.build( {
	...config,
	entryPoints: [ 'app/taxonomy/App.js' ],
	outfile: 'assets/taxonomy.js',
} );