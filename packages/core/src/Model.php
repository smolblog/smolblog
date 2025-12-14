<?php

namespace Smolblog\Core;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\DomainModel;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const AUTO_SERVICES = [
		Channel\Services\ChannelDataService::class,
		Channel\Services\ChannelHandlerRegistry::class,
		Channel\Services\ChannelLinker::class,
		Channel\Services\ContentPushService::class,
		Channel\Services\ChannelQueryService::class,

		Connection\Services\ConnectionDataService::class,
		Connection\Services\AuthRequestService::class,
		Connection\Services\ConnectionChannelRefresher::class,
		Connection\Services\ConnectionHandlerRegistry::class,
		Connection\Services\ConnectionRefresher::class,
		Connection\Services\ConnectionDeletionService::class,

		Content\Services\ContentDataService::class,
		Content\Services\ContentExtensionRegistry::class,
		Content\Services\ContentService::class,
		Content\Services\ContentTypeRegistry::class,

		Content\Types\Article\ArticleService::class,
		Content\Types\Note\NoteService::class,
		Content\Types\Picture\PictureService::class,
		Content\Types\Reblog\ReblogService::class,

		Content\Extensions\Tags\TagsService::class,
		Content\Extensions\Warnings\WarningsService::class,

		Media\Services\MediaService::class,

		Site\Services\SiteDataService::class,
		Site\Services\SiteService::class,
	];

	public const SERVICES = [
		// Defined here because there is an optional parameter.
		Media\Services\MediaHandlerRegistry::class => [
			'container' => ContainerInterface::class,
		],
	];
}
