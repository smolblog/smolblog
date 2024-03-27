<?php

use Crell\Tukio\ListenerPriority;
use Smolblog\Framework\Foundation\Service\Messaging\CheckMemoListener;
use Smolblog\Framework\Foundation\Service\Messaging\DownstreamListener;
use Smolblog\Framework\Foundation\Service\Messaging\ExecutionListener;
use Smolblog\Framework\Foundation\Service\Messaging\PersistEventListener;
use Smolblog\Framework\Foundation\Service\Messaging\SaveMemoListener;
use Smolblog\Framework\Foundation\Service\Messaging\SecurityListener;
use Smolblog\Framework\Foundation\Service\Messaging\ValidateEventListener;

it('creates a ListenerPriority instance', function(string $class) {
	expect(new $class())->toBeInstanceOf(ListenerPriority::class);
})->with([
	CheckMemoListener::class,
	DownstreamListener::class,
	ExecutionListener::class,
	PersistEventListener::class,
	SaveMemoListener::class,
	SecurityListener::class,
	ValidateEventListener::class,
]);
