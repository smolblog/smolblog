<?php

namespace Smolblog\Core\Content\Events;

/**
 * Describes an event that adds content to the publicly-viewable site.
 *
 * This is called out as its own event type as there are a number of significant actions that take place when content
 * is first published, including publishing to external channels.
 */
class PublicContentAdded extends PublicContentEvent {
}
