module.exports = function(grunt) {

	// Config
	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		// define tasks
		uglify: {
			files: {
				expand: true,
				src: [ 'assets/js/*.js', '!assets/js/*.min.js' ],  // source files
				ext: '.min.js'   // replace .js with .min.js
			}
		},

		cssmin: {
			minify: {
				expand: true,
				src: [ 'assets/css/*.css', '!assets/css/*.min.css' ],
				ext: '.min.css'
			}
		},

		watch: {
			js:  {
				files: ['assets/js/*.js', '!assets/js/*.min.js'],
				tasks: [ 'uglify' ]
			},
			browserify: {
				files: 'assets/js/src/**',
				tasks: ['browserify:script', 'browserify:admin']
			},
			css: {
				files: ['assets/css/*.css', '!assets/css/*.min.css'],
				tasks: [ 'cssmin' ]
			}
		},

		browserify: {

			script: {
				src: ['assets/js/src/script.js'],
				dest: 'assets/js/script.js'
			},
			admin: {
				src: ['assets/js/src/admin-script.js'],
				dest: 'assets/js/admin-script.js'
			}
		}
	});

	// load plugins
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-browserify');


	// register at least this one task
	grunt.registerTask('default', [ 'browserify:script', 'browserify:admin', 'uglify', 'cssmin' ]);

};