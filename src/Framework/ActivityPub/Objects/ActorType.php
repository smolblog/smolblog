<?php

namespace Smolblog\Framework\ActivityPub\Objects;

/**
 * Different types of Actor objects.
 */
enum ActorType {
	case Application;
	case Group;
	case Organization;
	case Person;
	case Service;
}
