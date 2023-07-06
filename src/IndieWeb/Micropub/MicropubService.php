<?php

namespace Smolblog\IndieWeb\Micropub;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Core\Content\Queries\ContentByPermalink;
use Smolblog\Core\Federation\SiteByResourceUri;
use Smolblog\Core\User\UserById;
use Smolblog\Core\User\UserSites;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\IndieWeb\MicroformatsConverter;
use Taproot\Micropub\MicropubAdapter;

/**
 * Handle the Micropub endpoint
 */
class MicropubService extends MicropubAdapter {
	public function __construct(
		private ApiEnvironment $env,
		private MessageBus $bus,
		private MicroformatsConverter $mf,
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

		$content = $this->bus->fetch(new ContentByPermalink(
			siteId: $site->id,
			permalink: $parts['path'],
			userId: $this->user['id'],
		));
		if (!$content) {
			return false;
		}

		$author = $this->bus->fetch(new UserById($content->authorId));
		$props = $this->mf->entryPropertiesFromContent(content: $content, author: $author);

		return array_filter(
			$props,
			fn($key) => !isset($properties) || in_array($key, $properties),
			ARRAY_FILTER_USE_KEY
		);
	}


	public function createCallback(array $data, array $uploadedFiles) {
		wp_insert_post([
			'post_title' => 'Hit to micropub endpoint: create',
			'post_content' => '<pre>' . print_r(['data' => $data, 'uploadedFiles' => $uploadedFiles], true) . '</pre>',
		]);
	}


	public function updateCallback(string $url, array $actions) {
	}


	public function deleteCallback(string $url) {
	}


	public function undeleteCallback(string $url) {
	}


	public function mediaEndpointCallback(UploadedFileInterface $file) {
		$command = new HandleUploadedMedia(
			file: $file,
			userId: $this->user['id'],
			siteId: $this->bus->fetch(new UserSites($this->user['id']))[0]->id
		);

		$this->bus->dispatch($command);

		return $command->urlToOriginal ?? 500;
	}
}
