<?php

namespace Smolblog\Foundation\Service\Command;

use Smolblog\Foundation\Service;
use Smolblog\Framework\Messages\Listener;

/**
 * Denotes a service that performs an action on or in response to Commands.
 *
 * This is not for fulfilling a singular Command; this is for actions related to any or all Commands. For example,
 * logging a Command's parameters or checking the security of a Command.
 *
 * Will have more structure in the future.
 */
interface CommandMiddleware extends Service, Listener {
}
