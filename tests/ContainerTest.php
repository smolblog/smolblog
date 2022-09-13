<?php

namespace Smolblog\Core;

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

	public function testDependenciesCanBeDeclaredOutOfOrder() {
		$container = new Container();
		$container->add(ContainerTestComposedClass::class);
		$container->add(ContainerTestStandaloneClass::class);
		$container->extend(ContainerTestComposedClass::class)->addArgument(ContainerTestStandaloneClass::class);

		$retrieved = $container->get(ContainerTestComposedClass::class);
		$this->assertInstanceOf(ContainerTestComposedClass::class, $retrieved);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved->getInternalClass());
	}

	public function testAnInstanceCanBePassedViaFactory() {
		$instance = new ContainerTestStandaloneClass();
		$instance->setTestString(uniqid());

		$container = new Container();
		$container->addShared(ContainerTestStandaloneClass::class, fn() => $instance);

		$retrieved = $container->get(ContainerTestStandaloneClass::class);
		$this->assertInstanceOf(ContainerTestStandaloneClass::class, $retrieved);
		$this->assertEquals($instance->getTestString(), $retrieved->getTestString());
	}
}
