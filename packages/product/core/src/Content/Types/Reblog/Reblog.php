<?php

namespace Smolblog\Core\Content\Types\Reblog;

use Smolblog\Core\Content\ContentType;
use Smolblog\Framework\Objects\SerializableKit;

/**
 * For reblogging interesting things from around the web.
 */
class Reblog implements ContentType {
	use SerializableKit;

	/**
	 * Undocumented function
	 *
	 * @param string                   $url         URL being reblogged.
	 * @param string|null              $comment     Optional markdown-formatted comment on the remote content.
	 * @param ExternalContentInfo|null $info        Fetched info from the external URL.
	 * @param string|null              $commentHtml The HTML-formatted optional comment.
	 */
	public function __construct(
		public readonly string $url,
		public readonly ?string $comment = null,
		private ?ExternalContentInfo $info = null,
		private ?string $commentHtml = null,
	) {
	}

	/**
	 * Get the title of this content.
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->info->title;
	}

	/**
	 * Get the rendered HTML of this content.
	 *
	 * @return string
	 */
	public function getBodyContent(): string {
		return $this->info->embed . "\n\n" . $this->commentHtml;
	}

	/**
	 * Get the type key ('reblog').
	 *
	 * @return string
	 */
	public function getTypeKey(): string {
		return 'reblog';
	}

	/**
	 * Set the info for the external URL
	 *
	 * @param ExternalContentInfo $info Fetched info.
	 * @return void
	 */
	public function setExternalInfo(ExternalContentInfo $info): void {
		$this->info = $info;
	}

	/**
	 * Store the rendered markdown for any additional comment.
	 *
	 * @param string $html Rendered markdown.
	 * @return void
	 */
	public function setCommentHtml(string $html): void {
		$this->commentHtml = $html;
	}

	/**
	 * Get the HTML for just the comment if it exists.
	 *
	 * @return string|null
	 */
	public function getCommentHtml(): ?string {
		return $this->commentHtml ?? null;
	}

	/**
	 * Deserialize the object.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		return new Reblog(
			url: $data['url'],
			comment: $data['comment'] ?? null,
			info: isset($data['info']) ? ExternalContentInfo::fromArray($data['info']) : null,
			commentHtml: $data['commentHtml'] ?? null,
		);
	}
}
