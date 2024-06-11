<?php

namespace Smolblog\Foundation\Service\Query;

use Smolblog\Foundation\Service;
use Smolblog\Foundation\Service\Messaging\Listener;

/**
 * Denotes a service that performs an action on or in response to Queries.
 *
 * This is not for fulfilling a singular Query; this is for actions related to any or all Queries. For example,
 * logging a Query's parameters or checking the security of a Query.
 *
 * Will have more structure in the future.
 */
interface QueryMiddleware extends Service, Listener {
}
