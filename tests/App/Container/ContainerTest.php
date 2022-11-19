<?php

namespace Smolblog\App\Container;

use PHPUnit\Framework\TestCase;

final class ContainerTestStandaloneClass {
	private $testString;
	public function getTestString() { return $this->testString; }
	public function setTestString($value) { $this->testString = $value; }
}

final class ContainerTestComposedClass {
	private $testString;
	public function __construct(private ContainerTestStandaloneClass $internalInstance) {}
	public function getTestString() { return $this->testString; }
	public function setTestString($value) { $this->testString = $value; }
	public function getInternalClass() { return $this->internalInstance; }
}

interface ContainerTestInterface {
	public function getTestString();
}

final class ContainerTestInterfaceImplemented implements ContainerTestInterface {
	public function getTestString() { return 'camelot'; }
}

final class ContainerTest extends TestCase {
	public function testAClassCanBeAddedAndRetrieved() {
		$container = new Container();
		$container->add(ContainerTestStandaloneClass::class);
		$this->assertTrue($container->has(ContainerTestStandaloneClass::class));
		$this->assertInstanceOf(
			ContainerTestStandaloneClass::class,
			$container->get(ContainerTestStandaloneClass::class)
		);
	}

	public function testASharedInstanceCanBeAddedAndRetrieved() {
		$container = new Container();
		$container->addShared(ContainerTestStandaloneClass::class);
		$this->assertTrue($container->has(ContainerTestStandaloneClass::class));

		$retrievedFirst = $container->get(ContainerTestStandaloneClass::class);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrievedFirst);
		$retrievedFirst->setTestString(uniqid());

		$retrievedSecond = $container->get(ContainerTestStandaloneClass::class);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrievedSecond);
		$this->assertEquals($retrievedFirst->getTestString(), $retrievedSecond->getTestString());
	}

	public function testADependencyCanBeAddedToAClass() {
		$container = new Container();
		$container->add(ContainerTestStandaloneClass::class);
		$container->add(ContainerTestComposedClass::class)->addArgument(ContainerTestStandaloneClass::class);

		$retrieved = $container->get(ContainerTestComposedClass::class);
		$this->assertInstanceOf(ContainerTestComposedClass::class, $retrieved);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved->getInternalClass());
	}

	public function testDependenciesCanBeAddedBeforeBeingDeclared() {
		$container = new Container();
		$container->add(ContainerTestComposedClass::class)->addArgument(ContainerTestStandaloneClass::class);
		$container->add(ContainerTestStandaloneClass::class);

		$retrieved = $container->get(ContainerTestComposedClass::class);
		$this->assertInstanceOf(ContainerTestComposedClass::class, $retrieved);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved->getInternalClass());
	}

	public function testDependenciesCanBeAddedLater() {
		$container = new Container();
		$container->add(ContainerTestComposedClass::class);
		$container->add(ContainerTestStandaloneClass::class);
		$container->extend(ContainerTestComposedClass::class)->addArgument(ContainerTestStandaloneClass::class);

		$retrieved = $container->get(ContainerTestComposedClass::class);
		$this->assertInstanceOf(ContainerTestComposedClass::class, $retrieved);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved->getInternalClass());
	}

	public function testAClassCanBeInstantiatedViaFactory() {
		$container = new Container();
		$container->add(ContainerTestStandaloneClass::class, fn() => new ContainerTestStandaloneClass());

		$retrieved_first = $container->get(ContainerTestStandaloneClass::class);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved_first);
		$retrieved_second = $container->get(ContainerTestStandaloneClass::class);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved_second);

		$retrieved_first->setTestString('one');
		$retrieved_second->setTestString('two');
		$this->assertNotEquals($retrieved_first->getTestString(), $retrieved_second->getTestString());
	}

	public function testASharedInstanceCanBePassedViaFactory() {
		$instance = new ContainerTestStandaloneClass();
		$instance->setTestString(uniqid());

		$container = new Container();
		$container->addShared(ContainerTestStandaloneClass::class, fn() => $instance);

		$retrieved = $container->get(ContainerTestStandaloneClass::class);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved);
		$this->assertEquals($instance->getTestString(), $retrieved->getTestString());
	}

	public function testAnInterfaceCanBeADependency() {
		$container = new Container();
		$container->add(ContainerTestInterfaceImplemented::class);
		$container->setImplementation(
			interface: ContainerTestInterface::class,
			class: ContainerTestInterfaceImplemented::class
		);

		$retrieved = $container->get(ContainerTestInterface::class);
		$this->assertInstanceOf(ContainerTestInterfaceImplemented::class, $retrieved);
		$this->assertEquals('camelot', $retrieved->getTestString());
	}
}
