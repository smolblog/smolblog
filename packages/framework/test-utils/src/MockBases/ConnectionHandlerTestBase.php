<?php

namespace Smolblog\Test\MockBases;

use Smolblog\Core\Connection\Services\ConnectionHandler;

/**
 * Provices a ConnectionHandler with key 'testmock'
 */
abstract class ConnectionHandlerTestBase implements ConnectionHandler {
	public static function getKey(): string {
		return 'testmock';
	}
}
