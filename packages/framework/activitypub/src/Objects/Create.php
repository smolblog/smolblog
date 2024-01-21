<?php

namespace Smolblog\Framework\ActivityPub\Objects;

/**
 * The Create activity is used when posting a new object. This has the side effect that the object embedded within
 * the Activity (in the object property) is created.
 */
readonly class Create extends Activity {
}
