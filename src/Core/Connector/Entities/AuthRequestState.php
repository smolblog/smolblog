<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\SerializableKit;
use Smolblog\Framework\Objects\Value;

/**
 * State for an OAuth request. Not an Entity because, though it needs to persist, it doesn't need the extra
 * requirements of being an Entity. It can be persisted in any key-value store.
 */
class AuthRequestState extends Value {
	use SerializableKit;

	/**
	 * Create the state
	 *
	 * @param string     $key    String used by both parties to identify the request.
	 * @param Identifier $userId User this request is attached to.
	 * @param array      $info   Information to store with this request.
	 */
	public function __construct(
		public readonly string $key,
		public readonly Identifier $userId,
		public readonly array $info,
	) {
	}

	/**
	 * Create an instance of this class from an associative array. Assumes array keys map correctly to object
	 * properties.
	 *
	 * @param array $data Data to initialize class with.
	 * @return static New instancce of this object
	 */
	public static function fromArray(array $data): static {
		$data['userId'] = Identifier::fromString($data['userId']);
		return new static(...$data);
	}

	/**
	 * Get all defined fields as a single array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$data = get_object_vars($this);
		$data['userId'] = $this->userId->toString();
		return $data;
	}
}
