<?php

namespace Smolblog\Core\ContentV1\Extensions\Tags;

use Smolblog\Core\ContentV1\ContentBuilderKit;
use Smolblog\Core\ContentV1\Events\PublicContentChanged;
use Smolblog\Core\ContentV1\Queries\GenericContentBuilder;

/**
 * Indicates that the tags on a piece of public content have changed.
 */
class PublicContentTagsChanged extends PublicContentChanged implements GenericContentBuilder {
	use ContentBuilderKit;
}
