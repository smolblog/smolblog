<?php

namespace Smolblog\App\Registrars;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Smolblog\Core\Importer\Importer;

abstract class ImporterMock implements Importer {
	public readonly string $id;

	public function __construct() {
		$this->id = uniqid();
	}
}

final class ImporterRegistrarTest extends TestCase {
	public function testImporterCanBeRegisteredAndRetrieved() {
		$expected = $this->getMockForAbstractClass(ImporterMock::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('has')->willReturn(true);
		$container->method('get')->willReturn($expected);

		$importers = new ImporterRegistrar(container: $container);
		$importers->register(key: 'camelot', class: ImporterMock::class);
		$actual = $importers->get('camelot');

		$this->assertEquals(
			$expected->id,
			$actual->id
		);
	}
}
