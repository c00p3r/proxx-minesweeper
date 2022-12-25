<?php

namespace Feature;

use App\StartCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTest extends TestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $application = new Application('Testing Proxx game');
        $application->add(new StartCommand);
        $command = $application->find('proxx');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * Testing quick game
     */
    public function testExecute(): void
    {
        // adding inputs for all the cells
        // so the game ends with whatever result
        $inputs = ['yes'];
        $rowHeaders = range(1, 5);
        $columnHeaders = range('a', 'e');

        foreach ($columnHeaders as $col) {
            foreach ($rowHeaders as $row) {
                $inputs[] = "$col,$row";
            }
        }

        $this->commandTester->setInputs($inputs);

        $this->commandTester->execute([]);

        $this->assertMatchesRegularExpression('/Busted|Congratulations/', $this->commandTester->getDisplay());
    }
}
