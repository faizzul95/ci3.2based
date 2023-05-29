var jsPathDest = 'public/dist/custom.js';

var uglifyDistFiles = {
	'public/dist/custom-uglify.min.js': ['public/dist/custom.js']
};

var clearBuild = [
	'public/dist/custom.js',
	'public/dist/custom-uglify.min.js',
	'public/dist/js/*.custom.min.js',
	'public/dist/css/*.custom.css',
];

// // CUSTOM : JAVASCRIPT
var jsPathSrc = [
	'public/custom/js/jquery.min.js',
	'public/custom/js/axios.min.js',
	'public/custom/js/helper.js',
	'public/custom/js/html2canvas.js',
	'public/custom/js/printThis.js',
	'public/custom/js/validationJS.js',
	'public/custom/js/toastr.min.js',
	'public/custom/js/block-ui.js',
];

var babelDistFiles = {
	'public/dist/js/custom.min.js': ['public/dist/custom-uglify.min.js'],
};

// CUSTOM : CSS
var cssTargetFiles = {
	'public/dist/css/custom.css': ['public/custom/css/toastr.min.css', 'public/custom/css/skeleton.css']
};

module.exports = function (grunt) {

	// Load the grunt plugin
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-babel');
	grunt.loadNpmTasks('grunt-file-rev');

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
		rev: {
			options: {
				algorithm: 'sha512',
				length: 8
			},
			assets: {
				files: {
					src: ['public/dist/js/custom.min.js', 'public/dist/css/custom.css']
				}
			}
		},
		cssmin: {
			options: {
				mergeIntoShorthands: false,
				roundingPrecision: -1
			},
			target: {
				files: cssTargetFiles
			}
		},
		concat: {
			options: {
				separator: ';',
			},
			dist: {
				src: jsPathSrc,
				dest: jsPathDest,
			},
		},
		uglify: {
			dist: {
				files: uglifyDistFiles,
			},
		},
		babel: {
			options: {
				sourceMap: true,
				presets: ['@babel/preset-env']
			},
			dist: {
				files: babelDistFiles
			}
		},
		clean: {
			build: clearBuild
		},
	});

	// Register the tasks
	grunt.registerTask('default', ['concat', 'uglify', 'babel', 'cssmin', 'clean:build', 'rev']);
};
