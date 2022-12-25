# Proxx (minesweeper console game)

### Requirements

- [php 8.2](https://www.php.net/releases/8.2/en.php)
- [composer](https://getcomposer.org)

### Set up

- run `composer install --no-dev`
- run in terminal `php console proxx`
- enjoy!

### Testing

- run `composer install --dev` to install [PHPUnit](https://phpunit.de/) package
- run `sh test` (you can edit that shell script to debug or generate coverage report)


### Notes
The game powered by [Symfony Console](https://symfony.com/doc/current/components/console.html).

The board size can be configured choosing 'no' to quick game question.

Difficulty is fixed (0.15 size/mines ratio).

I used 'excel' style coordinates: letter for columns, number for row.

I used 'while' for seamless experience: the game does not crash on invalid input, and it continues up to win or loss
