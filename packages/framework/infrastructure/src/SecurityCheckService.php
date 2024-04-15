<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Foundation\Exceptions\MessageNotAuthorized;
use Smolblog\Foundation\Service\Messaging\SecurityListener;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Service\Messaging\MessageBus;

/**
 * Simple class to check the authorization Query of an AuthorizableMessage.
 */
class SecurityCheckService implements Listener {
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
	 * a MessageNotAuthorized is thrown.
	 *
	 * @throws MessageNotAuthorized Thrown when security query fails.
	 *
	 * @param AuthorizableMessage $event Message to check.
	 * @return void
	 */
	#[SecurityListener()]
	public function onAuthorizableMessage(AuthorizableMessage $event): void {

		$securityQuery = $event->getAuthorizationQuery();
		if (!$this->messageBus->fetch($securityQuery)) {
			$event->stopMessage();
			throw new MessageNotAuthorized(
				originalMessage: $event,
				authorizationQuery: $securityQuery
			);
		}
	}
}
