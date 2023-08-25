<?php

namespace Smolblog\Core\Content\Extensions\Tags;

use Smolblog\Core\Content\ContentBuilderKit;
use Smolblog\Core\Content\Events\PublicContentChanged;
use Smolblog\Core\Content\Queries\GenericContentBuilder;

/**
 * Indicates that the tags on a piece of public content have changed.
 */
class PublicContentTagsChanged extends PublicContentChanged implements GenericContentBuilder {
	use ContentBuilderKit;
}
