<?php

namespace Leettastic\EspressoMachine;

use Leettastic\EspressoMachine\Container\BeansContainerInterface;
use Leettastic\EspressoMachine\Container\ContainerFullException;
use Leettastic\EspressoMachine\Container\EspressoMachineContainerException;


class BeansContainer extends Container implements BeansContainerInterface
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
    public function addBeans($numSpoons)
    {
        $this->add($numSpoons);
    }

    /**
     * Get $numSpoons from the container
     *
     * @param integer $numSpoons number of spoons of beans
     *
     * @return integer
     *
     * @throws EspressoMachineContainerException
     */
    public function useBeans($numSpoons)
    {
        $this->need($numSpoons);
    }

    /**
     * Returns the number of spoons of beans left in the container
     *
     * @return integer
     */
    public function getBeans()
    {
        return $this->get();
    }

}
