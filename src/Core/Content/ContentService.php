<?php

namespace Smolblog\Core\Content;

use Smolblog\Core\Content\Commands\EditContentBaseAttributes;
use Smolblog\Core\Content\Events\ContentBaseAttributeEdited;
use Smolblog\Core\Content\Queries\AdaptableContentQuery;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Handle generic content commands.
 */
class ContentService implements Listener {
	/**
	 * Construct the service
	 *
	 * @param MessageBus          $bus      MessageBus for messages.
	 * @param ContentTypeRegistry $registry Registry of content types.
	 */
	public function __construct(
		private MessageBus $bus,
		private ContentTypeRegistry $registry,
	) {
	}

	/**
	 * Edit the base attributes on a piece of content.
	 *
	 * @param EditContentBaseAttributes $command Valid command to execute.
	 * @return void
	 */
	public function onEditContentBaseAttributes(EditContentBaseAttributes $command): void {
		$this->bus->dispatch(new ContentBaseAttributeEdited(
			contentId: $command->contentId,
			userId: $command->userId,
			siteId: $command->siteId,
			publishTimestamp: $command->publishTimestamp,
			authorId: $command->authorId,
		));
	}

	/**
	 * Fetch the content for an AdaptableContentQuery.
	 *
	 * @param AdaptableContentQuery $query Query being fetched.
	 * @return void
	 */
	public function onAdaptableContentQuery(AdaptableContentQuery $query): void {
		if ($query->getContentId() === null) {
			$query->setResults(null);
			return;
		}

		$singleQueryClass = $this->registry->singleItemQueryFor($query->getContentType());
		$query->setResults(
			$this->bus->fetch(
				new $singleQueryClass(
					userId: $query->getUserId(),
					siteId: $query->getSiteId(),
					contentId: $query->getContentId(),
				)
			)
		);
	}
}
