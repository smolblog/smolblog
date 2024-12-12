<?php

namespace Smolblog\Core;

use ReflectionClass;
use ReflectionProperty;
use Smolblog\Foundation\Value\Http\HttpVerb;

class Endpoints {
	private static $commands = [
		Channel\Commands\AddChannelToSite::class => ['route' => '/sites/{siteId}/channels'],
		Channel\Commands\PushContentToChannel::class => ['route' => '/channels/{channelId}/push'],

		Connection\Commands\BeginAuthRequest::class => ['route' => '/connections/create/{handler}/init', 'verb' => HttpVerb::GET],
		Connection\Commands\DeleteConnection::class => ['route' => '/connections/{connectionId}', 'verb' => HttpVerb::DELETE],
		Connection\Commands\FinishAuthRequest::class => ['route' => '/connections/create/{handler}/callback', 'verb' => HttpVerb::GET, 'authenticatedUserField' => null],
		Connection\Commands\RefreshChannels::class => ['route' => '/connections/{connectionId}/refreshChannels', 'verb' => HttpVerb::PUT],
		Connection\Commands\RefreshConnection::class => ['route' => '/connections/{connectionId}/refresh', 'verb' => HttpVerb::PUT],

		Content\Commands\CreateContent::class => ['route' => '/sites/{siteId}/content'],
		Content\Commands\DeleteContent::class => ['route' => '/sites/{siteId}/content/{contentId}', 'verb' => HttpVerb::DELETE],
		Content\Commands\UpdateContent::class => ['route' => '/sites/{siteId}/content/{contentId}', 'verb' => HttpVerb::PUT],

		Media\Commands\DeleteMedia::class => ['route' => '/sites/{siteId}/media/{mediaId}', 'verb' => HttpVerb::DELETE],
		Media\Commands\EditMediaAttributes::class => ['route' => '/sites/{siteId}/media/{mediaId}', 'verb' => HttpVerb::PUT],
		Media\Commands\HandleUploadedMedia::class => ['route' => '/sites/{siteId}/media/upload'],
		Media\Commands\SideloadMedia::class => ['route' => '/sites/{siteId}/media/sideload'],

		Site\Commands\CreateSite::class => ['route' => '/sites'],
		Site\Commands\SetUserSitePermissions::class => ['route' => '/sites/{siteId}/permissions', 'verb' => HttpVerb::PUT],
		Site\Commands\UpdateSiteDetails::class => ['route' => '/sites/{siteId}', 'verb' => HttpVerb::PUT],
	];

	private static $defaults = [
		'verb' => HttpVerb::POST,
		'authenticatedUserField' => 'userId',
		'response' => 'HTTP 201',
	];

	public static function listEndpoints() {
		echo "Smolblog REST API v3\n-------------------\n\n";

		foreach (self::$commands as $command => $overrides) {
			$props = \array_merge(self::$defaults, $overrides);
			$reflection = new ReflectionClass($command);

			echo $props['verb']->value . ' ' . $props['route'] . "\n";
			echo self::getDescription($reflection->getDocComment())[0] . "\n";
			echo 'Authentication: ' . (isset($props['authenticatedUserField']) ? 'Bearer' : 'n/a') . "\n";

			\preg_match_all('/{([\w\d]+)}/', $props['route'], $matches, \PREG_PATTERN_ORDER);
			$urlProps = $matches[1];
			foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $payloadProp) {
				if (\in_array($payloadProp->getName(), $urlProps) || $payloadProp->getName() == $props['authenticatedUserField']) {
					continue;
				}
				echo "  {$payloadProp->getName()}: {$payloadProp->getType()}\n";
			}
			echo "\n";
		}
	}

		/**
		 * Get a description and summary from an endpoint class' docblock.
		 *
		 * @param string $docblock Unparsed docblock.
		 * @return array Summary on index 0, full description on index 1.
		 */
	private static function getDescription(string $docblock): array {
		$summary = '';
		$description = '';

		foreach (explode("\n", $docblock) as $line) {
			if (str_starts_with('/**', $line) || str_contains($line, '*/')) {
				continue;
			}
			$procLine = ltrim($line, "* \t\n\r\0\x0B");

			if (empty($summary)) {
				$summary = $procLine;
				continue;
			}
			if (empty($description . $procLine)) {
				continue;
			}

			$description .= "$procLine\n";
		}

		return [$summary, $description];
	}
}
