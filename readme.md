# Kompakt Audio Snippets

Audio snippet generator

## Install

+ `git clone https://github.com/kompakt/audio-snippets.git`
+ `cd audio-snippets`
+ `curl -sS https://getcomposer.org/installer | php`
+ `php composer.phar install`

## Tests

+ `cp tests/config.php.dist config.php`
+ Adjust `config.php` as needed
+ `vendor/bin/phpunit`
+ `vendor/bin/phpunit --coverage-html tests/_coverage`

## License

kompakt/audio-snippets is licensed under the MIT license - see the LICENSE file for details