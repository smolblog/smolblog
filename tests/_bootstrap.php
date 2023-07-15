<?php

namespace Smolblog\Test;

use Closure;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use SebastianBergmann\Comparator\ComparisonFailure;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Api\EndpointConfig;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\RandomIdentifier;

require_once __DIR__ . '/../vendor/autoload.php';

class TestCase extends PHPUnitTestCase {
	protected function randomId(bool $scrub = false): Identifier {
		$id = new RandomIdentifier();

		return $scrub ? $this->scrubId($id) : $id;
	}

	protected function scrubId(Identifier $id): Identifier {
		return Identifier::fromByteString($id->toByteString());
	}
}

trait EndpointTestToolkit {
	private function getApiEnvironment(): ApiEnvironment {
		return new class implements ApiEnvironment {
			public function getApiUrl(string $endpoint = '/'): string {
				return '//smol.blog/api' . $endpoint;
			}
		};
	}

	private function mockBusExpects(Command $expected) {
		$bus = $this->createMock(MessageBus::class);
		$bus->expects($this->once())->method('dispatch')->with($this->equalTo($expected));
		return $bus;
	}

	public function testItGivesAValidConfiguration(): void {
		$config = self::ENDPOINT::getConfiguration();
		$this->assertInstanceOf(EndpointConfig::class, $config);
	}
}

trait DateIdentifierTestKit {
	/**
	 * Asserts that two identifiers are created from the same date. A v7 UUID hashes the date, then adds random bytes.
	 * This function trims the random bytes and compares the remaining data.
	 */
	private function assertIdentifiersHaveSameDate(Identifier $expected, Identifier $actual) {
		$expectedTrim = substr(strval($expected), offset: 0, length: -8);
		$actualTrim = substr(strval($actual), offset: 0, length: -8);

		$this->assertEquals($expectedTrim, $actualTrim);
	}
}

trait EventComparisonTestKit {
	private function eventEquivalentTo(Event $expected): Constraint {
		return new EventIsEquivalent($expected);
	}
}

class EventIsEquivalent extends Constraint {
	public function __construct(private Event $expected) {}

	public function toString(): string { return 'two Events are equivalent'; }
	protected function failureDescription($other): string { return $this->toString(); }

	protected function matches($other): bool {
		if (!is_a($other, Event::class)) {
			throw new InvalidArgumentException('Object is not an Event.');
		}

		$expectedData = $this->expected->toArray();
		unset($expectedData['id']);
		unset($expectedData['timestamp']);

		$actualData = $other->toArray();
		unset($actualData['id']);
		unset($actualData['timestamp']);

		return $expectedData == $actualData;
	}

	protected function fail($other, $description, ?ComparisonFailure $comparisonFailure = null): void
	{
		if ($comparisonFailure === null) {
			$expectedData = $this->expected->toArray();
			unset($expectedData['id']);
			unset($expectedData['timestamp']);

			$actualData = $other->toArray();
			unset($actualData['id']);
			unset($actualData['timestamp']);

			$comparisonFailure = new ComparisonFailure(
				$this->expected,
				$other,
				$this->exporter()->export($expectedData),
				$this->exporter()->export($actualData),
				false,
				'Failed asserting that two Events are equivalent.'
			);
		}

		parent::fail($other, $description, $comparisonFailure);
	}
}

trait DatabaseTestKit {
	private Connection $db;

	private function initDatabaseWithTable(string $name, Closure $builder): void {
		$manager = new Manager();
		$manager->addConnection([
			'driver' => 'sqlite',
			'database' => ':memory:',
			'prefix' => '',
		]);
		$manager->getConnection()->getSchemaBuilder()->create($name, $builder);

		$this->db = $manager->getConnection();
	}

	protected function assertOnlyTableEntryEquals(Builder $table, mixed ...$expected) {
		$this->assertEquals((object)$expected, $table->first());
		$this->assertEquals(1, $table->count());
	}

	protected function assertTableEmpty(Builder $table) {
		$this->assertEquals(0, $table->count(), 'Table not empty: found ' . print_r($table->first(), true));
	}
}
