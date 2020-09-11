const path = require('path')

const config = {
	entry: './app/index.js',
	output: {
		filename: 'post-type.js',
		path: path.resolve( __dirname, './js' )
	},
	module: {
		rules: [
			{
				test: /\.js/,
				exclude: /node_modules/,
				use: {
				loader: 'babel-loader',
					options: {
						plugins: ['@babel/plugin-transform-react-jsx', '@babel/plugin-proposal-class-properties']
					}
				}
			},
			{
				test: /\.css$/,
				use: ['style-loader', 'css-loader']
			}
		]
	}
}

module.exports = config;