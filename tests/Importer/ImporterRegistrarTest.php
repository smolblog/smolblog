<?php

namespace Smolblog\Core\Importer;

use PHPUnit\Framework\TestCase;

abstract class ImporterMock implements Importer {
	public static function config(): ImporterConfig { return new ImporterConfig(slug: 'camelot'); }
	public readonly string $id;

	public function __construct() {
		$this->id = uniqid();
	}
}

final class ImporterRegistrarTest extends TestCase {
	public function testImporterCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockForAbstractClass(ImporterMock::class);

		$importers = new ImporterRegistrar();
		$importers->register(class: ImporterMock::class, factory: fn() => $expected);
		$actual = $importers->get('camelot');

		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}
}
