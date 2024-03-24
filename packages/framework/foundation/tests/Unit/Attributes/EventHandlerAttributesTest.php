<?php
use Crell\Tukio\ListenerPriority;
use Smolblog\Framework\Foundation\Attributes\CheckMemoLayerListener;
use Smolblog\Framework\Foundation\Attributes\ContentBuildLayerListener;
use Smolblog\Framework\Foundation\Attributes\EventStoreLayerListener;
use Smolblog\Framework\Foundation\Attributes\EventValidateLayerListener;
use Smolblog\Framework\Foundation\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Foundation\Attributes\SaveMemoLayerListener;
use Smolblog\Framework\Foundation\Attributes\SecurityLayerListener;

it('creates a ListenerPriority instance', function(string $class) {
	expect(new $class())->toBeInstanceOf(ListenerPriority::class);
})->with([
	CheckMemoLayerListener::class,
	ContentBuildLayerListener::class,
	EventStoreLayerListener::class,
	EventValidateLayerListener::class,
	ExecutionLayerListener::class,
	SaveMemoLayerListener::class,
	SecurityLayerListener::class,
]);
