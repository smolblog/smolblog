<?php

namespace Smolblog\Core\ContentV1\Events;

/**
 * Describes an event that removes content from the publicly-viewable site.
 *
 * Removing content could have a number of side-effects that it can be useful to call out. Note that "removal" does not
 * have to indicate "deleted," as this could also be triggered by making a post "private"
 *
 * @deprecated Migrate to Smolblog\Core\Content
 */
class PublicContentRemoved extends PublicContentEvent {
}
