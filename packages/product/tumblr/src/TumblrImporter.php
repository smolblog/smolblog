<?php

namespace Smolblog\Tumblr;

use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Messages\MessageBus;

//phpcs:disable Generic.Files.LineLength.TooLong

/**
 * Service to import posts from Tumblr.
 */
class TumblrImporter implements Listener {
	/**
	 * Construct the service.
	 *
	 * @param TumblrClientFactory $factory Client for connecting to Tumblr.
	 * @param MessageBus          $bus     Dispatch messages.
	 */
	public function __construct(
		private TumblrClientFactory $factory,
		private MessageBus $bus,
	) {
	}


	/**
	 * Import posts from the given blog.
	 *
	 * @param ImportFromTumblr $command Command to execute.
	 * @return void
	 */
	#[ExecutionLayerListener]
	public function handleCommand(ImportFromTumblr $command) {
		$client = $this->factory->getUserClient(...$command->connection->details);

		$response = $client->getBlogPosts(
			$command->channel->channelKey,
			[
				'reblog_info' => true,
				'npf'         => true,
				'before'      => $command->before ?? time(),
			]
		);

		$last_timestamp  = -1;
		$all_empty       = true;

		foreach ($response->posts as $post) {
			if (! $this->hasBeenImported($post->id)) {
				$this->importPost($post);
				$all_empty         = false;
				$last_timestamp    = $post->timestamp;
			}
		}

		if (!$all_empty) {
			$this->bus->dispatchAsync(ImportFromTumblr::fromArray([
				...$command->toArray(),
				'before' => ( $last_timestamp - 1 ),
			]));
		}
	}

	/**
	 * Check to see if this post is already imported
	 *
	 * @param string $tumblr_id ID of post to check.
	 * @return boolean True if post has been imported.
	 */
	private function hasBeenImported(string $tumblr_id) {
		$check_query = new \WP_Query(
			[
				'meta_key'   => 'smolblog_social_import_id',
				'meta_value' => 'tumblr_' . $tumblr_id,
			]
		);

		return $check_query->found_posts > 0;
	}

	/**
	 * Parse a tumblr post object for use by a CreatePost object
	 *
	 * @param object $post Parsed object from the Tumblr API.
	 * @return array Associative array for CreatePost
	 */
	private function importPost(object $post) {
		$parsed_blocks = $this->parseBlocks($post->content);
		$new_post      = [
			'title'     => $parsed_blocks['title'] ?? null,
			'date'      => wp_date(DATE_RFC3339, $post->timestamp),
			'tags'      => $post->tags,
			'slug'      => $post->slug,
			'status'    => $this->parseState($post->state),
			'excerpt'   => $post->summary,
			'import_id' => "tumblr_$post->id_string",
			'meta'      => [],
			'content'   => $parsed_blocks['content'],
			'reblog'    => $post->reblogged_from_url ?? null,
			'media'     => $parsed_blocks['media'],
		];

		return array_filter($new_post);
	}

	/**
	 * Translate a Tumblr state into a WordPress state
	 *
	 * @param string $state State from the Tumblr API.
	 * @return string State for WordPress
	 */
	private function parseState(string $state) {
		switch (strtolower($state)) {
			case 'queued':
				return 'future';
			case 'draft':
				return 'draft';
			case 'private':
				return 'private';
			case 'published':
				return 'publish';
		}
		return 'draft';
	}

	/**
	 * Translate a given Tumblr format into HTML tags.
	 *
	 * @param object $format Format object from the Tumblr API.
	 * @return array Array of two strings containing the opening and closing HTML tags.
	 */
	private function parseFormatTags(object $format): array {
		// Using the non-semantic tags here because we do not know the semantic meanting.
		switch (strtolower($format->type)) {
			case 'bold':
				return [ '<b>', '</b>' ];
			case 'italic':
				return [ '<i>', '</i>' ];
			case 'strikethrough':
				return [ '<s>', '</s>' ];
			case 'small':
				return [ '<small>', '</small>' ];
			case 'link':
				return [ "<a href=\"$format->url\">", '</a>' ];
			case 'mention':
				return [ "<a href=\"{$format->blog->url}\">", '</a>' ];
			case 'color':
				return [ "<span style=\"color:$format->hex\">", '</span>' ];
		}
		return [ '<span>', '</span>' ];
	}

	/**
	 * Indent level for parsing text blocks.
	 *
	 * @var integer
	 */
	private $indent_level = 0;

	/**
	 * Open indent tags for parsing text blocks.
	 *
	 * @var array of strings
	 */
	private $open_indent_tags = [];

	/**
	 * Currently open indent block
	 *
	 * @var string
	 */
	private $current_indent_block = '';

	/**
	 * Translate Tumblr NPF blocks into WordPress blocks
	 *
	 * @param array $blocks Array of blocks from the Tumblr API.
	 * @return array Three variables: title, content, and media array.
	 */
	private function parseBlocks(array $blocks): array {
		$title  = null;
		$parsed = '';
		$media  = [];

		// Reset parsing vars.
		$this->indent_level = 0;
		$this->open_indent_tags = [];

		foreach ($blocks as $block_index => $block) {
			$this->closeIndentedBlock($block);

			switch (strtolower($block->type)) {
				case 'text':
					if (! $title && isset($block->subtype) && 'heading1' === strtolower($block->subtype)) {
						// If this is the first H1, it's the post's title.
						$title = $block->text;
						break;
					}
					$parsed .= $this->parseTextBlock($block) . "\n\n";
					break;
				case 'image':
					$local_id = count($media);
					$parsed  .= "#SMOLBLOG_MEDIA_IMPORT#{$local_id}#\n\n";
					$media[]  = $this->parseImage($block);
					break;
				case 'link':
					$parsed .= $this->parseLink($block) . "\n\n";
					break;
				case 'audio':
					$block = $this->parseAudio($block);
					if (is_array($block)) {
						$local_id = count($media);
						$parsed  .= "#SMOLBLOG_MEDIA_IMPORT#{$local_id}#\n\n";
						$media[]  = $block;
						break;
					}
					$parsed .= $block . "\n\n";
					break;
				case 'video':
					$block = $this->parseVideo($block);
					if (is_array($block)) {
						$local_id = count($media);
						$parsed  .= "#SMOLBLOG_MEDIA_IMPORT#{$local_id}#\n\n";
						$media[]  = $block;
						break;
					}
					$parsed .= $block . "\n\n";
					break;
			}//end switch
		}//end foreach

		while ($popped = array_pop($this->open_indent_tags)) {
			$parsed .= $popped;
		}

		return [
			'title'   => $title,
			'content' => $parsed,
			'media'   => $media,
		];
	}

	/**
	 * Translate a Tumblr text block into a WordPress block
	 *
	 * @param object $block Block object from the Tumblr API.
	 * @return string WordPress block for a Block Editor (Gutenberg) post.
	 */
	private function parseTextBlock(object $block): string {
		$block_text = $this->parseTextFormatting($block);

		if (! isset($block->subtype)) {
			return "<!-- wp:paragraph -->\n<p>$block_text</p>\n<!-- /wp:paragraph -->";
		}
		switch (strtolower($block->subtype)) {
			case 'heading1':
				return "<!-- wp:heading {\"level\":1} -->\n<h1>$block_text</h1>\n<!-- /wp:heading -->";
			case 'heading2':
				return "<!-- wp:heading -->\n<h2>$block_text</h2>\n<!-- /wp:heading -->";
			case 'quirky':
				return "<!-- wp:paragraph -->\n<p>$block_text</p>\n<!-- /wp:paragraph -->";
			case 'quote':
				return "<!-- wp:pullquote -->\n<figure class=\"wp-block-pullquote\"><blockquote><p>$block_text</p></blockquote></figure>\n<!-- /wp:pullquote -->";
			case 'chat':
				return "<!-- wp:code -->\n<pre class=\"wp-block-code\"><code>$block_text</code></pre>\n<!-- /wp:code -->";
			case 'indented':
			case 'ordered-list-item':
			case 'unordered-list-item':
				return $this->openIndentedBlock($block, $block_text);
		}
		return "<!-- wp:paragraph -->\n<p>$block_text</p>\n<!-- /wp:paragraph -->";
	}

	/**
	 * Special processing for indentable blocks
	 *
	 * @param object $block      Block object from the Tumblr API.
	 * @param string $block_text Formatted text for this block.
	 * @return string Text for this block.
	 */
	private function openIndentedBlock(object $block, string $block_text): string {
		$output = '';
		$this_block = strtolower($block->subtype);
		$this_indent = $block->indent_level ?? 0;
		if (empty($this->open_indent_tags)) {
			switch ($this_block) {
				case 'indented':
					$output = "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\">\n";
					array_push($this->open_indent_tags, "</blockquote>\n<!-- /wp:quote -->");
					break;
				case 'ordered-list-item':
					$output = "<!-- wp:list {\"ordered\":true} -->\n<ol>\n";
					array_push($this->open_indent_tags, "</ol>\n<!-- /wp:list -->");
					break;
				case 'unordered-list-item':
					$output = "<!-- wp:list -->\n<ul>\n";
					array_push($this->open_indent_tags, "</ul>\n<!-- /wp:list -->");
					break;
			}
			$this->current_indent_block = $this_block;
		}

		if ($this_indent > $this->indent_level) {
			switch ($this_block) {
				case 'indented':
					$output .= "<blockquote class=\"wp-block-quote\">\n";
					array_push($this->open_indent_tags, "</blockquote>\n");
					break;
				case 'ordered-list-item':
					$output .= "<ol>\n";
					array_push($this->open_indent_tags, "</ol>\n");
					break;
				case 'unordered-list-item':
					$output .= "<ul>\n";
					array_push($this->open_indent_tags, "</ul>\n");
					break;
			}
			$this->indent_level = $this_indent;
		}

		switch ($this_block) {
			case 'indented':
				$output .= "<p>$block_text</p>\n";
				break;
			case 'ordered-list-item':
			case 'unordered-list-item':
				$output .= "<li>$block_text</li>\n";
				break;
		}

		return $output;
	}

	/**
	 * Finish processing indented blocks.
	 *
	 * @param object $block Block to process.
	 * @return string
	 */
	private function closeIndentedBlock(object $block): string {
		$output = '';
		$this_block = strtolower($block->subtype ?? '');
		$this_indent = $block->indent_level ?? 0;

		if ($this_indent < $this->indent_level) {
			$this->indent_level -= 1;
			$output .= array_pop($this->open_indent_tags) . $this->closeIndentedBlock($block);
		}

		if ($this_indent === 0 && $this_block !== $this->current_indent_block) {
			$output .= array_pop($this->open_indent_tags);
		}

		return $output;
	}

	/**
	 * Apply any formatting objects to the block's text as HTML tags
	 *
	 * @param object $block Block object from the Tumblr API.
	 * @return string HTML-formatted text from the block.
	 */
	private function parseTextFormatting(object $block): string {
		if (! isset($block->formatting) || ! is_array($block->formatting)) {
			return $block->text;
		}

		$inserts = [];
		foreach ($block->formatting as $format) {
			[$open_tag, $close_tag] = $this->parseFormatTags($format);

			$existing_open_tag         = $inserts[ $format->start ] ?? '';
			$inserts[ $format->start ] = $existing_open_tag . $open_tag;

			$existing_close_tag      = $inserts[ $format->end ] ?? '';
			$inserts[ $format->end ] = $close_tag . $existing_close_tag;
		}

		$formatted_text = '';
		$cursor         = 0;
		$stops          = array_keys($inserts);

		foreach ($stops as $stop) {
			$formatted_text .= mb_substr($block->text, $cursor, ( $stop - $cursor ));
			$formatted_text .= $inserts[ $stop ];
			$cursor          = $stop;
		}
		if ($cursor < mb_strlen($block->text)) {
			$formatted_text .= mb_substr($block->text, $cursor);
		}

		return $formatted_text;
	}

	/**
	 * Translate an image block into a WordPress block.
	 *
	 * @param object $block Block object from the Tumblr API.
	 * @return array Image data to be given to CreatePost
	 */
	private function parseImage(object $block): array {
		$img_size = -1;
		$img_url  = '#';

		foreach ($block->media as $img_info) {
			if ($img_size < $img_info->width || $img_size < $img_info->height) {
				$img_url  = $img_info->url;
				$img_size = ( $img_info->width > $img_info->height ) ? $img_info->width : $img_info->height;
			}
		}

		return [
			'type'    => 'image',
			'url'     => $img_url,
			'alt'     => $block->alt_text ?? 'Image from tumblr',
			'caption' => $block->caption ?? null,
		];
	}

	/**
	 * Translate a link block into a WordPress block
	 *
	 * @param object $block Block object from the Tumblr API.
	 * @return string Block for a WordPress post.
	 */
	private function parseLink(object $block): string {
		return "<!-- wp:heading -->\n<h2><a href=\"$block->url\" data-type=\"URL\" data-id=\"$block->url\">$block->title</a></h2>\n<!-- /wp:heading -->";
	}

	/**
	 * Translate an audio block into a WordPress block. Native Tumblr audio is marked for sideloading.
	 * Off-site audio (soundcloud, spotify) is embedded with oEmbed.
	 *
	 * @param object $block Block object from the Tumblr API.
	 * @return string|array String for embedded audio, array for sideloaded audio
	 */
	private function parseAudio(object $block) {
		if (isset($block->provider) && $block->provider === 'tumblr') {
			return [
				'type' => 'audio',
				'url'  => $block->media->url,
			];
		}

		return '<!-- wp:embed {"url":"' . $block->url . '","type":"rich","className":""} -->
		<figure class="wp-block-embed is-type-rich"><div class="wp-block-embed__wrapper">
		' . $block->url . '
		</div></figure>
		<!-- /wp:embed -->';
	}

	/**
	 * Translate an video block into a WordPress block. Native Tumblr video is marked for sideloading.
	 * Off-site video (YouTube, Vimeo) is embedded with oEmbed.
	 *
	 * @param object $block Block object from the Tumblr API.
	 * @return string|array String for embedded video, array for sideloaded video
	 */
	private function parseVideo(object $block) {
		if (isset($block->provider) && $block->provider === 'tumblr') {
			return [
				'type' => 'video',
				'url'  => $block->media->url,
			];
		}

		return '<!-- wp:embed {"url":"' . $block->url . '","type":"rich","className":""} -->
		<figure class="wp-block-embed is-type-rich"><div class="wp-block-embed__wrapper">
		' . $block->url . '
		</div></figure>
		<!-- /wp:embed -->';
	}
}
