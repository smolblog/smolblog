<?php

namespace Smolblog\Core\ContentV1;

use Smolblog\Core\ContentV1\Commands\EditContentBaseAttributes;
use Smolblog\Core\ContentV1\Events\ContentBaseAttributeEdited;
use Smolblog\Core\ContentV1\Queries\AdaptableContentQuery;
use Smolblog\Foundation\Service\Messaging\ExecutionListener;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

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
	#[ExecutionListener(later: 5)]
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
