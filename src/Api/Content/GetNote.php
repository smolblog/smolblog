<?php

namespace Smolblog\Api\Content;

use Smolblog\Api\AuthScope;
use Smolblog\Api\BasicEndpoint;
use Smolblog\Api\EndpointConfig;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Api\Exceptions\NotFound;
use Smolblog\Api\ParameterType;
use Smolblog\Core\Content\Content;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Note\NoteById;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get a particular Note content
 */
class GetNote extends BasicEndpoint {
	/**
	 * Get the endpoint's configuration.
	 *
	 * @return EndpointConfig
	 */
	public static function getConfiguration(): EndpointConfig {
		return new EndpointConfig(
			route: '/site/{site}/content/note/{content}',
			pathVariables: [
				'site' => ParameterType::identifier(),
				'content' => ParameterType::identifier(),
			],
			requiredScopes: [AuthScope::Identified],
		);
	}

	/**
	 * Construct the endpoint.
	 *
	 * @param MessageBus $bus MessageBus to send queries.
	 */
	public function __construct(
		private MessageBus $bus
	) {
	}

	/**
	 * Execute the endpoint.
	 *
	 * @throws BadRequest Given ID is not a Note.
	 * @throws NotFound Given ID is not a visible piece of content.
	 *
	 * @param Identifier|null $userId User making the request.
	 * @param array|null      $params Expects id parameter from path.
	 * @param object|null     $body   Ignored.
	 * @return Content
	 */
	public function run(?Identifier $userId = null, ?array $params = [], ?object $body = null): Content {
		if (
			!$this->bus->fetch(
				new GenericContentById(siteId: $params['site'], contentId: $params['content'], userId: $userId)
			)
		) {
			throw new NotFound('No content exists with that ID.');
		}

		$note = $this->bus->fetch(
			new NoteById(siteId: $params['site'], contentId: $params['content'], userId: $userId)
		);
		if (!$note) {
			throw new BadRequest('Content is not a Note.');
		}

		return $note;
	}
}
