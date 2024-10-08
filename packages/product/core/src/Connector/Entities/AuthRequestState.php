<?php

namespace Smolblog\Core\Connector\Entities;

use Smolblog\Foundation\Value\Fields\Identifier;
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
	 * @param string     $key         String used by both parties to identify the request.
	 * @param Identifier $userId      User this request is attached to.
	 * @param string     $provider    Connector this request is using.
	 * @param array      $info        Information to store with this request.
	 * @param string     $returnToUrl Optional URL to return the user to after completion.
	 */
	public function __construct(
		public readonly string $key,
		public readonly Identifier $userId,
		public readonly string $provider,
		public readonly array $info,
		public readonly ?string $returnToUrl = null,
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
