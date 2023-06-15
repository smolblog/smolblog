<?php

namespace Smolblog\ActivityPub\Api;

enum ActorType: string {
	case Application = 'Application';
	case Group = 'Group';
	case Organizaion = 'Organizaion';
	case Person = 'Person';
	case Service = 'Service';
}
