<?php

namespace Smolblog\Core\Test\Stubs;

use Smolblog\Core\Media\Services\MediaHandler;

/**
 * Provices a MediaHandler with key 'testmock'
 */
abstract class MediaHandlerTestBase implements MediaHandler {
	public static function getKey(): string {
		return 'testmock';
	}
}
