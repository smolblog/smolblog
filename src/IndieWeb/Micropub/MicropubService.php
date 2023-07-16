<?php

namespace Smolblog\IndieWeb\Micropub;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Core\Content\Queries\ContentByPermalink;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Core\Content\Types\Reblog\CreateReblog;
use Smolblog\Core\Content\Types\Reblog\PublishReblog;
use Smolblog\Core\Federation\SiteByResourceUri;
use Smolblog\Core\User\UserById;
use Smolblog\Core\User\UserSites;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\IndieWeb\MicroformatsConverter;
use Taproot\Micropub\MicropubAdapter;

/**
 * Handle the Micropub endpoint
 */
class MicropubService extends MicropubAdapter {
	/**
	 * Construct the service.
	 *
	 * @param ApiEnvironment        $env Current environment.
	 * @param MessageBus            $bus For sending queries and commands.
	 * @param MicroformatsConverter $mf  Handle converting Smolblog objects to their Microformats counterparts.
	 */
	public function __construct(
		private ApiEnvironment $env,
		private MessageBus $bus,
		private MicroformatsConverter $mf,
	) {
	}

	/**
	 * Verify the given access token.
	 *
	 * Verification itself is handled by the API layer itself. Scopes are not currently checked.
	 *
	 * @param string $token Given access token.
	 * @return array
	 */
	public function verifyAccessTokenCallback(string $token) {
		return [
			'id' => $this->request->getAttribute('smolblogUserId'),
		];
	}

	/**
	 * Get the Micropub configuration for this server.
	 *
	 * @param array $params Raw query parameters.
	 * @return mixed
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
		$site = $this->bus->fetch(new SiteByResourceUri($url));
		if (!$site) {
			return false;
		}

		$parts = parse_url($url);
		if (!$parts) {
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

	/**
	 * Create new content from the Micropub endpoint.
	 *
	 * @param array $data          Provided content.
	 * @param array $uploadedFiles Uploaded images.
	 * @return mixed
	 */
	public function createCallback(array $data, array $uploadedFiles) {
		if (!in_array('h-entry', $data['type'])) {
			return [
				'error' => 400,
				'error_description' => 'Unsupported type; must be Note or Repost.',
			];
		}

		$props = $data['properties'];
		$site = $this->bus->fetch(new UserSites($this->user['id']))[0];
		$createCommand = null;
		$publishCommand = null;

		$commonProps = [
			'userId' => $this->user['id'],
			'siteId' => $site->id,
			'contentId' => new DateIdentifier(),
		];

		if (isset($props['repost-of'])) {
			$comment = is_array($props['content'] ?? null) ? $props['content'][0] : null;
			$createCommand = new CreateReblog(
				...$commonProps,
				url: $props['repost-of'][0],
				comment: $comment,
				publish: false,
			);
			$publishCommand = new PublishReblog(
				siteId: $commonProps['siteId'],
				userId: $commonProps['userId'],
				reblogId: $commonProps['contentId'],
			);
		} else {
			$createCommand = new CreateNote(
				...$commonProps,
				text: $props['content'][0],
				publish: false,
			);
			$publishCommand = new PublishNote(
				siteId: $commonProps['siteId'],
				userId: $commonProps['userId'],
				noteId: $commonProps['contentId'],
			);
		}//end if

		$this->bus->dispatch($createCommand);

		if (!empty($props['category'])) {
			$this->bus->dispatch(new SetTags(
				...$commonProps,
				tags: $props['category'],
			));
		}

		$this->bus->dispatch($publishCommand);

		$createdContent = $this->bus->fetch(new GenericContentById(...$commonProps));
		return $site->baseUrl . $createdContent->permalink;
	}


	/**
	 * Update the given content.
	 *
	 * @param string $url     URL of the content.
	 * @param array  $actions Actions to take.
	 * @return mixed
	 */
	public function updateCallback(string $url, array $actions) {
	}

	/**
	 * Delete the given content.
	 *
	 * @param string $url URL of the content.
	 * @return mixed
	 */
	public function deleteCallback(string $url) {
	}

	/**
	 * Handle uploads at the media endpoint.
	 *
	 * @param UploadedFileInterface $file Uploaded files.
	 * @return mixed
	 */
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
