<?php

namespace Smolblog\Framework\Foundation\Service\Messaging;

use Smolblog\Framework\Foundation\Service;

/**
 * A service that listens for and reacts to messages.
 *
 * This interface exists mostly to allow a listening service to be identified as such without having to declare it
 * somewhere else.
 */
interface Listener extends Service {
}
