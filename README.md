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

### Screenshots
<img width="411" alt="start" src="https://user-images.githubusercontent.com/5259532/209481770-69f510e5-ea38-48af-b32a-dfeb34f4cc58.png">
<img width="322" alt="game" src="https://user-images.githubusercontent.com/5259532/209481772-b160badd-c7d8-4096-9c04-097a067f7fbc.png">
<img width="331" alt="win" src="https://user-images.githubusercontent.com/5259532/209481775-7e7808a4-59fe-4b0f-a489-49384a8b63b9.png">
<img width="336" alt="loss" src="https://user-images.githubusercontent.com/5259532/209481776-2c7c91ce-5dd5-415b-9273-c6a55726eed4.png">
