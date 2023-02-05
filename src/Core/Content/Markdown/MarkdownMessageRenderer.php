<?php

namespace Smolblog\Core\Content\Markdown;

use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Service to provide rendered Markdown on a message that needs it.
 */
class MarkdownMessageRenderer {
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
	#[ExecutionLayerListener(later: 1)]
	public function onNeedsMarkdownRendered(NeedsMarkdownRendered $message) {
		$message->setHtml($this->md->parse($message->getMarkdown()));
	}
}
