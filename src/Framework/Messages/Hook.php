<?php

namespace Smolblog\Framework\Messages;

/**
 * A chance for other systems outside the core Domain Model to affect things.
 *
 * Unlike Commands, Events, and Queries, Hooks can (and are expected to) be modified.
 */
abstract class Hook {
}
