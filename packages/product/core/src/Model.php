<?php

namespace Smolblog\Core;

use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Connector\Data\ChannelProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Connector\Data\ChannelSiteLinkProjection::class => [
			'db' => ConnectionInterface::class,
			'bus' => MessageBus::class,
		],
		Connector\Data\ConnectionProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Connector\Data\ConnectorEventStream::class => [
			'db' => ConnectionInterface::class,
		],
		Connector\Services\AuthRequestService::class => [
			'connectors' => Connector\Services\ConnectorRegistry::class,
			'stateRepo' => Connector\Services\AuthRequestStateRepo::class,
			'messageBus' => MessageBus::class,
		],
		Connector\Services\ChannelLinker::class => [
			'bus' => MessageBus::class,
		],
		Connector\Services\ChannelRefresher::class => [
			'messageBus' => MessageBus::class,
			'connectors' => Connector\Services\ConnectorRegistry::class,
		],
		Connector\Services\ConnectionRefresher::class => [
			'messageBus' => MessageBus::class,
			'connectorRepo' => Connector\Services\ConnectorRegistry::class,
		],
		Connector\Services\ConnectionService::class => [
			'bus' => MessageBus::class,
		],
		Connector\Services\ConnectorRegistry::class => [
			'container' => ContainerInterface::class,
			'configuration' => null,
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
			'connectors' => Connector\Services\ConnectorRegistry::class,
		]
	];
}
