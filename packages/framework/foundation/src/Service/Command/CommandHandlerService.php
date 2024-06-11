<?php

namespace Smolblog\Foundation\Service\Command;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Service\Messaging\Listener;

interface CommandHandlerService extends Service, Listener {
}
