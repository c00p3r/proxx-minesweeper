<?php

namespace Unit;

use App\Cell;
use PHPUnit\Framework\TestCase;

class CellTest extends TestCase
{
    private Cell $cell;

    protected function setUp(): void
    {
        $this->cell = new Cell(1, 1);
    }

    public function testIncrementCounter(): void
    {
        $this->assertEquals(0, $this->cell->getCounter());

        $this->cell->incrementCounter();

        $this->assertEquals(1, $this->cell->getCounter());
    }

    public function testDisplayValue(): void
    {
        $this->assertEquals('', $this->cell->displayValue());

        $this->cell->setVisible(true);

        $this->assertEquals(0, $this->cell->displayValue());

        $this->cell->setMine(true);

        $this->assertEquals('X', $this->cell->displayValue());

        $this->cell->setMine(false);
        $this->cell->setCounter(2);

        $this->assertEquals(2, $this->cell->displayValue());
    }
}
