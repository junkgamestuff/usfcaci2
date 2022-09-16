/* global require */
var gulp = require("gulp");
var shell = require("gulp-shell");

var d8paths = {
  source: {
    img: [
      "pattern_lab/source/images/**",
    ],
    css: [
      "pattern_lab/source/css/**",
      "!pattern_lab/source/css/pattern-scaffolding.css"
    ],
    js: ["pattern_lab/source/js/**/*.js", "!pattern_lab/source/js/DP.js"]
  },
  dest: {
    img: "images/",
    css: "style/css/",
    js: "js/"
  }
};

gulp.task("copy_images", function copyImg() {
  "use strict";
  return gulp.src(d8paths.source.img).pipe(gulp.dest(d8paths.dest.img));
});

gulp.task("copy_css", function copyCss() {
  "use strict";
  return gulp.src(d8paths.source.css).pipe(gulp.dest(d8paths.dest.css));
});

gulp.task("copy_js", function copyJs() {
  "use strict";
  return gulp.src(d8paths.source.js).pipe(gulp.dest(d8paths.dest.js));
});

gulp.task("pl", shell.task(["cd pattern_lab && npm ci"]));

gulp.task(
  "build",
  gulp.series(gulp.parallel("pl")),
  async function build() {
    "use strict";

    console.log("Built pattern lab assets.");
  }
);
