<?php

namespace Unit;

use App\Board;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class BoardTest extends TestCase
{
    private Board $board;

    protected function setUp(): void
    {
        $this->board = new Board(5, 5);
    }

    protected function tearDown(): void
    {
        unset($this->board);
    }

    public function testRenderBoard(): void
    {
        $output = new BufferedOutput();

        $this->board->render($output);

        $mines = $this->board->getMines();

        $result = $output->fetch();

        $this->assertEquals(
            <<<END
$mines mines on the board
+-----+-----+-----+-----+-----+-----+
|  *  |  a  |  b  |  c  |  d  |  e  |
+-----+-----+-----+-----+-----+-----+
|  1  |     |     |     |     |     |
+-----+-----+-----+-----+-----+-----+
|  2  |     |     |     |     |     |
+-----+-----+-----+-----+-----+-----+
|  3  |     |     |     |     |     |
+-----+-----+-----+-----+-----+-----+
|  4  |     |     |     |     |     |
+-----+-----+-----+-----+-----+-----+
|  5  |     |     |     |     |     |
+-----+-----+-----+-----+-----+-----+
END,
            trim($result));
    }

    public function testRenderRevealedBoard(): void
    {
        $output = new BufferedOutput();

        $this->board->revealBoard();

        $this->board->render($output);

        $result = $output->fetch();

        $this->assertStringContainsString('X', $result);
        $this->assertStringContainsString('0', $result);
    }

    public function testFindCellByCoordinates(): void
    {
        $cell = $this->board->findCellByCoordinates('a', 1);

        $this->assertNotEmpty($cell);

        $cell = $this->board->findCellByCoordinates('x', 1);

        $this->assertEmpty($cell);

        $cell = $this->board->findCellByCoordinates('a', 99);

        $this->assertEmpty($cell);
    }

    public function testFindCellByIndexes(): void
    {
        $cell = $this->board->findCellByIndexes(0, 0);

        $this->assertNotEmpty($cell);

        $cell = $this->board->findCellByIndexes(10, 10);

        $this->assertEmpty($cell);
    }

    public function testRevealCell(): void
    {
        $cell = $this->board->findCellByIndexes(0, 0);

        $this->board->revealCell($cell);

        $this->assertEquals(true, $cell->getVisible());

        $this->assertGreaterThan(0, $this->board->getRevealCounter());
    }

    public function testRevealCellWithMine(): void
    {
        $cell = $this->board->findCellByIndexes(0, 0);

        $cell->setMine(true);

        $this->board->revealCell($cell);

        $this->assertEquals(true, $cell->getVisible());

        $this->assertGreaterThan(0, $this->board->getRevealCounter());

        $this->assertEquals(true, $this->board->getBusted());
    }

    public function testRevealCellWithCellsAround(): void
    {
        $cell = $this->board->findCellByIndexes(0, 0);

        $cell->setCounter(0);

        $this->board->revealCell($cell);

        $this->assertEquals(true, $cell->getVisible());

        $this->assertGreaterThan(1, $this->board->getRevealCounter());
    }

    public function testRevealBoard(): void
    {
        $this->board->revealBoard();

        $cell = $this->board->findCellByIndexes(0, 0);

        $this->assertEquals(true, $cell->getVisible());

        $this->assertEquals(25, $this->board->getRevealCounter());
    }
}
