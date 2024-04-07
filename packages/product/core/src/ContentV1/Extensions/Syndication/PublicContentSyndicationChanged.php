<?php

namespace Smolblog\Core\ContentV1\Extensions\Syndication;

use Smolblog\Core\ContentV1\ContentBuilderKit;
use Smolblog\Core\ContentV1\Events\PublicContentChanged;
use Smolblog\Core\ContentV1\Queries\GenericContentBuilder;

/**
 * Indicates the syndication information on a piece of public content has changed.
 */
class PublicContentSyndicationChanged extends PublicContentChanged implements GenericContentBuilder {
	use ContentBuilderKit;
}
