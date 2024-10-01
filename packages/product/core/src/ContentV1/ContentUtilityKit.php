<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Framework\Messages\Message;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Utility functions for working with Content.
 */
trait ContentUtilityKit {
	/**
	 * Allow the service to send messages.
	 *
	 * @var MessageBus
	 */
	private MessageBus $bus;

	/**
	 * Dispatch the given message if the given content is public.
	 *
	 * @param Message $message       Message to dispatch.
	 * @param array   $contentParams Array of parameters to pass to GenericContentById.
	 * @return void
	 */
	public function dispatchIfContentPublic(Message $message, array $contentParams): void {
		$content = $this->bus->fetch(new GenericContentById(...$contentParams));

		if ($content->visibility === ContentVisibility::Published) {
			$this->bus->dispatch($message);
		}
	}
}
