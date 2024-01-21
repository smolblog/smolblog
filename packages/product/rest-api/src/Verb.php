<?php

namespace Smolblog\Api;

/**
 * Standard definition of HTTP verbs.
 */
enum Verb: string {
	case GET = 'GET';
	case HEAD = 'HEAD';
	case POST = 'POST';
	case PUT = 'PUT';
	case DELETE = 'DELETE';
	case OPTIONS = 'OPTIONS';
	case TRACE = 'TRACE';
	case PATCH = 'PATCH';
}
