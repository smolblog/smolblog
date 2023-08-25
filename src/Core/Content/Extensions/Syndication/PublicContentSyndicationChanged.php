<?php

namespace Smolblog\Core\Content\Extensions\Syndication;

use Smolblog\Core\Content\ContentBuilderKit;
use Smolblog\Core\Content\Events\PublicContentChanged;
use Smolblog\Core\Content\Queries\GenericContentBuilder;

/**
 * Indicates the syndication information on a piece of public content has changed.
 */
class PublicContentSyndicationChanged extends PublicContentChanged implements GenericContentBuilder {
	use ContentBuilderKit;
}
