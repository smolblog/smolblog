<?php

namespace Smolblog\Core\Test\Setup;

use Smolblog\Core\Media\Services\MediaHandler;

/**
 * Provices a MediaHandler with key 'testmock'
 */
abstract class MediaExtensionTestMediaHandler implements MediaHandler {
	public static function getKey(): string {
		return 'testmock';
	}
}
