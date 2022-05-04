<?php

namespace Smolblog\Core\Endpoints;

use Smolblog\Core\Definitions\Endpoint;

/**
 * An abastract Endpoint class that takes care of most of the basics. Provides
 * some sensible defaults for getting an Endpoint built quickly. More advanced
 * classes will probably want to implement the Endpoint interface directly and
 * not inherit from this class.
 */
abstract class BasicEndpoint implements Endpoint {
}
