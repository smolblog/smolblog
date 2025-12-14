<?php

namespace Smolblog\Core\Content\Data;

use Smolblog\Core\Channel\Events\ContentPushedToChannel;
use Smolblog\Core\Channel\Events\ContentPushSucceeded;
use Smolblog\Core\Content\Events\{ContentCanonicalUrlSet, ContentCreated, ContentDeleted, ContentUpdated};
use Smolblog\Foundation\Service\Event\EventListener;
use Smolblog\Foundation\Service\Event\EventListenerService;

/**
 * A set of Content-related events that the Model expects to be listened to.
 *
 * This interface does not have to be implemented per se (there are no services in the registry depending on it). It is
 * provided as a way of communicating expectations. Even if this interface isn't "officially" implemented, a service
 * filling this role should behave according to the methods documented here.
 *
 * Also, remember to add the Smolblog\Foundation\Service\Event\EventListener attribute to these methods!
 */
interface ContentStateManager extends EventListenerService {
	/**
	 * Handle the ContentCreated event.
	 *
	 * This should create a new entry for the Content object described in the event. Use $event->getContentObject()
	 * to quickly get a full Content object.
	 *
	 * @param ContentCreated $event Event to handle.
	 * @return void
	 */
	public function onContentCreated(ContentCreated $event): void;

	/**
	 * Handle the ContentUpdated event.
	 *
	 * This should update the entry for the Content object with ID `$event->entityId` to match all properties described
	 * in the event. If a property exists in the persisted state that is `null` in this event, then the persisted state
	 * should be updated to be `null`. If the property _does not exist for the event_ (ex: `canonicalUrl` and `links`),
	 * then it should not be changed by this method.
	 *
	 * @param ContentUpdated $event Event to handle.
	 * @return void
	 */
	public function onContentUpdated(ContentUpdated $event): void;

	/**
	 * Handle the ContentDeleted event.
	 *
	 * The entry for the Content object with ID `$event->entityId` should be removed entirely from persisted storage.
	 *
	 * @param ContentDeleted $event Event to handle.
	 * @return void
	 */
	public function onContentDeleted(ContentDeleted $event): void;

	/**
	 * Handle the ContentCanonicalUrlSet event.
	 *
	 * Update the entry for Content ID `$event->entityId` with the given canonical URL. No other fields should be
	 * changed.
	 *
	 * @param ContentCanonicalUrlSet $event Event to handle.
	 * @return void
	 */
	public function onContentCanonicalUrlSet(ContentCanonicalUrlSet $event): void;

	/**
	 * Handle the ContentPushSucceeded event.
	 *
	 * Update the `links` property on the Content with ID `$event->contentId` to add the information in the event.
	 * Note that the `entityId` here is the ContentChannelEntry and created from the intersection of the Content and
	 * Channel IDs.
	 *
	 * @param ContentPushSucceeded $event Event to handle.
	 * @return void
	 */
	public function onContentPushSucceeded(ContentPushSucceeded $event): void;
}
