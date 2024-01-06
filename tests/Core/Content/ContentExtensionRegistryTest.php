<?php

namespace Smolblog\Core\Content;

use Smolblog\Test\TestCase;

final class ContentExtensionRegistryTest extends TestCase {
	public function testItRegistersContentExtensionServices() {
		$this->assertEquals(ContentExtensionService::class, ContentExtensionRegistry::getInterfaceToRegister());
	}

	public function testDetailsOfRegisteredTypesCanBeRetrieved() {
		$typeOne = new class() implements ContentExtensionService {
			public static function getConfiguration(): ContentExtensionConfiguration
			{
				return new ContentExtensionConfiguration(
					handle: 'typeOne',
					displayName: 'Type One',
					extensionClass: __NAMESPACE__ . '\\TypeOne',
				);
			}
		};
		$typeTwo = new class() implements ContentExtensionService {
			public static function getConfiguration(): ContentExtensionConfiguration
			{
				return new ContentExtensionConfiguration(
					handle: 'typeTwo',
					displayName: 'Type Two',
					extensionClass: __NAMESPACE__ . '\\TypeTwo',
				);
			}
		};

		$registry = new ContentExtensionRegistry([get_class($typeOne), get_class($typeTwo)]);

		$this->assertEquals(['typeOne' => 'Type One', 'typeTwo' => 'Type Two'], $registry->availableContentExtensions());
		$this->assertEquals(__NAMESPACE__ . '\\TypeOne', $registry->extensionClassFor('typeOne'));
		$this->assertEquals(__NAMESPACE__ . '\\TypeTwo', $registry->extensionClassFor('typeTwo'));
	}
}
