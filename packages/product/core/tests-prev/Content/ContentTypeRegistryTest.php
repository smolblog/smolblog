<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Queries\BaseContentById;
use Smolblog\Test\TestCase;

final class ContentTypeRegistryTest extends TestCase {
	public function testItRegistersContentTypeServices() {
		$this->assertEquals(ContentTypeService::class, ContentTypeRegistry::getInterfaceToRegister());
	}

	public function testDetailsOfRegisteredTypesCanBeRetrieved() {
		$typeOne = new class() implements ContentTypeService {
			public static function getConfiguration(): ContentTypeConfiguration
			{
				return new ContentTypeConfiguration(
					handle: 'typeOne',
					displayName: 'Type One',
					typeClass: __NAMESPACE__ . '\\TypeOne',
					singleItemQuery: __NAMESPACE__ . '\\TypeOneQuery',
					deleteItemCommand: __NAMESPACE__ . '\\TypeOneDelete',
				);
			}
		};
		$typeTwo = new class() implements ContentTypeService {
			public static function getConfiguration(): ContentTypeConfiguration
			{
				return new ContentTypeConfiguration(
					handle: 'typeTwo',
					displayName: 'Type Two',
					typeClass: __NAMESPACE__ . '\\TypeTwo',
					singleItemQuery: __NAMESPACE__ . '\\TypeTwoQuery',
					deleteItemCommand: __NAMESPACE__ . '\\TypeTwoDelete',
				);
			}
		};

		$registry = new ContentTypeRegistry([get_class($typeOne), get_class($typeTwo)]);

		$this->assertEquals(['typeOne' => 'Type One', 'typeTwo' => 'Type Two'], $registry->availableContentTypes());
		$this->assertEquals(__NAMESPACE__ . '\\TypeOne', $registry->typeClassFor('typeOne'));
		$this->assertEquals(__NAMESPACE__ . '\\TypeTwo', $registry->typeClassFor('typeTwo'));
		$this->assertEquals(__NAMESPACE__ . '\\TypeOneQuery', $registry->singleItemQueryFor('typeOne'));
		$this->assertEquals(__NAMESPACE__ . '\\TypeTwoQuery', $registry->singleItemQueryFor('typeTwo'));
		$this->assertEquals(__NAMESPACE__ . '\\TypeOneDelete', $registry->deleteItemCommandFor('typeOne'));
		$this->assertEquals(__NAMESPACE__ . '\\TypeTwoDelete', $registry->deleteItemCommandFor('typeTwo'));
	}
}
