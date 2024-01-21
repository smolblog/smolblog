<?php

namespace Smolblog\Core\Content;

enum ContentVisibility: string {
	case Draft = 'draft';
	case Protected = 'protected';
	case Published = 'published';
}
