<?php

namespace Smolblog\Core\Media\Extensions;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Extensions\License\License;
use Smolblog\Core\Media\Entities\MediaExtension;
use Smolblog\Core\Test\MediaExtensionTest;

#[AllowMockObjectsWithoutExpectations]
final class LicenseTest extends MediaExtensionTest {
	public const string EXTENSION_KEY = 'license';
	public const string SERVICE_CLASS = LicenseService::class;
	public const string EXTENSION_CLASS = License::class;

	protected function createExampleExtension(): MediaExtension {
		return new License(); // all defaults
	}

	protected function createModifiedExtension(): MediaExtension {
		return new License(requiredAttributionOverride: 'Courtesy <a href="https://oddevan.com/">@oddevan.com</a>');
	}
}
