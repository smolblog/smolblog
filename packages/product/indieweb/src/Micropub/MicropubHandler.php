<?php

namespace Smolblog\IndieWeb\Micropub;

use DateTimeInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Connector\Queries\ChannelsForSite;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\ContentType;
use Smolblog\Core\Content\ContentTypeRegistry;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Extensions\Syndication\AddSyndicationLink;
use Smolblog\Core\Content\Extensions\Syndication\SetSyndicationChannels;
use Smolblog\Core\Content\Extensions\Syndication\Syndication;
use Smolblog\Core\Content\Extensions\Tags\SetTags;
use Smolblog\Core\Content\Extensions\Tags\Tags;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Core\Content\Media\Media;
use Smolblog\Core\Content\Media\MediaByDefaultUrl;
use Smolblog\Core\Content\Media\MediaById;
use Smolblog\Core\Content\Media\SideloadMedia;
use Smolblog\Core\Content\Queries\ContentByPermalink;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Note\CreateNote;
use Smolblog\Core\Content\Types\Note\EditNote;
use Smolblog\Core\Content\Types\Note\PublishNote;
use Smolblog\Core\Content\Types\Picture\CreatePicture;
use Smolblog\Core\Content\Types\Picture\PublishPicture;
use Smolblog\Core\Content\Types\Reblog\CreateReblog;
use Smolblog\Core\Content\Types\Reblog\EditReblogComment;
use Smolblog\Core\Content\Types\Reblog\EditReblogUrl;
use Smolblog\Core\Content\Types\Reblog\PublishReblog;
use Smolblog\Core\Federation\SiteByResourceUri;
use Smolblog\Core\Site\SiteById;
use Smolblog\Core\User\UserById;
use Smolblog\Core\User\UserSites;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\IndieWeb\MicroformatsConverter;
use Taproot\Micropub\MicropubAdapter;

/**
 * Handle the Micropub endpoint.
 *
 * This is a "handler" and not a "service" because it does contain its own state and thus should be re-created on
 * every request.
 */
class MicropubHandler extends MicropubAdapter {
	/**
	 * Construct the service.
	 *
	 * @param ApiEnvironment        $env     Current environment.
	 * @param MessageBus            $bus     For sending queries and commands.
	 * @param MicroformatsConverter $mf      Handle converting Smolblog objects to their Microformats counterparts.
	 * @param ContentTypeRegistry   $typeReg For getting content type information.
	 * @param LoggerInterface       $log     Logger for debug info.
	 */
	public function __construct(
		private ApiEnvironment $env,
		private MessageBus $bus,
		private MicroformatsConverter $mf,
		private ContentTypeRegistry $typeReg,
		private LoggerInterface $log,
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
			'siteId' => Identifier::fromString($this->request->getAttribute('smolblogPathVars', [])['site']),
		];
	}

	/**
	 * Get the Micropub configuration for this server.
	 *
	 * @param array $params Raw query parameters.
	 * @return mixed
	 */
	public function configurationQueryCallback(array $params) {
		$currentSiteId = $this->user['siteId'];

		$sites = $this->bus->fetch(new UserSites($this->user['id'])) ?? [];
		$siteChannels = [];

		foreach ($sites as $site) {
			$siteChannels[$site->id->toString()] = $this->bus->fetch(
				new ChannelsForSite(siteId: $site->id, canPush: true)
			) ?? [];
		}

		return [
			'media-endpoint' => $this->env->getApiUrl("/site/$currentSiteId/micropub/media"),
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
				['type' => 'photo', 'name' => 'Picture'],
				['type' => 'multi-photo', 'name' => 'Multiple Pictures'],
			],
			'syndicate-to' => array_map(
				fn($channel) => [
					'uid' => $channel->id->toString(),
					'name' => $channel->displayName,
				],
				array_values($siteChannels[strval($currentSiteId)]),
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

		$res = [
			'properties' => array_filter(
				$props,
				fn($key) => !isset($properties) || in_array($key, $properties),
				ARRAY_FILTER_USE_KEY
			)
		];
		if (!isset($properties)) {
			$res['type'] = ['h-entry'];
		}

		return $res;
	}

	/**
	 * Create new content from the Micropub endpoint.
	 *
	 * @param array $data          Provided content.
	 * @param array $uploadedFiles Uploaded images.
	 * @return mixed
	 */
	public function createCallback(array $data, array $uploadedFiles = []) {
		$this->log->debug(
			message: 'Micropub create ' . date(DateTimeInterface::COOKIE),
			context: ['data' => $data, 'uploadedFiles' => $uploadedFiles],
		);

		if (!in_array('h-entry', $data['type'])) {
			return [
				'error' => 400,
				'error_description' => 'Unsupported type; must be Note, Photo, or Repost.',
			];
		}

		$props = $data['properties'];
		$site = $this->bus->fetch(new SiteById($this->user['siteId']));
		$createCommand = null;
		$publishCommand = null;

		$commonProps = [
			'userId' => $this->user['id'],
			'siteId' => $site->id,
			'contentId' => new DateIdentifier(),
		];

		if (isset($props['repost-of'])) {
			$comment = is_array($props['content'] ?? null) ? $this->getContentFromRequest($props['content']) : null;
			$createCommand = new CreateReblog(
				...$commonProps,
				url: $props['repost-of'][0],
				comment: $comment,
				publish: false,
			);
			$publishCommand = new PublishReblog(...$commonProps);
		} elseif (isset($props['photo']) || !empty($uploadedFiles)) {
			$filesArray = [];
			if (is_array($uploadedFiles) && isset($uploadedFiles['photo'])) {
				$filesArray = is_array($uploadedFiles['photo']) ? $uploadedFiles['photo'] : [$uploadedFiles['photo']];
			}

			$mediaIds = array_merge(
				array_filter(array_map(
					fn($imageProp) => $this->getOrLoadImageFromProp($imageProp, $commonProps)->id,
					$props['photo'] ?? []
				)),
				array_map(
					fn($file) => $this->handleUploadedFile($file, $site->id)->id,
					$filesArray
				),
			);

			$createCommand = new CreatePicture(
				...$commonProps,
				mediaIds: $mediaIds,
				caption: is_array($props['content'] ?? null) ? $this->getContentFromRequest($props['content']) : null,
			);
			$publishCommand = new PublishPicture(...$commonProps);
		} else {
			$createCommand = new CreateNote(
				...$commonProps,
				text: $this->getContentFromRequest($props['content']),
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

		if (!empty($props['mp-syndicate-to'])) {
			$this->bus->dispatch(new SetSyndicationChannels(
				...$commonProps,
				channels: array_map(fn($id) => Identifier::fromString($id), $props['mp-syndicate-to']),
			));
		}

		if (!isset($props['post-status']) || $props['post-status'][0] == 'published') {
			$this->bus->dispatch($publishCommand);
		}

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
		$this->log->debug(
			message: 'Micropub update ' . date(DateTimeInterface::COOKIE),
			context: ['url' => $url, 'actions' => $actions],
		);

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
		$publish = false;
		foreach ($actions as $action => $props) {
			if ($action === 'mp-syndicate-to') {
				$channels = array_values(array_unique(array_merge(
					array_map(fn($id) => Identifier::fromString($id), $props),
					$content->extensions[Syndication::class]?->channels ?? []
				)));

				$commands[] = new SetSyndicationChannels(...$commonProps, channels: $channels);

				continue;
			}

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

			if (is_array($props['syndication'] ?? null)) {
				foreach ($props['syndication'] as $syndLink) {
					$commands[] = new AddSyndicationLink(...$commonProps, url: $syndLink);
				}
			}

			if (
				isset($props['post-status']) &&
				$props['post-status'][0] == 'publish' &&
				$content->visibility !== ContentVisibility::Published
			) {
				$publish = true;
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
			if (in_array('mp-syndicate-to', $deleteThese)) {
				$commands[] = new SetSyndicationChannels(...$commonProps, channels: []);
			}
		}

		if ($tags != $originalTags) {
			$commands[] = new SetTags(...$commonProps, tags: $tags);
		}
		if ($publish) {
			$commands[] = $type === 'reblog' ? new PublishReblog(...$commonProps) : new PublishNote(...$commonProps);
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
		$siteId = $this->user['siteId'];
		$newMedia = $this->handleUploadedFile($file, $siteId);

		return $newMedia->defaultUrl;
	}

	/**
	 * Send an UploadedFileInterface into the system.
	 *
	 * @param UploadedFileInterface $file   File uploaded.
	 * @param Identifier|null       $siteId Site being used.
	 * @return Media
	 */
	private function handleUploadedFile(UploadedFileInterface $file, ?Identifier $siteId = null): Media {
		$siteId ??= $this->user['siteId'];
		$command = new HandleUploadedMedia(
			file: $file,
			userId: $this->user['id'],
			siteId: $siteId,
			accessibilityText: '',
		);

		$this->bus->dispatch($command);

		return $this->bus->fetch(new MediaById(
			siteId: $siteId,
			contentId: $command->contentId,
			userId: $this->user['id'],
		));
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

	/**
	 * Get text content from the content property
	 *
	 * @param array $content Content property.
	 * @return string
	 */
	private function getContentFromRequest(array $content): string {
		if (empty($content)) {
			return '';
		}

		$pieces = array_is_list($content) ? $content : [$content];
		return join("\n\n", array_map(fn($item) => is_array($item) ? $item['text'] ?? $item['html'] : $item, $pieces));
	}

	/**
	 * Get the media object if the URL is in the database or sideload the media if not.
	 *
	 * @param string|array $prop        URL to load or sideload or an array with a 'value' and 'alt' prop.
	 * @param array        $commonProps Common content props.
	 * @return Media
	 */
	private function getOrLoadImageFromProp(string|array $prop, array $commonProps): Media {
		['value' => $url, 'alt' => $alt] = is_array($prop) ? $prop : ['value' => $prop, 'alt' => null];

		$existing = $this->bus->fetch(new MediaByDefaultUrl($url));
		if (isset($existing)) {
			return $existing;
		}

		$contentProps = [
			'userId' => $commonProps['userId'],
			'siteId' => $commonProps['siteId'],
			'contentId' => new DateIdentifier(),
		];

		$this->bus->dispatch(new SideloadMedia(
			...$contentProps,
			url: $url,
			accessibilityText: $alt ?? '',
		));
		return $this->bus->fetch(new MediaById(...$contentProps));
	}
}
