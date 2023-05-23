module.exports = function (grunt) {

	// Load the grunt plugin
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-babel');

	grunt.initConfig({
		watch: {
			scripts: {
				files: ['public/custom/**/*.js'],
				tasks: ['concat', 'uglify', 'babel', 'clean:build'],
				options: {
					spawn: true
				}
			},
			// styles: {
			// 	files: ['public/custom/**/*.css'],
			// 	tasks: ['cssmin'],
			// 	options: {
			// 		spawn: false
			// 	}
			// }
		},
		cssmin: {
			options: {
				mergeIntoShorthands: false,
				roundingPrecision: -1
			},
			target: {
				files: {
					'output.css': ['foo.css', 'bar.css']
				}
			}
		},
		concat: {
			options: {
				separator: ';',
			},
			dist: {
				src: [
					'public/custom/js/jquery.min.js',
					'public/custom/js/axios.min.js',
					'public/custom/js/helper.js',
					'public/custom/js/html2canvas.js',
					'public/custom/js/printThis.js',
					'public/custom/js/validationJS.js',
					'public/custom/js/toastr.min.js',
					'public/custom/js/block-ui.js',
				],
				dest: 'public/dist/custom.js',
			},
		},
		uglify: {
			dist: {
				files: {
					'public/dist/custom-uglify.min.js': ['public/dist/custom.js'],
				},
			},
		},
		babel: {
			options: {
				sourceMap: true,
				presets: ['@babel/preset-env']
			},
			dist: {
				files: {
					'public/dist/custom.min.js': ['public/dist/custom-uglify.min.js'],
				}
			}
		},
		clean: {
			build: ['public/dist/custom.js', 'public/dist/custom-uglify.min.js']
		},
	});

	// Register the tasks
	grunt.registerTask('default', ['concat', 'uglify', 'babel', 'clean:build']);
};
