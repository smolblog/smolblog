<?php

namespace Smolblog\Foundation\Value\Jobs;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

readonly class ExampleJob extends Job {
	public function __construct(string $service, string $method, public string $prop) {
		parent::__construct($service, $method);
	}
}

#[CoversClass(Job::class)]
final class JobTest extends TestCase {
	public function testItCanBeInstantiated() {
		$actual = new ExampleJob(self::class, 'go', 'test');

		$this->assertInstanceOf(Job::class, $actual);
		$this->assertEquals(['prop' => 'test'], $actual->getParameters());
	}

	public function testItCanBeSerializedAndDeserialized() {
		$object = new ExampleJob(self::class, 'go', 'test');
		$array = [
			'type' => ExampleJob::class,
			'service' => self::class,
			'method' => 'go',
			'prop' => 'test',
		];

		$this->assertEquals($array, $object->serializeValue());
		$this->assertEquals($object, Job::deserializeValue($array));
	}
}
