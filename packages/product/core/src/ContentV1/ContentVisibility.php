<?php

namespace Smolblog\Core\ContentV1;

enum ContentVisibility: string {
	case Draft = 'draft';
	case Protected = 'protected';
	case Published = 'published';
}
