<?php

namespace Smolblog\ActivityPub;

use DateTimeInterface;
use Smolblog\ActivityPhp\Type\Core\ObjectType;
use Smolblog\ActivityPhp\Type\Extended\Object\Note;
use Smolblog\Api\ApiEnvironment;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Site\Site;
use Smolblog\Markdown\SmolblogMarkdown;

/**
 * Convert Smolblog objects to ActivityTypes objects.
 */
class ActivityTypesConverter {
	/**
	 * Construct the service.
	 *
	 * @param SmolblogMarkdown $md  Markdown parser.
	 * @param ApiEnvironment   $env API Environment for creating links.
	 */
	public function __construct(
		private SmolblogMarkdown $md,
		private ApiEnvironment $env,
	) {
	}

	/**
	 * Convert content to an activity object.
	 *
	 * Currently sends everything as a "Note" with links until I can find some better documentation.
	 *
	 * @param Content   $content Content to convert.
	 * @param Site|null $site    Optionally provide a site object for creating permalinks.
	 * @return ObjectType
	 */
	public function activityObjectFromContent(Content $content, ?Site $site): ObjectType {
		$apObject = new ObjectType();
		switch ($content->type->getTypeKey()) {
			case 'note':
				$apObject = new Note();
				$apObject->content = $this->md->parse($content->type->text);
				$apObject->mediaType = 'text/html';
				$apObject->source = new ObjectType();
				$apObject->source->content = $content->type->text;
				$apObject->source->mediaType = 'text/markdown';
				break;

			// TODO: Reverse-engineer Mastodon's reblog.
			case 'reblog':
				$generatedMarkdown = ':[' . $content->type->getTitle() .
					'](' . $content->type->url . ")\n\n" .
					($content->type->comment ?? '');

				$apObject = new Note();
				$apObject->content = $this->md->parse($generatedMarkdown);
				$apObject->mediaType = 'text/html';
				$apObject->source = new ObjectType();
				$apObject->source->content = $generatedMarkdown;
				$apObject->source->mediaType = 'text/markdown';
				break;

			case 'picture':
				$permalink = rtrim($site?->baseUrl ?? '', '/') . $content->permalink;
				$generatedMarkdown = (isset($content->type->caption) ? $content->type->caption . "\n\n" : '') .
					"[View picture]($permalink)";

				$apObject = new Note();
				$apObject->content = $this->md->parse($generatedMarkdown);
				$apObject->mediaType = 'text/html';
				$apObject->source = new ObjectType();
				$apObject->source->content = $generatedMarkdown;
				$apObject->source->mediaType = 'text/markdown';
				break;

			default:
				$permalink = rtrim($site?->baseUrl ?? '', '/') . $content->permalink;
				$generatedMarkdown = "[{$content->type->getTitle()}]($permalink)";

				$apObject = new Note();
				$apObject->content = $this->md->parse($generatedMarkdown);
				$apObject->mediaType = 'text/html';
				$apObject->source = new ObjectType();
				$apObject->source->content = $generatedMarkdown;
				$apObject->source->mediaType = 'text/markdown';
				break;
		}//end switch

		$apObject->id = $this->env->getApiUrl("/site/$content->siteId/activitypub/content/$content->id");
		$apObject->published = $content->publishTimestamp->format(DateTimeInterface::W3C);
		$apObject->attributedTo = $this->env->getApiUrl("/site/$content->siteId/activitypub/actor");
		$apObject->to = 'https://www.w3.org/ns/activitystreams#Public';

		return $apObject;
	}
}
