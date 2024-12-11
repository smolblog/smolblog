<?php

namespace Smolblog\Core;

use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\DomainModel;
use Smolblog\Foundation\Service\KeypairGenerator;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Channel\Services\ChannelHandlerRegistry::class => [
			'container' => ContainerInterface::class,
		],
		Channel\Services\ChannelLinker::class => [
			'channels' => Channel\Data\ChannelRepo::class,
			'eventBus' => EventDispatcherInterface::class,
			'perms' => Permissions\SitePermissionsService::class,
		],
		Channel\Services\ContentPushService::class => [
			'eventBus' => EventDispatcherInterface::class,
			'perms' => Permissions\SitePermissionsService::class,
			'contentRepo' => Content\Data\ContentRepo::class,
			'channelRepo' => Channel\Data\ChannelRepo::class,
			'handlers' => Channel\Services\ChannelHandlerRegistry::class,
		],
		Channel\Services\ChannelQueryService::class => [
			'repo' => Channel\Data\ChannelRepo::class,
			'sitePerms' => Permissions\SitePermissionsService::class,
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
		Content\Types\Picture\PictureService::class => [
			'eventBus' => EventDispatcherInterface::class,
		],
		Content\Types\Reblog\ReblogService::class => [
			'eventBus' => EventDispatcherInterface::class,
		],

		Content\Extensions\Tags\TagsService::class => [],
		Content\Extensions\Warnings\WarningsService::class => [],

		Media\Services\MediaHandlerRegistry::class => [
			'container' => ContainerInterface::class,
		],
		Media\Services\MediaService::class => [
			'bus' => EventDispatcherInterface::class,
			'registry' => Media\Services\MediaHandlerRegistry::class,
			'mediaRepo' => Media\Data\MediaRepo::class,
			'perms' => Permissions\SitePermissionsService::class,
		],

		Site\Services\SiteService::class => [
			'globalPerms' => Permissions\GlobalPermissionsService::class,
			'sitePerms' => Permissions\SitePermissionsService::class,
			'repo' => Site\Data\SiteRepo::class,
			'keygen' => KeypairGenerator::class,
			'eventBus' => EventDispatcherInterface::class,
		]
	];
}
