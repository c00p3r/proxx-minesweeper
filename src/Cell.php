<?php

namespace App;

class Cell
{
    private bool $visible = false;
    private bool $mine = false;
    private int $counter = 0;
    private int $row;
    private int $col;

    public function __construct(int $row, int $col)
    {
        $this->row = $row;
        $this->col = $col;
    }

    public function getRow(): int
    {
        return $this->row;
    }

    public function getCol(): int
    {
        return $this->col;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function getVisible(): bool
    {
        return $this->visible;
    }

    public function setMine(bool $mine): void
    {
        $this->mine = $mine;
    }

    public function getMine(): bool
    {
        return $this->mine;
    }

    public function setCounter(int $counter): void
    {
        $this->counter = $counter;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function incrementCounter(): void
    {
        $this->counter++;
    }

    public function displayValue(): string
    {
        if (!$this->visible) {
            return '';
        }

        if ($this->mine) {
            return 'X';
        }

        return (string) $this->counter;
    }
}
