<?php

namespace Smolblog\Core;

use Illuminate\Database\ConnectionInterface;
use Psr\Container\ContainerInterface;
use Smolblog\Foundation\Service\Messaging\MessageBus;
use Smolblog\Framework\Messages\MessageBus as DeprecatedMessageBus;
use Smolblog\Framework\Objects\DomainModel;
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
			'bus' => DeprecatedMessageBus::class,
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
			'messageBus' => DeprecatedMessageBus::class,
		],
		Connector\Services\ChannelLinker::class => [
			'bus' => DeprecatedMessageBus::class,
		],
		Connector\Services\ChannelRefresher::class => [
			'messageBus' => DeprecatedMessageBus::class,
			'connectors' => Connector\Services\ConnectorRegistry::class,
		],
		Connector\Services\ConnectionRefresher::class => [
			'messageBus' => DeprecatedMessageBus::class,
			'connectorRepo' => Connector\Services\ConnectorRegistry::class,
		],
		Connector\Services\ConnectionService::class => [
			'bus' => DeprecatedMessageBus::class,
		],
		Connector\Services\ConnectorRegistry::class => [
			'container' => ContainerInterface::class,
			'configuration' => null,
		],

		Content\ContentService::class => [
			'types' => Content\Type\ContentTypeRegistry::class,
			'extensions' => Content\Extension\ContentExtensionRegistry::class,
		],
		Content\ContentStateRepo::class => [
			'db' => ConnectionInterface::class,
		],
		Content\Note\NoteService::class => [
			'bus' => MessageBus::class
		],
		Content\Type\ContentTypeRegistry::class => [
			'container' => ContainerInterface::class,
		],
		Content\Extension\ContentExtensionRegistry::class => [
			'container' => ContainerInterface::class,
		],

		ContentV1\ContentService::class => [
			'bus' => DeprecatedMessageBus::class,
			'registry' => ContentV1\ContentTypeRegistry::class,
		],
		ContentV1\ContentTypeRegistry::class => [
			'configuration' => null,
		],
		ContentV1\Data\ContentEventStream::class => [
			'db' => ConnectionInterface::class,
		],
		ContentV1\Data\StandardContentProjection::class => [
			'db' => ConnectionInterface::class,
			'bus' => DeprecatedMessageBus::class,
		],
		ContentV1\Extensions\Syndication\ContentSyndicationProjection::class => [
			'db' => ConnectionInterface::class,
		],
		ContentV1\Extensions\Syndication\SyndicationService::class => [
			'bus' => DeprecatedMessageBus::class,
		],
		ContentV1\Extensions\Tags\TagService::class => [
			'bus' => DeprecatedMessageBus::class,
		],
		ContentV1\Markdown\MarkdownMessageRenderer::class => [
			'md' => SmolblogMarkdown::class,
		],
		ContentV1\Media\MediaService::class => [
			'bus' => DeprecatedMessageBus::class,
			'registry' => ContentV1\Media\MediaHandlerRegistry::class,
		],
		ContentV1\Media\MediaHandlerRegistry::class => [
			'container' => ContainerInterface::class,
			'configuration' => null,
		],
		ContentV1\Media\MediaProjection::class => [
			'db' => ConnectionInterface::class,
		],
		ContentV1\Types\Note\NoteProjection::class => [
			'db' => ConnectionInterface::class,
		],
		ContentV1\Types\Note\NoteService::class => [
			'bus' => DeprecatedMessageBus::class,
		],
		ContentV1\Types\Picture\PictureProjection::class => [
			'db' => ConnectionInterface::class,
		],
		ContentV1\Types\Picture\PictureService::class => [
			'bus' => DeprecatedMessageBus::class,
		],
		ContentV1\Types\Reblog\ReblogService ::class => [
			'bus' => DeprecatedMessageBus::class,
			'embedService' => ContentV1\Types\Reblog\ExternalContentService::class,
		],
		ContentV1\Types\Reblog\ReblogProjection::class => [
			'db' => ConnectionInterface::class,
		],
		Federation\FederationService::class => [
			'bus' => DeprecatedMessageBus::class,
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
			'bus' => DeprecatedMessageBus::class,
			'connectors' => Connector\Services\ConnectorRegistry::class,
		]
	];
}
