/* eslint-env node, es6 */
module.exports = function ( grunt ) {
	grunt.loadNpmTasks( 'grunt-eslint' );

	grunt.initConfig( {
		eslint: {
			options: {
				cache: true
			},
			all: [
				'**/*.json',
				'!node_modules/**',
				'!vendor/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'eslint' ] );
	grunt.registerTask( 'default', 'test' );
};
