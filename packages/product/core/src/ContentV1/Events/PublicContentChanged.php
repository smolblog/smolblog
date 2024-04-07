<?php

namespace Smolblog\Core\ContentV1\Events;

/**
 * Describes an event that changes content on the publicly-viewable site.
 *
 * This is called out as its own event type as there are a number of significant actions that take place when content
 * is first published, including publishing to external channels.
 */
class PublicContentChanged extends PublicContentEvent {
}
