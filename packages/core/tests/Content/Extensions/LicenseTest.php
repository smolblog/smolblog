<?php

namespace Smolblog\Core\Content\Extensions\License;

use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Test\ContentExtensionTest;

#[AllowMockObjectsWithoutExpectations]
final class LicenseTest extends ContentExtensionTest {
	public const string EXTENSION_KEY = 'license';
	public const string SERVICE_CLASS = LicenseService::class;
	public const string EXTENSION_CLASS = License::class;

	protected function createExampleExtension(): ContentExtension {
		return new License(); // all defaults
	}

	protected function createModifiedExtension(): ContentExtension {
		return new License(requiredAttributionOverride: 'Courtesy <a href="https://oddevan.com/">@oddevan.com</a>');
	}

	public function testCreatorMustNotBeEmpty() {
		$this->expectException(InvalidValueProperties::class);

		new License(creator: '');
	}

	public function testAttributionOverrideMustNotBeEmpty() {
		$this->expectException(InvalidValueProperties::class);

		new License(requiredAttributionOverride: '');
	}
}
