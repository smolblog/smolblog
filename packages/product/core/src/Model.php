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
			'perms' => Permissions\SitePermissionsService::class,
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

		Content\Services\ContentExtensionRegistry::class => [
			'container' => ContainerInterface::class,
		],
		Content\Services\ContentService::class => [
			'types' => Content\Services\ContentTypeRegistry::class,
			'extensions' => Content\Services\ContentExtensionRegistry::class,
			'repo' => Content\Data\ContentRepo::class,
			'perms' => Permissions\SitePermissionsService::class,
		],
		Content\Services\ContentTypeRegistry::class => [
			'container' => ContainerInterface::class,
		],

		Content\Types\Article\ArticleService::class => [
			'eventBus' => EventDispatcherInterface::class,
		],
		Content\Types\Note\NoteService::class => [
			'eventBus' => EventDispatcherInterface::class,
		],
		Content\Types\Reblog\ReblogService::class => [
			'eventBus' => EventDispatcherInterface::class,
		],

		Content\Extensions\Tags\TagsService::class => [],
		Content\Extensions\Warnings\WarningsService::class => [],

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
