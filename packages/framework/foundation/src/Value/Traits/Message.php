<?php

namespace Smolblog\Foundation\Value\Traits;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * A message is an object passed to an event dispatcher and sent to listeners. It provides a way to attach metadata
 * to the message to preserve the immutability of the message.
 *
 * @see https://www.php-fig.org/psr/psr-14/
 *
 * @deprecated Events are fire-and-forget
 */
interface Message extends StoppableEventInterface {
	/**
	 * Call this to denote that the message should be stopped.
	 *
	 * @return void
	 */
	public function stopMessage(): void;

	/**
	 * Get a given meta value on this message.
	 *
	 * @param string $key Key of the meta value to get.
	 * @return mixed Value of the meta value.
	 */
	public function getMetaValue(string $key): mixed;

	/**
	 * Set a given meta value on this message.
	 *
	 * @param string $key   Key of the meta value to set.
	 * @param mixed  $value Value of the meta value.
	 * @return void
	 */
	public function setMetaValue(string $key, mixed $value): void;
}
