<?php

namespace Smolblog\IndieWeb\Micropub;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\ContentTypeRegistry;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Core\Content\Queries\ContentByPermalink;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\EditNote;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Core\Content\Types\Reblog\CreateReblog;
use Smolblog\Core\Content\Types\Reblog\EditReblogComment;
use Smolblog\Core\Content\Types\Reblog\EditReblogUrl;
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
	 * @param ApiEnvironment        $env     Current environment.
	 * @param MessageBus            $bus     For sending queries and commands.
	 * @param MicroformatsConverter $mf      Handle converting Smolblog objects to their Microformats counterparts.
	 * @param ContentTypeRegistry   $typeReg For getting content type information.
	 */
	public function __construct(
		private ApiEnvironment $env,
		private MessageBus $bus,
		private MicroformatsConverter $mf,
		private ContentTypeRegistry $typeReg,
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
		$content = $this->contentByUrl($url);
		if (!isset($content)) {
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
			$publishCommand = new PublishReblog(...$commonProps);
		} else {
			$createCommand = new CreateNote(
				...$commonProps,
				text: $props['content'][0],
				publish: false,
			);
			$publishCommand = new PublishNote(...$commonProps);
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
		$content = $this->contentByUrl($url);
		if (!isset($content)) {
			return false;
		}

		$type = $content->type->getTypeKey();
		$commonProps = [
			'userId' => $this->user['id'],
			'siteId' => $content->siteId,
			'contentId' => $content->id,
		];
		$commands = [];
		$tags = array_map(fn($ent) => $ent->text, $content->extensions[Tags::class]?->tags ?? []);
		$originalTags = $tags;
		foreach ($actions as $action => $props) {
			if ($type === 'reblog' && isset($props['repost-of'])) {
				$commands[] = new EditReblogUrl(...$commonProps, url: $props['repost-of'][0]);
			}

			if (is_array($props['content'] ?? null)) {
				$newContent = $action === 'add' ? $this->getTextContent($content->type) ?? '' : '';
				$newContent .= $props['content'][0];
				switch ($content->type->getTypeKey()) {
					case 'reblog':
						$commands[] = new EditReblogComment(...$commonProps, comment: $newContent);
						break;

					case 'note':
						$commands[] = new EditNote(...$commonProps, text: $newContent);
						break;
				}
			}

			if (!empty($props['category'])) {
				switch ($action) {
					case 'add':
						$tags = array_merge($tags, $props['category']);
						break;

					case 'replace':
						$tags = $props['category'];
						break;

					case 'delete':
						$tags = array_values(array_diff($tags, $props['category']));
						break;
				}
			}
		}//end foreach

		if (is_array($actions['delete']) && array_is_list($actions['delete'])) {
			$deleteThese = $actions['delete'];
			if ($type === 'reblog' && in_array('content', $deleteThese)) {
				$commands[] = new EditReblogComment(...$commonProps, comment: null);
			}
			if (in_array('category', $deleteThese)) {
				$tags = [];
			}
		}

		if ($tags != $originalTags) {
			$commands[] = new SetTags(...$commonProps, tags: $tags);
		}

		foreach ($commands as $command) {
			$this->bus->dispatch($command);
		}
	}

	/**
	 * Delete the given content.
	 *
	 * @param string $url URL of the content.
	 * @return mixed
	 */
	public function deleteCallback(string $url) {
		$content = $this->contentByUrl($url);
		if (!isset($content)) {
			return false;
		}

		$commandClass = $this->typeReg->deleteItemCommandFor($content->type->getTypeKey());
		if (!isset($commandClass) || !class_exists($commandClass)) {
			return false;
		}

		$this->bus->dispatch(
			new $commandClass(contentId: $content->id, siteId: $content->siteId, userId: $this->user['id'])
		);
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

	/**
	 * Get the Content at the given URL.
	 *
	 * This will likely be its own query eventually. Eventually.
	 *
	 * @param string $url URL of the content.
	 * @return Content|null
	 */
	private function contentByUrl(string $url): ?Content {
		$site = $this->bus->fetch(new SiteByResourceUri($url));
		if (!$site) {
			return null;
		}

		$parts = parse_url($url);
		if (!$parts) {
			return null;
		}

		return $this->bus->fetch(new ContentByPermalink(
			siteId: $site->id,
			permalink: $parts['path'],
			userId: $this->user['id'],
		));
	}

	/**
	 * Get the text content from a ContentType object.
	 *
	 * @param ContentType $contentTypeData Content data.
	 * @return string|null
	 */
	private function getTextContent(ContentType $contentTypeData): ?string {
		switch ($contentTypeData->getTypeKey()) {
			case 'note':
				return $contentTypeData->text;

			case 'reblog':
				return $contentTypeData->comment;

			default:
				return null;
		}
	}
}
