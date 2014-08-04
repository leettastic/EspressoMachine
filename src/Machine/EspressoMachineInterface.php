<?php

namespace Leettastic\EspressoMachine\Machine;

use Leettastic\EspressoMachine\Container\BeansContainerInterface;
use Leettastic\EspressoMachine\Container\WaterContainerInterface;

/**
 * A single espresso uses 1 spoon of beans and 0.05 litres of water
 * A double espresso uses 2 spoons of beans and 0.10 litres of water
 *
 * The machine MUST be descaled after every 5 litres of Espresso made
 *    Descaling uses 1 litres of water
 *    You CANNOT make coffee while the machine needs descaling
 *    The machine will start with no beans or water in its containers
 *
 */
interface EspressoMachineInterface extends BeansContainerInterface, WaterContainerInterface
{

    /**
     * Runs the process to descale the machine
     * so the machine can be used make coffee
     * uses 1 litre of water
     *
     * @throws NoWaterException
     *
     * @return void
     */
    public function descale();

    /**
     * Runs the process for making Espresso
     *
     * @throws DescaleNeededException When the machine needs descaled and cannot make coffee
     * @throws NoBeansException When there is not enough beans to make the coffee
     * @throws NoWaterException When there is not enough water to make the coffee
     *
     * @return float of litres of coffee made
     */
    public function makeEspresso();

    /**
     * @see makeEspresso
     *
     * @throws DescaleNeededException When the machine needs descaled and cannot make coffee
     * @throws NoBeansException When there is not enough beans to make the coffee
     * @throws NoWaterException When there is not enough water to make the coffee
     *
     * @return float of litres of coffee made
     */
    public function makeDoubleEspresso();

    /**
     * This method controls what is displayed on the screen of the machine
     * Returns ONE of the following human readable statuses in the following preference order:
     *
     * Descale needed
     * Add beans and water
     * Add beans
     * Add water
     * {Integer} Espressos left
     *
     * Please note you should return "Add water" if the machine needs descaling and does not have enough water
     *
     * @return string
     */
    public function getStatus();

    /**
     * @param BeansContainerInterface $container
     * @return void
     */
    public function setBeansContainer(BeansContainerInterface $container);

    /**
     * @return BeansContainerInterface
     */
    public function getBeansContainer();

    /**
     * @param WaterContainerInterface $container
     * @return void
     */
    public function setWaterContainer(WaterContainerInterface $container);

    /**
     * @return WaterContainerInterface
     */
    public function getWaterContainer();
}
