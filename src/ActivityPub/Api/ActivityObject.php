<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPhp\Type;
use Smolblog\ActivityPhp\Type\AbstractObject;
use Smolblog\ActivityPhp\Type\TypeConfiguration;

/**
 * Wrapper for the ActivityPhp types to let them work more easily in the REST API.
 */
class ActivityObject {
	/**
	 * Wrapper around Smolblog\ActivityPhp\Type::create.
	 *
	 * @param array $data Array from the request JSON.
	 * @return AbstractObject
	 */
	public static function fromArray(array $data): AbstractObject {
		TypeConfiguration::set('undefined_properties', 'ignore');
		return Type::create($data);
	}
}
