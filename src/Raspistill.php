<?php

namespace Cvuorinen\Raspicam;

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

        $command = $this->getCommandBuilder();

        $command->addArgument('output', $filename);

        return $this->execute($command);
    }
}
