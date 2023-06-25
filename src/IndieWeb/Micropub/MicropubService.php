<?php

namespace Smolblog\IndieWeb\Micropub;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Federation\SiteByResourceUri;
use Smolblog\Core\User\UserSites;
use Smolblog\Framework\Messages\MessageBus;
use Taproot\Micropub\MicropubAdapter;

/**
 * Handle the Micropub endpoint
 */
class MicropubService extends MicropubAdapter {
	public function __construct(
		private ApiEnvironment $env,
		private MessageBus $bus,
	) {
	}

	public function verifyAccessTokenCallback(string $token) {
		return [
			'id' => $this->request->getAttribute('smolblogUserId'),
		];
	}

	/**
	 * Get the Micropub configuration for this server.
	 *
	 * @param array $params Raw query parameters.
	 * @return void
	 */
	public function configurationQueryCallback(array $params) {
		$sites = $this->bus->fetch(new UserSites($this->user['id'])) ?? [];
		$allChannels = [];
		$sitechannels = [];

		foreach ($sites as $site) {
			$siteChannels[$site->id->toString()] = $this->bus->fetch(
				new ChannelsForSite(siteId: $site->id, canPush: true)
			) ?? [];

			foreach ($siteChannels[$site->id->toString()] as $channel) {
				$allChannels[$channel->id->toString] = $channel;
			}
		}

		return [
			'media-endpoint' => $this->env->getApiUrl('/micropub/media'),
			'destination' => array_map(
				fn($site) => [
					'uid' => $site->id->toString(),
					'name' => $site->handle,
					'smolblog-display-name' => $site->displayName,
					'smolblog-url' => $site->baseUrl,
					'syndicate-to' => array_map(
						fn($channel) => [
							'uid' => $channel->id->toString(),
							'name' => $channel->displayName,
						],
						$siteChannels[$site->id->toString()]
					),
				],
				$sites
			),
			'post-types' => [
				['type' => 'note', 'name' => 'Note'],
				['type' => 'repost', 'name' => 'Reblog'],
			],
			'syndicate-to' => array_map(
				fn($channel) => [
					'uid' => $channel->id->toString(),
					'name' => $channel->displayName,
				],
				array_values($allChannels),
			),
		];
	}

	/**
	 * Handle a source query.
	 *
	 * @param string     $url        URL of the item to query.
	 * @param array|null $properties Properties to return (null if all).
	 * @return array|false
	 */
	public function sourceQueryCallback(string $url, ?array $properties = null) {
		$parts = parse_url($url);
		if (!$parts) {
			return false;
		}

		$site = $this->bus->fetch(new SiteByResourceUri($parts['host']));
		if (!$site) {
			return false;
		}

		$content = $this->bus->fetch();
	}


	public function createCallback(array $data, array $uploadedFiles) {
	}


	public function updateCallback(string $url, array $actions) {
	}


	public function deleteCallback(string $url) {
	}


	public function undeleteCallback(string $url) {
	}


	public function mediaEndpointCallback(UploadedFileInterface $file) {
	}
}
