<?php

namespace Smolblog\Framework\ActivityPub\Objects;

/**
 * Indicates that the actor is undoing the object. In most cases, the object will be an Activity describing some
 * previously performed action (for instance, a person may have previously "liked" an article but, for whatever
 * reason, might choose to undo that like at some later point in time).
 *
 * The target and origin typically have no defined meaning.
 */
readonly class Undo extends Activity {
}
