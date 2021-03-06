<?php

namespace Benchmark;

use Athletic\AthleticEvent;
use Benchmark\Fixture\Foo;
use Closure;
use ReflectionProperty;

class WriteManyPropertiesEvent extends AthleticEvent
{
    private $object;
    private $propertyName;
    /**
     * @var ReflectionProperty
     */
    private $reflectionProperty;
    private $closure;

    public function setUp()
    {
        $this->object = new Foo('test');
        $this->propertyName = 'prop';

        $this->reflectionProperty = new ReflectionProperty(get_class($this->object), $this->propertyName);
        $this->reflectionProperty->setAccessible(true);

        $this->closure = function($prop, $value) {
            $this->{$prop} = $value;
        };
    }

    /**
     * @iterations 10000
     */
    public function reflection()
    {
        $this->reflectionProperty->setValue($this->object, 'test2');
    }

    /**
     * @iterations 10000
     */
    public function closure()
    {
        if (!method_exists('Closure', 'bindTo')) {
            throw new \Exception("works on PHP 5.4");
        }
        $closure = Closure::bind($this->closure, $this->object, $this->object);
        $closure($this->propertyName, 'test2');
    }
}
