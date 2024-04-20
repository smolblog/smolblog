<?php

namespace Smolblog\Core\ContentV1;

/** @deprecated Migrate to Smolblog\Core\Content */
enum ContentVisibility: string {
	case Draft = 'draft';
	case Protected = 'protected';
	case Published = 'published';
}
