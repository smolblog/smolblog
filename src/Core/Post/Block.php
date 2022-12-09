<?php

namespace Smolblog\Core\Post;

use Exception;
use Smolblog\Framework\Entity;
use Smolblog\Framework\Identifier;

/**
 * A unit of content for a Post. Just an empty Entity class because the only
 * common piece is an ID. Every Block's data could look different, and parsing
 * that data into another format is another class' job.
 */
readonly abstract class Block extends Entity {
	/**
	 * Create with the given ID. Creates a date-based ID if not provided.
	 *
	 * @param Identifier|null $id ID if one exists.
	 */
	public function __construct(Identifier $id = null) {
		parent::__construct(id: $id ?? Identifier::createFromDate());
	}

	/**
	 * Add the block type to the serialized value
	 *
	 * @return array
	 */
	public function toArray(): array {
		$arr = parent::toArray();
		$arr['type'] = static::class;

		return $arr;
	}

	/**
	 * Take in an array with a `type` key that corresponds to the block it represents.
	 *
	 * @throws Exception When given class does not exist.
	 *
	 * @param array $data Associative array representing a block.
	 * @return Block
	 */
	public static function fromTypedArray(array $data): Block {
		$class = $data['type'];
		if (!class_exists($class)) {
			throw new Exception("Class $class not found");
		}

		unset($data['type']);
		return $class::fromArray($data);
	}
}
