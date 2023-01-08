<?php

namespace Smolblog\Core\Content;

use DateTimeInterface;
use Smolblog\Framework\Objects\EntityKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Standard Content object
 *
 * Every CMS needs to be able to handle different types of content. The approach Smolblog has chosen to take is
 * to allow each content type to define its own events and projections so long as they also project the content
 * into the Standard Content model shown here.
 *
 * The Standard Content model is a *projection*. The canonical data for all content types is and will always be
 * the events. Not all data has to be present here, just enough for other parts of the system to interact with
 * the content in a standard fashion. This is not the final state of the data; this is a lowest common
 * denominator.
 */
class Content {
	use EntityKit;

	/**
	 * Unique identifier for this piece of content.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $id;

	/**
	 * Date associated with the content (usually the publish date).
	 *
	 * @var DateTimeInterface
	 */
	public readonly DateTimeInterface $publishDate;

	/**
	 * ID of the content author.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $authorId;

	/**
	 * Server-relative URL of the content.
	 *
	 * @var string
	 */
	public readonly string $permalink;

	/**
	 * Title of the content.
	 *
	 * @var string
	 */
	public readonly string $title;

	/**
	 * HTML content.
	 *
	 * @var string
	 */
	public readonly string $content;

	/**
	 * Text-only description or excerpt of the content.
	 *
	 * @var string
	 */
	public readonly string $description;

	/**
	 * Visibility of the content.
	 *
	 * @var ContentVisiblity
	 */
	public readonly ContentVisibility $status;

	/**
	 * Array of URLs this content has been syndicated to (other social networks/sites).
	 *
	 * @var string[]
	 */
	public readonly array $syndicationUrls;

	/**
	 * Keywords for the post.
	 *
	 * @var string[]
	 */
	public readonly array $keywords;

	/**
	 * Arbitrary extra attributes.
	 *
	 * This is not for content; that should be projected directly into $content. This is for arbitrary extra
	 * information that may be needed by other processes.
	 *
	 * @var string[]
	 */
	public readonly array $attributes;

	/**
	 * Undocumented function
	 *
	 * @param Identifier        $id              Unique identifier for this piece of content.
	 * @param DateTimeInterface $publishDate     Date associated with the content (usually the publish date).
	 * @param Identifier        $authorId        ID of the content author.
	 * @param string            $permalink       Server-relative URL of the content.
	 * @param string            $title           Title of the content.
	 * @param string            $content         HTML content.
	 * @param string            $description     Text-only description or excerpt of the content.
	 * @param ContentVisibility $status          Visibility of the content.
	 * @param array             $syndicationUrls Array of URLs this content has been syndicated to.
	 * @param array             $keywords        Keywords for the post.
	 * @param array             $attributes      Arbitrary extra attributes.
	 */
	public function __construct(
		Identifier $id,
		DateTimeInterface $publishDate,
		Identifier $authorId,
		string $permalink,
		string $title,
		string $content,
		string $description,
		ContentVisibility $status,
		array $syndicationUrls,
		array $keywords,
		array $attributes,
	) {
		$this->id = $id;
		$this->publishDate = $publishDate;
		$this->authorId = $authorId;
		$this->permalink = $permalink;
		$this->title = $title;
		$this->content = $content;
		$this->description = $description;
		$this->status = $status;
		$this->syndicationUrls = $syndicationUrls;
		$this->keywords = $keywords;
		$this->attributes = $attributes;
	}
}
