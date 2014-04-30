# Kompakt Audio Snippets

Generate audio snippets from the command line

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

See LICENSE.