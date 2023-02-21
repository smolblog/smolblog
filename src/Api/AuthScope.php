<?php

namespace Smolblog\Api;

enum AuthScope: string {
	case Read = 'read';
	case Write = 'write';
}
