<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * Useful functions for building a Message. Use this kit for readonly classes.
 *
 * Stores metadata in a private property so it is not serialized. If the Message class is readonly, it will need
 * to be redeclared. Consuming class MUST set $this->meta in the constructor. For example:
 * `$this->meta = new MessageMetadata();`
 *
 * @deprecated Seriously, Evan? This is a code smell
 */
trait ReadonlyMessageKit {
	/**
	 * Store the message's meta data.
	 *
	 * @var MessageMetadata
	 */
	private readonly MessageMetadata $meta;

	/**
	 * Get the message's meta value for the given key.
	 *
	 * @deprecated Messages should be immutable.
	 *
	 * @param string $key Key of the meta value to get.
	 * @return mixed Value of the meta value.
	 */
	public function getMetaValue(string $key): mixed {
		return $this->meta->getMetaValue($key) ?? null;
	}

	/**
	 * Set the message's meta value for the given key.
	 *
	 * @deprecated Messages should be immutable.
	 *
	 * @param string $key   Key of the meta value to set.
	 * @param mixed  $value Value of the meta value.
	 * @return void
	 */
	public function setMetaValue(string $key, mixed $value): void {
		$this->meta->setMetaValue($key, $value);
	}

	/**
	 * Mark the message as being stopped.
	 *
	 * @deprecated Messages should not be stopped.
	 *
	 * @return void
	 */
	public function stopMessage(): void {
		$this->setMetaValue('stopped', true);
	}

	/**
	 * Check if the message is stopped.
	 *
	 * @deprecated Messages should not be stopped.
	 *
	 * @return boolean
	 */
	public function isPropagationStopped(): bool {
		return $this->getMetaValue('stopped') ?? false;
	}
}
