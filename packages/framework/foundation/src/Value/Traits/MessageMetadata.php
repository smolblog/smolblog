<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * A class to store metadata on a message.
 *
 * Written the way it is to allow adding mutable properties to an immutable object.
 *
 * @deprecated Seriously, Evan? This is a code smell
 */
class MessageMetadata {
	/**
	 * Store the metadata.
	 *
	 * @var array
	 */
	private array $metadata;

	/**
	 * Construct the metadata.
	 */
	public function __construct() {
		$this->metadata = ['stopped' => false];
	}

	/**
	 * Get a meta value.
	 *
	 * @param string $key Key of the meta value to get.
	 * @return mixed
	 */
	public function getMetaValue(string $key): mixed {
		return $this->metadata[$key] ?? null;
	}

	/**
	 * Set a meta value
	 * @param string $key   Key of the meta value to set.
	 * @param mixed  $value Value of the meta value.
	 * @return void
	 */
	public function setMetaValue(string $key, mixed $value): void {
		$this->metadata[$key] = $value;
	}
}
