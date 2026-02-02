<?php

namespace Smolblog\Core\Content\Extensions\Warnings;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Content\Entities\ContentExtension;
use Smolblog\Core\Test\ContentExtensionTest;

#[AllowMockObjectsWithoutExpectations]
final class WarningsTest extends ContentExtensionTest {
	public const string EXTENSION_KEY = 'warnings';
	public const string SERVICE_CLASS = WarningsService::class;
	public const string EXTENSION_CLASS = Warnings::class;

	protected function createExampleExtension(): ContentExtension {
		return new Warnings([
			new ContentWarning('weapon', mention: true),
			new ContentWarning('blood'),
		]);
	}

	protected function createModifiedExtension(): ContentExtension {
		return new Warnings([
			new ContentWarning('language'),
			new ContentWarning('weapon', mention: true),
		]);
	}
}
