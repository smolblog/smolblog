<?php

namespace Smolblog\CoreRestApi;

use Doctrine\DBAL\Connection as DatabaseConnection;
use Smolblog\Api\EndpointConfig;
use Smolblog\Foundation\DomainModel;
use Smolblog\Core;
use Smolblog\Foundation\Service\Command\CommandBus;
use Smolblog\Foundation\Value\Http\HttpVerb;
use Smolblog\Infrastructure\Endpoint\CommandEndpoint;
use Smolblog\Infrastructure\Endpoint\Endpoint;

/**
 * Set up the services and listeners for the Core Data domain model.
 */
class Model extends DomainModel {
	const AUTO_COMMANDS = [
		Core\Channel\Commands\AddChannelToSite::class => ['route' => '/sites/{siteId}/channels'],
		Core\Channel\Commands\PushContentToChannel::class => ['route' => '/channels/{channelId}/push'],

		Core\Connection\Commands\BeginAuthRequest::class => ['route' => '/connections/create/{handler}/init', 'verb' => HttpVerb::GET],
		Core\Connection\Commands\DeleteConnection::class => ['route' => '/connections/{connectionId}', 'verb' => HttpVerb::DELETE],
		Core\Connection\Commands\FinishAuthRequest::class => ['route' => '/connections/create/{handler}/callback', 'verb' => HttpVerb::GET, 'authenticatedUserField' => null],
		Core\Connection\Commands\RefreshChannels::class => ['route' => '/connections/{connectionId}/refreshChannels', 'verb' => HttpVerb::PUT],
		Core\Connection\Commands\RefreshConnection::class => ['route' => '/connections/{connectionId}/refresh', 'verb' => HttpVerb::PUT],

		Core\Content\Commands\CreateContent::class => ['route' => '/sites/{siteId}/content'],
		Core\Content\Commands\DeleteContent::class => ['route' => '/sites/{siteId}/content/{contentId}', 'verb' => HttpVerb::DELETE],
		Core\Content\Commands\UpdateContent::class => ['route' => '/sites/{siteId}/content/{contentId}', 'verb' => HttpVerb::PUT],

		Core\Media\Commands\DeleteMedia::class => ['route' => '/sites/{siteId}/media/{mediaId}', 'verb' => HttpVerb::DELETE],
		Core\Media\Commands\EditMediaAttributes::class => ['route' => '/sites/{siteId}/media/{mediaId}', 'verb' => HttpVerb::PUT],
		Core\Media\Commands\HandleUploadedMedia::class => ['route' => '/sites/{siteId}/media/upload'],
		Core\Media\Commands\SideloadMedia::class => ['route' => '/sites/{siteId}/media/sideload'],

		Core\Site\Commands\CreateSite::class => ['route' => '/sites'],
		Core\Site\Commands\SetUserSitePermissions::class => ['route' => '/sites/{siteId}/permissions', 'verb' => HttpVerb::PUT],
		Core\Site\Commands\UpdateSiteDetails::class => ['route' => '/sites/{siteId}', 'verb' => HttpVerb::PUT],
	];

	/**
	 * Get the dependency map for this Model.
	 *
	 * @return array
	 */
	public static function getDependencyMap(): array {
		return [
			...parent::getDependencyMap(),
			...self::generateCommandEndpoints(),
		];
	}

	private static function generateCommandEndpoints(): array {
		$endpoints = [];
		foreach (self::AUTO_COMMANDS as $command => $config) {
			$generated = self::makeEndpointClass($command, $config);
			$endpoints[get_class($generated)] = fn($container) => $generated->withBus($container->get(CommandBus::class));
		}
		return $endpoints;
	}

	private static function makeEndpointClass(string $commandClass, array $overrides): Endpoint {
		// NOPE this won't work. See https://www.php.net/manual/en/language.oop5.anonymous.php#123605 .
		$generated = new class () extends CommandEndpoint {
		};
	}
}
