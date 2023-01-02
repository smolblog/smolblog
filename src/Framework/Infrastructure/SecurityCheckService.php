<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Framework\Exceptions\MessageNotAuthorizedException;
use Smolblog\Framework\Messages\Attributes\SecurityLayerListener;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\MessageBus;

/**
 * Simple class to check the authorization Query of an AuthorizableMessage.
 */
class SecurityCheckService {
	/**
	 * Construct the service.
	 *
	 * @param MessageBus $messageBus MessageBus implementation.
	 */
	public function __construct(private MessageBus $messageBus) {
	}

	/**
	 * Get the authorization query of an AuthorizableMessage and check it for a truthy value.
	 *
	 * Fetches the Query object given by AuthorizableMessage->getAuthorizationQuery(). If the query returns a truthy
	 * value (i.e. not zero or empty), the message can proceed. If it is a falsy value, the message is stopped and
	 * a MessageNotAuthorizedException is thrown.
	 *
	 * @throws MessageNotAuthorizedException Thrown when security query fails.
	 *
	 * @param AuthorizableMessage $event Message to check.
	 * @return void
	 */
	#[SecurityLayerListener()]
	public function onAuthorizableMessage(AuthorizableMessage $event): void {

		$securityQuery = $event->getAuthorizationQuery();
		if (!$this->messageBus->fetch($securityQuery)) {
			$event->stopMessage();
			throw new MessageNotAuthorizedException(
				originalMessage: $event,
				authorizationQuery: $securityQuery
			);
		}
	}
}
