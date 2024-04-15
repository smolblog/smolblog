<?php

namespace Smolblog\Mock;

use Smolblog\Core\ContentV1\Events\PermalinkAssigned;
use Smolblog\Core\ContentV1\Events\PublicContentAdded;
use Smolblog\Foundation\Service\Messaging\ContentBuildLayerListener;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

class PermalinkService implements Listener {
	public function __construct(private MessageBus $bus) {}

	#[ContentBuildLayerListener(earlier: 10)]
	public function addContentPermalink(PublicContentAdded $event) {
		$permalink = "/post/$event->contentId";

		$this->bus->dispatch(new PermalinkAssigned(
			contentId: $event->contentId,
			userId: $event->userId,
			siteId: $event->siteId,
			permalink: $permalink,
		));
		$event->setContentProperty(permalink: $permalink);
	}
}
