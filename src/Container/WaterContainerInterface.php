<?php

namespace Leettastic\EspressoMachine\Container;


interface WaterContainerInterface
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
    public function addWater($litres);

    /**
     * Use $litres from the container
     *
     * @throws EspressoMachineContainerException
     *
     * @param float $litres
     *
     * @return integer
     */
    public function useWater($litres);

    /**
     * Returns the volume of water left in the container
     *
     * @return float number of litres
     */
    public function getWater();
}
