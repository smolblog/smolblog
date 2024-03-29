<?php

namespace Smolblog\Core\Content\Markdown;

use Smolblog\Framework\Messages\Attributes\ContentBuildLayerListener;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Service to provide rendered Markdown on a message that needs it.
 */
class MarkdownMessageRenderer implements Listener {
	/**
	 * Construct the service
	 *
	 * @param SmolblogMarkdown $md Smolblog-flavored Markdown parser.
	 */
	public function __construct(private SmolblogMarkdown $md) {
	}

	/**
	 * Render Markdown on a message.
	 *
	 * @param NeedsMarkdownRendered $message Message with Markdown to process.
	 * @return void
	 */
	#[ContentBuildLayerListener(earlier: 10)]
	public function onNeedsMarkdownRendered(NeedsMarkdownRendered $message) {
		$message->setMarkdownHtml(
			array_map(
				fn($md) => $this->md->parse($md),
				$message->getMarkdown()
			)
		);
	}
}
