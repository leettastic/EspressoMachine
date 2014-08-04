<?php

namespace Leettastic\EspressoMachine\Tests;

use Leettastic\EspressoMachine\EspressoMachine;
use Leettastic\EspressoMachine\WaterContainer;
use Leettastic\EspressoMachine\BeansContainer;
use Leettastic\EspressoMachine\Machine\NoBeansException;
use Leettastic\EspressoMachine\Machine\NoWaterException;
use Leettastic\EspressoMachine\Machine\DescaleNeededException;
use Leettastic\EspressoMachine\Container\ContainerFullException;
use Leettastic\EspressoMachine\Container\EspressoMachineContainerException;

class EspressoMachineTest extends \PHPUnit_Framework_TestCase
{

    protected $espresso;

    public function setUp() {
        $waterContainer = new WaterContainer(200);
        $beansContainer = new BeansContainer(10);

        $this->espresso = new EspressoMachine($waterContainer, $beansContainer);
    }

    public function emptyContainers() {
        $this->espresso->useWater($this->espresso->getWater());
        $this->espresso->useBeans($this->espresso->getBeans());
    }

    public function testMakingEspresso() {
        $this->emptyContainers();
        $this->espresso->addBeans(10);
        $this->espresso->addWater(10);

        $this->assertEquals(0.05, $this->espresso->makeEspresso());
    }

    public function testMakingDoubleEspresso() {
        $this->emptyContainers();
        $this->espresso->addBeans(10);
        $this->espresso->addWater(10);

        $this->assertEquals(0.10, $this->espresso->makeDoubleEspresso());
    }

    public function testUsingWater() {
        $this->emptyContainers();
        $this->espresso->addWater(10);
        $this->espresso->useWater(4.5);
        $this->assertEquals(5.5, $this->espresso->getWater());
    }

    public function testAddingWater() {
        $this->emptyContainers();
        $this->espresso->addWater(7.5);
        $this->assertEquals(7.5, $this->espresso->getWater());
    }

    public function testAddingBeans() {
        $this->emptyContainers();
        $this->espresso->addBeans(5);
        $this->assertEquals(5, $this->espresso->getBeans());
    }

    public function testUsingBeans() {
        $this->emptyContainers();
        $this->espresso->addBeans(5);
        $this->espresso->useBeans(2);
        $this->assertEquals(3, $this->espresso->getBeans());
    }

    public function testEspressoMachineContainerExceptionWhenAddingWater() {
        $this->emptyContainers();
        $this->espresso->addWater(200);
        try {
            $this->espresso->addWater(3);
        }
        catch (EspressoMachineContainerException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testDescaling() {
        $this->emptyContainers();
        $this->espresso->addWater(20);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            $this->espresso->makeDoubleEspresso();
            $this->espresso->addBeans(2);
        }
        $this->assertEquals(true, $this->espresso->needsDescaling());
        $this->espresso->descale();
        $this->assertEquals(false, $this->espresso->needsDescaling());
    }

    public function testDescalingUses1LitreOfWater() {
        $this->emptyContainers();
        $this->espresso->addWater(10);
        $this->espresso->addBeans(10);
        $this->espresso->descale();
        $this->assertEquals(9, $this->espresso->getWater());
    }

    public function testGetStatusDescaleNeededWhenEnoughWater() {
        $this->emptyContainers();
        $this->espresso->addWater(10);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            $this->espresso->makeDoubleEspresso();
            $this->espresso->addBeans(2);
        }
        $this->assertEquals('Descale needed', $this->espresso->getStatus());
    }

    public function testGetStatusDescaleNeededWhenNotEnoughWater() {
        $this->emptyContainers();
        $this->espresso->addWater(5);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            $this->espresso->makeDoubleEspresso();
            $this->espresso->addBeans(2);
        }
        $this->assertEquals('Add water',$this->espresso->getStatus());
    }

    public function testGetStatusAddWaterWhenNoWater() {
        $this->emptyContainers();
        $this->espresso->addBeans(10);
        $this->assertEquals('Add water', $this->espresso->getStatus());
    }

    public function testGetStatusAddWaterAndBeansWhenNoWaterAndBeans() {
        $this->emptyContainers();
        $this->assertEquals('Add beans and water', $this->espresso->getStatus());
    }

    public function testGetStatusAddBeansWhenNoBeans() {
        $this->emptyContainers();
        $this->espresso->addWater(1);
        $this->assertEquals('Add beans', $this->espresso->getStatus());
    }

    public function testGetStatusWhenOneLiterOfWaterAndTwoBeans() {
        $this->emptyContainers();
        $this->espresso->addWater(1);
        $this->espresso->addBeans(2);
        $this->assertEquals('2 Espressos left', $this->espresso->getStatus());
    }

    public function testMakingCoffeeWhileNeedsDescaling() {
        $this->emptyContainers();
        $this->espresso->addWater(5.5);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            $this->espresso->makeDoubleEspresso();
            $this->espresso->addBeans(2);
        }
        try {
            $this->espresso->makeEspresso();
        }
        catch (DescaleNeededException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testDescaleNeededException() {
        $this->emptyContainers();
        $this->espresso->addWater(10);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            $this->espresso->makeDoubleEspresso();
            $this->espresso->addBeans(2);
        }
        try {
            $this->espresso->makeDoubleEspresso();
        }
        catch (DescaleNeededException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testNoBeansException() {
        $this->emptyContainers();
        $this->espresso->addWater(10);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            try {
                $this->espresso->makeDoubleEspresso();
            }
            catch (NoBeansException $expected) {
                return;
            }
        }

        $this->fail('An expected exception has not been raised.');
    }

    public function testNoWaterException() {
        $this->emptyContainers();
        $this->espresso->addWater(1);
        $this->espresso->addBeans(10);
        for($i = 0; $i < 50; $i++) {
            try {
                $this->espresso->makeDoubleEspresso();
                $this->espresso->addBeans(2);
            }
            catch (NoWaterException $expected) {
                return;
            }
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testUseBeansEspressoMachineContainerException() {
        $this->emptyContainers();
        $this->espresso->addWater(1);
        $this->espresso->addBeans(10);
        try {
            $this->espresso->useBeans(13);
        }
        catch (EspressoMachineContainerException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testAddBeansEspressoMachineContainerException() {
        $this->emptyContainers();
        $this->espresso->addBeans(10);
        try {
            $this->espresso->useBeans(13);
        }
        catch (EspressoMachineContainerException $expected) {
            return;
        }
    }

    public function testDescalingNoWaterException() {
        $this->emptyContainers();
        $this->espresso->addBeans(10);
        try {
            $this->espresso->descale();
        }
        catch (NoWaterException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

    public function testContainerFullExceptionWhenAddingBeans() {
        $this->emptyContainers();
        $this->espresso->addBeans(10);
        try {
            $this->espresso->addBeans(3);
        }
        catch (ContainerFullException $expected) {
            return;
        }
        $this->fail('An expected exception has not been raised.');
    }

}