<?php

namespace Smolblog\Framework\Messages;

use Smolblog\Foundation\Service\Event\EventListenerService;

/**
 * A service that listens for and reacts to messages.
 *
 * This interface exists mostly to allow a listening service to be identified as such without having to declare it
 * somewhere else.
 *
 * @deprecated use CommandHandlerService, QueryHandlerService, or EventListenerService
 */
interface Listener {
}
