<?php

namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StartCommand extends Command
{
    public static int $minSize = 3;
    public static int $maxSize = 25;
    private SymfonyStyle $io;
    private int $height = 5;
    private int $width = 5;

    protected function configure(): void
    {
        $this->setName('proxx');
        $this->setDescription('Start Proxx game');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->greeting();

        $this->startGame();

        $board = new Board($this->height, $this->width);

        $board->render($output);

        // using 'while' for seamless experience (game does not break on invalid input)
        // so the game continues up to the win or loss
        while (true) {
            $cell = $this->io->ask("Make your move", null, function ($input) use ($board) {
                // awaiting Excel style cell coordinates
                // where letters represent columns and numbers represent rows
                $colAndRow = explode(',', $input);

                if (count($colAndRow) !== 2) {
                    $this->io->error('Invalid input: ' . $input);

                    return null;
                }

                $cell = $board->findCellByCoordinates($colAndRow[0], $colAndRow[1]);

                if (!isset($cell)) {
                    $this->io->error('Invalid input: ' . $input);

                    return null;
                }

                return $cell;
            });

            // make the move
            if (isset($cell)) {
                $board->revealCell($cell);

                if ($board->getBusted()) {
                    $board->revealBoard();

                    $board->render($output);

                    $this->io->error('Busted!');

                    return self::SUCCESS;
                }

                if ($board->allMinesFound()) {
                    $board->revealBoard();

                    $board->render($output);

                    $this->io->success('You win! Congratulations!');

                    return self::SUCCESS;
                }

                $board->render($output);
            }
        }
    }

    private function greeting(): void
    {
        $this->io->title('Want to play a game?..');
        $this->io->writeln('This is classic minesweeper game.');
        $this->io->writeln("Make a move by inputting cell coordinates (column and row) that you want to open \ne.g. b,7");
    }

    private function startGame(): void
    {
        $question = sprintf(
            "Start quick %dx%d game? [yes/no] \n (choose 'no' to customize)",
            $this->width,
            $this->height
        );

        $startGame = null;

        // using 'while' for seamless experience (game does not break on invalid input)
        while ($startGame === null) {
            $startGame = $this->io->ask($question, 'yes', function ($input) {
                $positive = ['yes', 'YES', 'y', 'Y'];
                $negative = ['no', 'NO', 'n', 'N'];

                if (!in_array($input, array_merge($positive, $negative), true)) {
                    $this->io->error('Invalid input: ' . $input);

                    return null;
                }

                if (in_array($input, $negative, true)) {
                    return false;
                }

                return true;
            });
        }

        if ($startGame) {
            // start quick game
            return;
        }

        // set custom board dimensions otherwise
        $question = sprintf(
            "Input the size of the board: [width,height] \n (min: %d,%d max: %d,%d)",
            self::$minSize,
            self::$minSize,
            self::$maxSize,
            self::$maxSize,
        );

        $height = null;
        $width = null;

        // using 'while' for seamless experience (game does not break on invalid input)
        while (!isset($height) && !isset($width)) {
            [$height, $width] = $this->io->ask($question, "$this->width,$this->height", function ($input) {
                $colAndRow = explode(',', $input);

                if (count($colAndRow) !== 2) {
                    $this->io->error('Invalid input: ' . $input);

                    return [null, null];
                }

                $width = (int) $colAndRow[0];
                $height = (int) $colAndRow[1];

                if ($width < self::$minSize || $width > self::$maxSize || $height < self::$minSize || $height > self::$maxSize) {
                    $this->io->error('Invalid input: ' . $input);

                    return [null, null];
                }

                return [$height, $width];
            });
        }

        $this->height = $height;
        $this->width = $width;
    }
}
