<?php

namespace Smolblog\Core;

use Psr\Container\ContainerInterface;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Set up the services and listeners for the Core domain model.
 */
class Model extends DomainModel {
	public const SERVICES = [
		Connector\Services\AuthRequestService::class => [
			'connectors' => Connector\Services\ConnectorRegistrar::class,
			'stateRepo' => Connector\Services\AuthRequestStateRepo::class,
			'messageBus' => MessageBus::class,
		],
		Connector\Services\ChannelRefresher::class => [
			'messageBus' => MessageBus::class,
			'connectors' => Connector\Services\ConnectorRegistrar::class,
		],
		Connector\Services\ConnectionRefresher::class => [
			'messageBus' => MessageBus::class,
			'connectorRepo' => Connector\Services\ConnectorRegistrar::class,
		],
		Connector\Services\ConnectorRegistrar::class => [
			'container' => ContainerInterface::class,
			'configuration' => null,
		],
		Content\Extensions\SyndicationLinks\SyndicationLinkService::class => [
			'bus' => MessageBus::class,
		],
		Content\Extensions\Tags\TagService::class => [
			'bus' => MessageBus::class,
		],
		Content\Markdown\MarkdownMessageRenderer::class => [
			'md' => SmolblogMarkdown::class,
		],
		Content\Types\Reblog\ReblogService ::class => [
			'bus' => MessageBus::class,
		],
		Content\Types\Status\StatusService::class => [
			'bus' => MessageBus::class,
		],
	];
}
