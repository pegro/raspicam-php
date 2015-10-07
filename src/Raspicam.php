<?php

namespace Cvuorinen\Raspicam;

use AdamBrett\ShellWrapper\Command\Builder as CommandBuilder;
use AdamBrett\ShellWrapper\Command\CommandInterface;
use AdamBrett\ShellWrapper\ExitCodes;
use AdamBrett\ShellWrapper\Runners\Exec;
use AdamBrett\ShellWrapper\Runners\ReturnValue;
use AdamBrett\ShellWrapper\Runners\Runner;

/**
 * Abstracts some common functionality related to al of the camera commands
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

    const WHITE_BALANCE_OFF = 'off';
    const WHITE_BALANCE_AUTO = 'auto';
    const WHITE_BALANCE_SUN = 'sun';
    const WHITE_BALANCE_CLOUD = 'cloud';
    const WHITE_BALANCE_SHADE = 'shade';
    const WHITE_BALANCE_TUNGSTEN = 'tungsten';
    const WHITE_BALANCE_FLUORESCENT = 'fluorescent';
    const WHITE_BALANCE_INCANDESCENT = 'incandescent';
    const WHITE_BALANCE_FLASH = 'flash';
    const WHITE_BALANCE_HORIZON = 'horizon';

    const EFFECT_NONE = 'none';
    const EFFECT_NEGATIVE = 'negative';
    const EFFECT_SOLARISE = 'solarise';
    const EFFECT_POSTERISE = 'posterise';
    const EFFECT_WHITEBOARD = 'whiteboard';
    const EFFECT_BLACKBOARD = 'blackboard';
    const EFFECT_SKETCH = 'sketch';
    const EFFECT_DENOISE = 'denoise';
    const EFFECT_EMBOSS = 'emboss';
    const EFFECT_OILPAINT = 'oilpaint';
    const EFFECT_HATCH = 'hatch';
    const EFFECT_GPEN = 'gpen';
    const EFFECT_PASTEL = 'pastel';
    const EFFECT_WATERCOLOUR = 'watercolour';
    const EFFECT_FILM = 'film';
    const EFFECT_BLUR = 'blur';
    const EFFECT_SATURATION = 'saturation';
    const EFFECT_COLOURSWAP = 'colourswap';
    const EFFECT_WASHEDOUT = 'washedout';
    const EFFECT_COLOURPOINT = 'colourpoint';
    const EFFECT_COLOURBALANCE = 'colourbalance';
    const EFFECT_CARTOON = 'cartoon';

    const METERING_AVERAGE = 'average';
    const METERING_SPOT = 'spot';
    const METERING_BACKLIT = 'backlit';
    const METERING_MATRIX = 'matrix';

    const TIMEUNIT_SECOND = 's';
    const TIMEUNIT_MILLISECOND = 'ms';
    const TIMEUNIT_MICROSECOND = 'us';

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

    /**
     * @param CommandInterface $command
     *
     * @throws CommandFailedException
     */
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
