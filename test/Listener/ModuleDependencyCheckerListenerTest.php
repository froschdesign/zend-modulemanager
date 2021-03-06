<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ModuleManager\Listener;

use PHPUnit\Framework\TestCase as TestCase;
use stdClass;
use Zend\ModuleManager\Exception;
use Zend\ModuleManager\Feature;
use Zend\ModuleManager\Listener\ModuleDependencyCheckerListener;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;

/**
 * @covers Zend\ModuleManager\Listener\ModuleDependencyCheckerListener
 */
class ModuleDependencyCheckerListenerTest extends TestCase
{
    /**
     * @covers \Zend\ModuleManager\Listener\ModuleDependencyCheckerListener::__invoke
     */
    public function testCallsGetModuleDependenciesOnModuleImplementingInterface()
    {
        //$moduleManager = new ModuleManager(array());
        /*$moduleManager->getEventManager()->attach(
            ModuleEvent::EVENT_LOAD_MODULE,
            new ModuleDependencyCheckerListener(),
            2000
        ); */

        $module = $this->getMockBuilder(Feature\DependencyIndicatorInterface::class)->getMock();
        $module->expects($this->once())->method('getModuleDependencies')->will($this->returnValue([]));

        $event = $this->getMockBuilder(ModuleEvent::class)->getMock();
        $event->expects($this->any())->method('getModule')->will($this->returnValue($module));

        $listener = new ModuleDependencyCheckerListener();
        $listener->__invoke($event);
    }

    /**
     * @covers \Zend\ModuleManager\Listener\ModuleDependencyCheckerListener::__invoke
     */
    public function testCallsGetModuleDependenciesOnModuleNotImplementingInterface()
    {
        $module = $this->getMockBuilder(stdClass::class)->setMethods(['getModuleDependencies'])->getMock();
        $module->expects($this->once())->method('getModuleDependencies')->will($this->returnValue([]));

        $event = $this->getMockBuilder(ModuleEvent::class)->getMock();
        $event->expects($this->any())->method('getModule')->will($this->returnValue($module));

        $listener = new ModuleDependencyCheckerListener();
        $listener->__invoke($event);
    }

    /**
     * @covers \Zend\ModuleManager\Listener\ModuleDependencyCheckerListener::__invoke
     */
    public function testNotFulfilledDependencyThrowsException()
    {
        $module = $this->getMockBuilder(stdClass::class)->setMethods(['getModuleDependencies'])->getMock();
        $module->expects($this->once())->method('getModuleDependencies')->will($this->returnValue(['OtherModule']));

        $event = $this->getMockBuilder(ModuleEvent::class)->getMock();
        $event->expects($this->any())->method('getModule')->will($this->returnValue($module));

        $listener = new ModuleDependencyCheckerListener();
        $this->expectException(Exception\MissingDependencyModuleException::class);
        $listener->__invoke($event);
    }
}
