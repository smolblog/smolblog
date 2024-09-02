<?php

namespace Smolblog\Foundation\Value\Traits;

/**
 * Useful functions for building a Message.
 *
 * Stores metadata in a private property so it is not serialized.
 *
 * @deprecated Events are fire-and-forget
 */
trait MessageKit {
	/**
	 * Store the message's meta data.
	 *
	 * @var array
	 */
	private array $meta = [];

	/**
	 * Get the message's meta value for the given key.
	 *
	 * @deprecated Events should be immutable
	 *
	 * @param string $key Key of the meta value to get.
	 * @return mixed Value of the meta value.
	 */
	public function getMetaValue(string $key): mixed {
		return $this->meta[$key] ?? null;
	}

	/**
	 * Set the message's meta value for the given key.
	 *
	 * @deprecated Events should be immutable.
	 *
	 * @param string $key   Key of the meta value to set.
	 * @param mixed  $value Value of the meta value.
	 * @return void
	 */
	public function setMetaValue(string $key, mixed $value): void {
		$this->meta[$key] = $value;
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
