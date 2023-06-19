<?php

namespace Smolblog\ActivityPub\Api;

use Smolblog\ActivityPhp\Type;
use Smolblog\ActivityPhp\Type\AbstractObject;

class ActivityObject {
	public static function fromArray(array $data): AbstractObject {
		return Type::create($data);
	}
}
