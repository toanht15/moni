var root = './docroot_static';

module.exports = {
    root: root,
    html: {
        src: root + '/_html/pages/**/*ect',
        partial: root + '/_html/partial/**/*ect',
        dest: root + '/html'
    },
    css: {
        src: root + '/_sass/**/*.scss',
        dest: root + '/css',
        stats: root + '/_stylestats',
        guide: root + '/_styleGuide'
    },
    js: {
        src: root + '/_js/**/*.js',
        dest: root + '/js'
    },
    img: {
        src: root + '/_img/**/*.+(jpg|jpeg|png|gif|svg)',
        dest: root + '/img'
    },
    font: {
        src: root + '/_font/**/*.svg',
        dest: root + '/font',
        css: {
            base: root + '/_font/_objects.font.scss',
            dest: root + '/_sass'
        }
    }
}