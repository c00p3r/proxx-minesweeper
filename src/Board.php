<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableCellStyle;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Output\OutputInterface;

class Board
{
    private int $height;
    private int $width;
    private array $board = [];
    private array $columnHeaders;
    private array $rowHeaders;
    private float $difficultyRate = 0.15;
    private int $mines;
    private int $revealCounter = 0;
    private bool $busted = false;

    public function __construct(int $height, int $width)
    {
        $this->height = $height;
        $this->width = $width;

        $this->generateBoard();
        $this->addMines();
    }

    public function getMines(): int
    {
        return $this->mines;
    }

    public function getRevealCounter(): int
    {
        return $this->revealCounter;
    }

    public function getBusted(): bool
    {
        return $this->busted;
    }

    public function render(OutputInterface $output): void
    {
        $table = new Table($output);

        // empty top left corner
        $headerRow = [$this->renderHeaderCell('*')];

        // header letters
        foreach ($this->columnHeaders as $char) {
            $headerRow[] = $this->renderHeaderCell($char);
        }

        $table->addRow($headerRow);

        $table->addRow(new TableSeparator);

        foreach ($this->board as $rowIndex => $cells) {

            // row headers column
            $displayRow = [$this->renderHeaderCell((string) ($this->rowHeaders[$rowIndex]))];

            foreach ($cells as $cell) {
                $displayRow[] = $this->renderCell($cell);
            }

            $table->addRow($displayRow);

            if ($rowIndex + 1 === $this->height) {
                // no need for row separator at the end
                break;
            }

            $table->addRow(new TableSeparator);
        }

        // add extra horizontal padding for better look
        $table->setColumnWidths(array_fill(0, $this->width + 1, 3));

        // center table values more or less
        $tableStyle = (new TableStyle)->setPadType(STR_PAD_BOTH);

        $table->setStyle($tableStyle);

        $output->writeln("<info>$this->mines mines on the board</info>");

        $table->render();
    }

    public function findCellByCoordinates(string $col, string $row): ?Cell
    {
        $colIndex = array_search($col, $this->columnHeaders, true);

        if ($colIndex === false) {
            return null;
        }

        $rowIndex = array_search($row, $this->rowHeaders, true);

        if ($rowIndex === false) {
            return null;
        }

        return $this->board[$rowIndex][$colIndex];
    }

    public function findCellByIndexes(int $col, int $row): ?Cell
    {
        return $this->board[$row][$col] ?? null;
    }

    public function revealCell(Cell $cell): void
    {
        if (!$cell->getVisible()) {
            $this->incrementRevealedCounter();
        }

        $cell->setVisible(true);

        if ($cell->getMine()) {
            $this->busted = true;
        }

        // if there is no mines around - automatically reveal adjacent cells
        if ($cell->getCounter() === 0) {
            $this->revealCellsAround($cell);
        }
    }

    public function revealBoard(): void
    {
        foreach ($this->board as $row) {
            foreach ($row as $cell) {
                $this->revealCell($cell);
            }
        }
    }

    private function generateBoard(): void
    {
        $this->rowHeaders = array_map('strval', range(1, $this->height));

        $this->columnHeaders = array_slice(range('a', 'z'), 0, $this->width);

        for ($row = 0; $row < $this->height; $row++) {
            for ($col = 0; $col < $this->width; $col++) {
                $this->board[$row][$col] = new Cell($row, $col);
            }
        }
    }

    private function addMines(): void
    {
        $this->mines = (int) round($this->height * $this->width * $this->difficultyRate);

        for ($i = 0; $i < $this->mines; $i++) {
            do {
                $row = random_int(0, $this->height - 1);
                $col = random_int(0, $this->width - 1);
                /** @var Cell $cell */
                $cell = $this->board[$row][$col];
            } while ($cell->getMine());

            $cell->setMine(true);

            $cellsAround = $this->getCellsAround($cell);

            /** @var Cell $cell */
            foreach ($cellsAround as $cell) {
                $cell->incrementCounter();
            }
        }
    }

    private function getCellsAround(Cell $cell): array
    {
        $cells = [];
        for ($row = $cell->getRow() - 1; $row <= $cell->getRow() + 1; $row++) {
            for ($col = $cell->getCol() - 1; $col <= $cell->getCol() + 1; $col++) {
                if ($row === $cell->getRow() && $col === $cell->getCol()) {
                    // skip self
                    continue;
                }
                if (isset($this->board[$row][$col])) {
                    $cells[] = $this->board[$row][$col];
                }
            }
        }

        return $cells;
    }

    private function renderHeaderCell(string $value): TableCell
    {
        return new TableCell($value);
    }

    private function renderCell(Cell $cell): TableCell
    {
        if (!$cell->getVisible()) {
            $style = [
                'fg' => 'yellow',
                'bg' => 'white'
            ];
        } elseif ($cell->getMine()) {
            $style = ['fg' => 'red'];
        } elseif ($cell->getCounter() > 0) {
            $style = ['fg' => 'blue'];
        } else {
            $style = ['fg' => 'green'];
        }

        return new TableCell(
            $cell->displayValue(),
            ['style' => new TableCellStyle($style)]
        );
    }

    private function revealCellsAround(Cell $cell): void
    {
        $cellsAround = $this->getCellsAround($cell);

        /** @var Cell $adjacentCell */
        foreach ($cellsAround as $adjacentCell) {
            if (!$adjacentCell->getMine() && !$adjacentCell->getVisible()) {
                $this->revealCell($adjacentCell);
            }
        }
    }

    private function incrementRevealedCounter(): void
    {
        $this->revealCounter++;
    }

    public function allMinesFound(): bool
    {
        return $this->height * $this->width - $this->mines - $this->revealCounter === 0;
    }
}
