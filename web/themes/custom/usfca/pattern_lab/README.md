# Metamorphic Pattern Lab

## Prerequisites

* PHP 7.1+: https://developerjack.com/blog/2016/installing-php71-with-homebrew/
* Composer: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
* NodeJS v10.16.0: use nodenv for this: https://github.com/nodenv/nodenv

## Install

* Execute `cd docroot/themes/custom/usfca/pattern_lab/`
* Execute `npm install`
* Execute `composer install`

## VSCODE Installation

* Go to extensions and install `Sass Lint`
* After installation, go to settings `Command + ,`
* Type `sass lint`
* Check box under `Sasslint: Resolve Paths Relative to Config`

## Development

* Execute `cd docroot/themes/custom/usfca/pattern_lab/`
* Execute `gulp serve`
* Visit http://localhost:8080 in browser.

## Heroku Deploy (on develop branch)

* Make sure you are logged in to Heroku with `heroku login`
* Make sure the heroku remote is present `git remote -v`, otherwise execute `heroku git:remote -a usfca-pattern-lab` from the repo root
* Execute `git subtree push --prefix docroot/themes/custom/usfca/pattern_lab heroku master`
