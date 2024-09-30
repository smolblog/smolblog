<?php

namespace Smolblog\Core;

use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Channel\Services\ChannelLinker::class => [
			'channels' => Channel\Data\ChannelRepo::class,
			'eventBus' => EventDispatcherInterface::class,
		],
		Connection\Services\AuthRequestService::class => [
			'handlers' => Connection\Services\ConnectionHandlerRegistry::class,
			'stateRepo' => Connection\Data\AuthRequestStateRepo::class,
			'eventBus' => EventDispatcherInterface::class,
			'refresher' => Connection\Services\ConnectionChannelRefresher::class,
		],
		Connection\Services\ConnectionChannelRefresher::class => [
			'connections' => Connection\Data\ConnectionRepo::class,
			'channels' => Channel\Data\ChannelRepo::class,
			'handlers' => Connection\Services\ConnectionHandlerRegistry::class,
			'eventBus' => EventDispatcherInterface::class,
		],
		Connection\Services\ConnectionHandlerRegistry::class => [
			'container' => ContainerInterface::class,
		],
		Connection\Services\ConnectionRefresher::class => [
			'connections' => Connection\Data\ConnectionRepo::class,
			'handlers' => Connection\Services\ConnectionHandlerRegistry::class,
			'eventBus' => EventDispatcherInterface::class,
		],
		Connection\Services\ConnectionDeletionService::class => [
			'connections' => Connection\Data\ConnectionRepo::class,
			'eventBus' => EventDispatcherInterface::class,
		],

		Content\ContentService::class => [
			'bus' => MessageBus::class,
			'registry' => Content\ContentTypeRegistry::class,
		],
		Content\ContentTypeRegistry::class => [
			'configuration' => null,
		],
		Content\Data\ContentEventStream::class => [
			'db' => ConnectionInterface::class,
		],
		Content\Data\StandardContentProjection::class => [
			'db' => ConnectionInterface::class,
			'bus' => MessageBus::class,
		],
		Content\Extensions\Syndication\ContentSyndicationProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Content\Extensions\Syndication\SyndicationService::class => [
			'bus' => MessageBus::class,
		],
		Content\Extensions\Tags\TagService::class => [
			'bus' => MessageBus::class,
		],
		Content\Markdown\MarkdownMessageRenderer::class => [
			'md' => SmolblogMarkdown::class,
		],
		Content\Media\MediaService::class => [
			'bus' => MessageBus::class,
			'registry' => Content\Media\MediaHandlerRegistry::class,
		],
		Content\Media\MediaHandlerRegistry::class => [
			'container' => ContainerInterface::class,
			'configuration' => null,
		],
		Content\Media\MediaProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Content\Types\Note\NoteProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Content\Types\Note\NoteService::class => [
			'bus' => MessageBus::class,
		],
		Content\Types\Picture\PictureProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Content\Types\Picture\PictureService::class => [
			'bus' => MessageBus::class,
		],
		Content\Types\Reblog\ReblogService ::class => [
			'bus' => MessageBus::class,
			'embedService' => Content\Types\Reblog\ExternalContentService::class,
		],
		Content\Types\Reblog\ReblogProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Federation\FederationService::class => [
			'bus' => MessageBus::class,
			'followerProviders' => Federation\FollowerProviderRegistry::class,
		],
		Federation\FollowerProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Federation\FollowerProviderRegistry::class => [
			'container' => ContainerInterface::class,
			'configuration' => null,
		],
		Site\SiteEventStream::class => [
			'db' => ConnectionInterface::class,
		],
		Syndication\SyndicationService::class => [
			'bus' => MessageBus::class,
			'connectors' => Connection\Services\ConnectionHandlerRegistry::class,
		]
	];
}
