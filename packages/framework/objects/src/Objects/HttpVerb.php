<?php

namespace Smolblog\Framework\Objects;

/**
 * Standard definition of HTTP verbs.
 *
 * @deprecated Migrate to Smolblog\Foundation classes
 */
enum HttpVerb: string {
	case GET = 'GET';
	case HEAD = 'HEAD';
	case POST = 'POST';
	case PUT = 'PUT';
	case DELETE = 'DELETE';
	case OPTIONS = 'OPTIONS';
	case TRACE = 'TRACE';
	case PATCH = 'PATCH';
}
