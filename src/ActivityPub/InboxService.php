<?php

namespace Smolblog\ActivityPub;

use Smolblog\ActivityPhp\Type\Extended\Activity\Follow;
use Smolblog\Framework\Messages\MessageBus;

class InboxService {
	public function __construct(
		private MessageBus $bus,
	)
	{

	}

	public function handleFollow(Follow $request): void {
		// find site from $request->object

		$this->bus->dispatch(new ActivityPubFollowerAdded(
			request: $request,
		))
	}
}
