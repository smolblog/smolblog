<?php

namespace Smolblog\Core\Parser;

use Smolblog\Core\Registrar\Registerable;
use Smolblog\Core\Post\Post;

interface Parser extends Registerable {
	/**
	 * Get the configuration for the object. This will let the Registrar
	 * get the information it needs without having to instantiate.
	 *
	 * @return ParserConfig
	 */
	public static function config(): ParserConfig;

	/**
	 * Convert the given data into a Post
	 *
	 * @param mixed $data    Post data to parse.
	 * @param array $options Options to use, such as types or pagination.
	 * @return null|Post Post ready to insert.
	 */
	public function createPost(mixed $data, array $options): ?Post;
}
