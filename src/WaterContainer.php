<?php

namespace Leettastic\EspressoMachine;

use Leettastic\EspressoMachine\Container\WaterContainerInterface;
use Leettastic\EspressoMachine\Container\ContainerFullException;
use Leettastic\EspressoMachine\Container\EspressoMachineContainerException;


class WaterContainer extends Container implements WaterContainerInterface
{

    /**
     * Adds water to the coffee machine's water tank
     *
     * @param float $litres
     *
     * @throws ContainerFullException, EspressoMachineContainerException
     *
     * @return void
     */
    public function addWater($litres)
    {
        $this->add($litres);
    }

    /**
     * Use $litres from the container
     *
     * @throws EspressoMachineContainerException
     *
     * @param float $litres
     *
     * @return integer
     */
    public function useWater($litres)
    {
        $this->need($litres);
    }

    /**
     * Returns the volume of water left in the container
     *
     * @return float number of litres
     */
    public function getWater()
    {
        return $this->get();
    }
}