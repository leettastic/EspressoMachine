<?php

namespace Leettastic\EspressoMachine\Container;

interface BeansContainerInterface
{

    /**
     * Adds beans to the container
     *
     * @param integer $numSpoons number of spoons of beans
     *
     * @throws ContainerFullException, EspressoMachineContainerException
     *
     * @return void
     */
    public function addBeans($numSpoons);

    /**
     * Get $numSpoons from the container
     *
     * @param integer $numSpoons number of spoons of beans
     *
     * @return integer
     *
     * @throws EspressoMachineContainerException
     */
    public function useBeans($numSpoons);

    /**
     * Returns the number of spoons of beans left in the container
     *
     * @return integer
     */
    public function getBeans();
}
