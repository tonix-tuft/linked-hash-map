module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        watch: {
            composer_json: {
                files: ["composer.json", "composer.lock"],
                tasks: ["exec:composer_install"],
            },
        },
        exec: {
            composer_install: {
                // For production use:
                //cmd: 'composer install --no-dev --optimize-autoloader',

                // For development use:
                cmd:
                    "composer self-update && composer install --no-dev && composer update --lock",
                //cmd: 'composer install --no-dev && composer update --lock',

                exitCode: [0, 255],
            },
        },
    });

    grunt.loadNpmTasks("grunt-exec");
    grunt.loadNpmTasks("grunt-contrib-watch");
};
