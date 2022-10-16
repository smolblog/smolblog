<?php

namespace Smolblog\Core\Importer;

use Smolblog\Core\Connector\{Channel, Connection};
use Smolblog\Core\Registrar\Registerable;
use Smolblog\Core\Post\Post;

interface Importer extends Registerable {
	/**
	 * Get the configuration for the object. This will let the Registrar
	 * get the information it needs without having to instantiate.
	 *
	 * @return ImporterConfig
	 */
	public static function config(): ImporterConfig;

	/**
	 * Get posts from the given Channel/Connection according to the given options.
	 *
	 * @param Connection $connection Authenticated connection to use.
	 * @param Channel    $channel    Channel to pull posts from.
	 * @param array      $options    Options to use, such as types or pagination.
	 * @return ImportablePost[] Array of Posts ready to insert.
	 */
	public function getPostsFromChannel(Connection $connection, Channel $channel, array $options): array;
}
