<?php

namespace Smolblog\Framework\ActivityPub\Objects;

/**
 * Different types of Actor objects.
 */
enum ActorType: string {
	case Application = 'Application';
	case Group = 'Group';
	case Organization = 'Organization';
	case Person = 'Person';
	case Service = 'Service';
}
