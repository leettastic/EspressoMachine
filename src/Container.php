<?php

namespace Leettastic\EspressoMachine;

use Leettastic\EspressoMachine\Container\ContainerFullException;
use Leettastic\EspressoMachine\Container\EspressoMachineContainerException;

class Container
{

    protected $amount;
    protected $capacity;

    public function __construct($capacity) {
        $this->capacity = $capacity;
        $this->amount = 0;
    }

    public function add($amount) {
        if($this->amount + $amount > $this->capacity) {
            throw new ContainerFullException();
        }
        $this->amount+=$amount;
    }

    public function need($amount) {
        if($this->amount - $amount < 0) {
            throw new EspressoMachineContainerException();
        }

        $this->amount -= $amount;
    }

    public function get() {
        return $this->amount;
    }

    public function getCapacity() {
        return $this->capacity;
    }
}