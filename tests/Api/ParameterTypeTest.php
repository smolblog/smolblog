<?php

namespace Smolblog\Api;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class ParameterTypeTest extends TestCase {
	public function testBasicDeclarativeMethods() {
		$this->assertEquals(
			new ParameterType(type: 'number'),
			ParameterType::number()
		);
		$this->assertEquals(
			new ParameterType(type: 'integer'),
			ParameterType::integer()
		);
		$this->assertEquals(
			new ParameterType(type: 'boolean'),
			ParameterType::boolean()
		);

		$this->assertEquals(
			new ParameterType(type: 'string', format: 'date-time'),
			ParameterType::dateTime()
		);
		$this->assertEquals(
			new ParameterType(type: 'string', format: 'date'),
			ParameterType::date()
		);

		$this->assertEquals(
			new ParameterType(type: 'array', items: new ParameterType(type: 'integer')),
			ParameterType::array(items: ParameterType::integer())
		);
	}

	public function testRequiredMethodDoesNotOtherwiseModify() {
		$this->assertEquals(
			new ParameterType(type: 'string', required: true, format: 'date-time'),
			ParameterType::required(ParameterType::dateTime()),
		);
	}

	public function testIdentifierTypeCorrectlyDescribesIdentifier() {
		$idType = ParameterType::identifier();

		$this->assertEquals('string', $idType->type);
		$this->assertEquals('uuid', $idType->format);

		$this->assertEquals(1, preg_match("/$idType->pattern/", Identifier::createRandom()->toString()));
	}

	public function testItCreatesASchema() {
		$type = ParameterType::required(ParameterType::object(
			url: ParameterType::required(ParameterType::string(format: 'url')),
			timestamp: ParameterType::dateTime()
		));

		$this->assertEquals(
			[
				'type' => 'object',
				'required' => 'true',
				'properties' => [
					'url' => [
						'type' => 'string',
						'format' => 'url',
					],
					'timestamp' => [
						'type' => 'string',
						'format' => 'date-time',
					],
				],
				'required' => ['url']
			],
			$type->schema()
		);
	}

	public function testArrayTypeAlsoWorks() {
		$this->assertInstanceOf(
			ArrayType::class,
			new ArrayType(self::class),
		);

		$this->assertInstanceOf(
			ArrayType::class,
			new ArrayType(['type' => 'integer', 'format' => 'base64']),
		);
	}

	public function testArrayParameterTypeHasItemSchema() {
		$expected = [
			'type' => 'array',
			'items' => [
				'type' => 'string',
				'format' => 'date-time',
			],
		];

		$actual = ParameterType::array(items: ParameterType::dateTime());

		$this->assertEquals($expected, $actual->schema());
	}

	public function testAnObjectReferenceCanBeCreated() {
		$expected = ['$ref' => '#/components/schemas/ParameterTypeTest'];
		$actual = ParameterType::fromClass(self::class);

		$this->assertEquals($expected, $actual->schema());
	}
}
