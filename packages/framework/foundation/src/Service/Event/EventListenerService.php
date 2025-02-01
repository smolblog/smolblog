<?php

namespace Smolblog\Foundation\Service\Event;

use Smolblog\Foundation\Service;

/**
 * A service that listens for and reacts to Events.
 *
 * This interface exists mostly to allow a listening service to be identified as such without having to declare it
 * somewhere else.
 */
interface EventListenerService extends Service {
}
