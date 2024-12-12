<?php

namespace Smolblog\Core;

use Psr\Container\ContainerInterface;
use Smolblog\Foundation\DomainModel;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const AUTO_COMMANDS = [
		Channel\Commands\AddChannelToSite::class,
		Channel\Commands\PushContentToChannel::class,

		Connection\Commands\BeginAuthRequest::class,
		Connection\Commands\DeleteConnection::class,
		Connection\Commands\FinishAuthRequest::class,
		Connection\Commands\RefreshChannels::class,
		Connection\Commands\RefreshConnection::class,

		Content\Commands\CreateContent::class,
		Content\Commands\DeleteContent::class,
		Content\Commands\UpdateContent::class,

		Media\Commands\DeleteMedia::class,
		Media\Commands\EditMediaAttributes::class,
		Media\Commands\HandleUploadedMedia::class,
		Media\Commands\SideloadMedia::class,

		Site\Commands\CreateSite::class,
		Site\Commands\SetUserSitePermissions::class,
		Site\Commands\UpdateSiteDetails::class,
	];

	public const AUTO_SERVICES = [
		Channel\Services\ChannelHandlerRegistry::class,
		Channel\Services\ChannelLinker::class,
		Channel\Services\ContentPushService::class,
		Channel\Services\ChannelQueryService::class,

		Connection\Services\AuthRequestService::class,
		Connection\Services\ConnectionChannelRefresher::class,
		Connection\Services\ConnectionHandlerRegistry::class,
		Connection\Services\ConnectionRefresher::class,
		Connection\Services\ConnectionDeletionService::class,

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

		Site\Services\SiteService::class,
	];

	public const SERVICES = [
		Media\Services\MediaHandlerRegistry::class => [
		'container' => ContainerInterface::class,
		],
	];
}
