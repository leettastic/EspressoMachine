<?php

namespace Leettastic\EspressoMachine;

use Leettastic\EspressoMachine\Container\BeansContainerInterface;
use Leettastic\EspressoMachine\Container\WaterContainerInterface;
use Leettastic\EspressoMachine\Machine\EspressoMachineInterface;
use Leettastic\EspressoMachine\Machine\NoBeansException;
use Leettastic\EspressoMachine\Machine\NoWaterException;
use Leettastic\EspressoMachine\Machine\DescaleNeededException;
use Leettastic\EspressoMachine\Container\ContainerFullException;
use Leettastic\EspressoMachine\Container\EspressoMachineContainerException;


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
class EspressoMachine implements EspressoMachineInterface
{

    private $waterContainer;
    private $beansContainer;
    private $amountOfCoffeeMade = 0.0;
    private $needsDescaling = false;

    public function __construct(WaterContainer $waterContainer, BeansContainer $beansContainer) {
        $this->waterContainer = $waterContainer;
        $this->beansContainer = $beansContainer;
    }

    /**
     * @param double $waterAmount
     * @param integer $beansAmount
     *
     * @throws DescaleNeededException
     * @throws NoBeansException
     * @throws NoWaterException
     *
     * @return double
     */
    private function makeCoffee($waterAmount, $beansAmount) {
        if($this->needsDescaling()) {
            throw new DescaleNeededException();
        }

        if(($this->amountOfCoffeeMade / 5000) < intval(($this->amountOfCoffeeMade + $waterAmount * 1000) / 5000)) {
            $this->needsDescaling = true;
        }
        $this->amountOfCoffeeMade = $this->amountOfCoffeeMade + $waterAmount * 1000;

        if($this->beansContainer->getBeans() - $beansAmount < 0) {
            throw new NoBeansException();
        }

        if($this->waterContainer->getWater() - $waterAmount < 0) {
            throw new NoWaterException();
        }

        $this->waterContainer->useWater($waterAmount);
        $this->beansContainer->useBeans($beansAmount);
        return $this->amountOfCoffeeMade / 1000;
    }

    private function espressosLeft() {
        return min($this->getBeans(), floor($this->getWater() / 0.05));
    }

    public function needsDescaling() {
        return $this->needsDescaling;
    }

    /**
     * Runs the process to descale the machine
     * so the machine can be used make coffee
     * uses 1 litre of water
     *
     * @throws NoWaterException
     *
     * @return void
     */
    public function descale()
    {
        if($this->waterContainer->getWater() - 1 < 0) {
            throw new NoWaterException();
        }
        $this->needsDescaling = false;
        $this->waterContainer->useWater(1);
    }

    /**
     * Runs the process for making Espresso
     *
     * @throws DescaleNeededException When the machine needs descaled and cannot make coffee
     * @throws NoBeansException When there is not enough beans to make the coffee
     * @throws NoWaterException When there is not enough water to make the coffee
     *
     * @return float of litres of coffee made
     */
    public function makeEspresso()
    {
        return $this->makeCoffee(0.05, 1);
    }

    /**
     * @see makeEspresso
     *
     * @throws DescaleNeededException When the machine needs descaled and cannot make coffee
     * @throws NoBeansException When there is not enough beans to make the coffee
     * @throws NoWaterException When there is not enough water to make the coffee
     *
     * @return float of litres of coffee made
     */
    public function makeDoubleEspresso()
    {
        return $this->makeCoffee(0.1, 2);
    }

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
     * Please note you should return "Add water" if the machine needs descaling and doesn't have enough water
     *
     * @return string
     */
    public function getStatus()
    {
        if($this->needsDescaling) {
            if($this->getWater() < 1) {
                return 'Add water';
            }
            return 'Descale needed';
        }

        if($this->getWater() <= 0 && $this->getBeans() <= 0) {
            return 'Add beans and water';
        }

        if($this->getBeans() <= 0) {
            return 'Add beans';
        }

        if($this->getWater() <= 0) {
            return 'Add water';
        }

        return $this->espressosLeft() . " Espressos left";
    }

    /**
     * @param BeansContainerInterface $container
     */
    public function setBeansContainer(BeansContainerInterface $container)
    {
        $this->beansContainer = $container;
    }

    /**
     * @return BeansContainer
     */
    public function getBeansContainer()
    {
        return $this->beansContainer;
    }

    /**
     * @param WaterContainerInterface $container
     */
    public function setWaterContainer(WaterContainerInterface $container)
    {
        $this->waterContainer = $container;
    }

    /**
     * @return WaterContainer
     */
    public function getWaterContainer()
    {
        return $this->waterContainer;
    }

    /**
     * Adds water to the coffee machine's water tank
     *
     * @param float $litres
     *
     * @throws ContainerFullException
     * @throws EspressoMachineContainerException
     *
     * @return void
     */
    public function addWater($litres)
    {
        if($this->waterContainer->get() + $litres > $this->waterContainer->getCapacity()) {
            throw new ContainerFullException;
        }

        try {
            $this->waterContainer->addWater($litres);
        }
        catch(NoWaterException $e) {
            throw new EspressoMachineContainerException();
        }
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
        $this->waterContainer->useWater($litres);
    }

    /**
     * Returns the volume of water left in the container
     *
     * @return float number of litres
     */
    public function getWater()
    {
        return $this->waterContainer->getWater();
    }

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
        $this->beansContainer->addBeans($numSpoons);
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
        $this->beansContainer->useBeans($numSpoons);
    }

    /**
     * Returns the number of spoons of beans left in the container
     *
     * @return integer
     */
    public function getBeans()
    {
        return $this->beansContainer->getBeans();
    }

}
