<?php

namespace Smolblog\Api\ActivityPub;

class Action extends ActivityPubObject {
	public function __construct(
		string $id,
		string $type,
		public readonly string $actor,
		public readonly string $object,
		mixed ...$etCetera,
	)
	{
		parent::__construct(...$etCetera, id: $id, type: $type)
	}
}
