<?php

namespace Cvuorinen\Raspicam;

use AdamBrett\ShellWrapper\Command\Builder as CommandBuilder;
use AdamBrett\ShellWrapper\Command\CommandInterface;
use AdamBrett\ShellWrapper\ExitCodes;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\Runners\ReturnValue;
use AdamBrett\ShellWrapper\Runners\Runner;

/**
 * Abstracts some common functionality related to cli commands
 *
 * @package Cvuorinen\Raspicam
 */
abstract class Raspicam
{
    const EXPOSURE_AUTO = 'auto';
    const EXPOSURE_NIGHT = 'night';
    const EXPOSURE_NIGHTPREVIEW = 'nightpreview';
    const EXPOSURE_BACKLIGHT = 'backlight';
    const EXPOSURE_SPOTLIGHT = 'spotlight';
    const EXPOSURE_SPORTS = 'sports';
    const EXPOSURE_SNOW = 'snow';
    const EXPOSURE_BEACH = 'beach';
    const EXPOSURE_VERYLONG = 'verylong';
    const EXPOSURE_FIXEDFPS = 'fixedfps';
    const EXPOSURE_ANTISHAKE = 'antishake';
    const EXPOSURE_FIREWORKS = 'fireworks';

    /**
     * @var Runner
     */
    protected $commandRunner;

    /**
     * @var string
     */
    protected $lastReturnValue;

    /**
     * Get name of the executable cli command
     *
     * @return string
     */
    abstract protected function getExecutable();

    /**
     * @return CommandBuilder
     */
    protected function getCommandBuilder()
    {
        return new CommandBuilder(
            $this->getExecutable()
        );
    }

    /**
     * @return Runner
     */
    protected function getCommandRunner()
    {
        if (null === $this->commandRunner) {
            $this->commandRunner = new Exec();
        }

        return $this->commandRunner;
    }

    /**
     * Mainly used as dependency injection with unit tests
     *
     * @param Runner $runner
     *
     * @return $this
     */
    public function setCommandRunner(Runner $runner)
    {
        $this->commandRunner = $runner;

        return $this;
    }

    protected function execute(CommandInterface $command)
    {
        $runner = $this->getCommandRunner();

        $this->lastReturnValue = $runner->run($command);

        $this->checkReturnValue($runner);
    }

    /**
     * @param Runner $runner
     *
     * @return bool
     * @throws CommandFailedException
     */
    private function checkReturnValue(Runner $runner)
    {
        if (!($runner instanceof ReturnValue)) {
            return true;
        }

        $exitCode = $runner->getReturnValue();

        if (ExitCodes::SUCCESS == $exitCode) {
            return true;
        }

        throw new CommandFailedException(
            ExitCodes::getDescription($exitCode)
        );
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        $runner = $this->getCommandRunner();

        if ($runner instanceof Exec) {
            return $runner->getOutput();
        }

        return $this->lastReturnValue;
    }
}
