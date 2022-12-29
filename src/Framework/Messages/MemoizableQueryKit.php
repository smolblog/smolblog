<?php

namespace Smolblog\Framework\Messages;

use Smolblog\Framework\Messages\StoppableMessageKit;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * Easy implementation for a MemoizableQuery.
 */
trait MemoizableQueryKit {
	use StoppableMessageKit;
	use SerializableKit;

	/**
	 * Create a memo key for this object by hashing the class name and its JSON-encoded properties.
	 *
	 * @return string
	 */
	public function getMemoKey(): string {
		$values = $this->toArray();
		unset($values['results']);
		return static::class . ':' . md5(json_encode($values));
	}
}
