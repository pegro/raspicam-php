<?php

namespace Cvuorinen\Raspicam;

use AdamBrett\ShellWrapper\Command\CommandInterface;

/**
 * Class that abstracts the usage of raspistill cli utility that is used to take photos with the
 * Raspberry Pi camera module.
 *
 * @package Cvuorinen\Raspicam
 */
class Raspistill extends Raspicam
{
    const COMMAND = 'raspistill';

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $verticalFlip;

    /**
     * @var bool
     */
    protected $horizontalFlip;

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function verticalFlip($value = true)
    {
        $this->verticalFlip = $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function horizontalFlip($value = true)
    {
        $this->horizontalFlip = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutable()
    {
        return self::COMMAND;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function takePicture($filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        $this->filename = $filename;

        return $this->execute(
            $this->buildCommand()
        );
    }

    /**
     * @return CommandInterface
     */
    private function buildCommand()
    {
        $command = $this->getCommandBuilder();

        if ($this->filename) {
            $command->addArgument('output', $this->filename);
        }

        if ($this->verticalFlip) {
            $command->addArgument('vflip');
        }

        if ($this->horizontalFlip) {
            $command->addArgument('hflip');
        }

        return $command;
    }
}
