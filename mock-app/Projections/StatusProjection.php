<?php

namespace Smolblog\Mock\Projections;

use DateTimeImmutable;
use PDO;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Types\Status\Status;
use Smolblog\Core\Content\Types\Status\StatusBodyEdited;
use Smolblog\Core\Content\Types\Status\StatusById;
use Smolblog\Core\Content\Types\Status\StatusCreated;
use Smolblog\Core\Content\Types\Status\StatusDeleted;
use Smolblog\Framework\Objects\Identifier;

class StatusProjection {
	public function __construct(private PDO $db) {}

	public function onStatusCreated(StatusCreated $event) {
		$prepared = $this->db->prepare('INSERT INTO statuses (content_id, body) VALUES (?, ?)');
		$prepared->execute([$event->contentId->toByteString(), $event->text]);
	}

	public function onStatusBodyEdited(StatusBodyEdited $event) {
		$prepared = $this->db->prepare('UPDATE statuses SET body = ? WHERE content_id = ?');
		$prepared->execute([$event->text, $event->contentId->toByteString()]);
	}

	public function onStatusById(StatusById $query) {
		$prepared = $this->db->prepare('SELECT
				statuses.body AS "text",
				standard_content.site_id AS "siteId",
				standard_content.author_id AS "authorId",
				standard_content.permalink AS "permalink",
				standard_content.publish_timestamp AS "publishTimestamp",
				standard_content.visibility AS "visibility",
				standard_content.extensions AS "extensions",
				standard_content.body AS "rendered"
			FROM
				statuses
				INNER JOIN standard_content ON statuses.content_id = standard_content.content_id
			WHERE
				statuses.content_id = ?');
		$prepared->execute([$query->id->toByteString()]);
		$results = $prepared->fetch(mode: PDO::FETCH_ASSOC);
		if (empty($results)) {
			$query->setResults(null);
			return;
		}

		$extArray = json_decode($results['extensions'] ?? '{}', associative: true);
		$extParsed = [];
		foreach ($extArray as $class => $data) {
			$extParsed[$class] = $class::fromArray($data);
		}

		$query->setResults(new Status(
			text: $results['text'],
			siteId: Identifier::fromByteString($results['siteId']),
			authorId: Identifier::fromByteString($results['authorId']),
			permalink: $results['permalink'] ?? null,
			publishTimestamp: $results['publishTimestamp'] ? new DateTimeImmutable($results['publishTimestamp']) : null,
			visibility: ContentVisibility::tryFrom($results['visibility'] ?? ''),
			id: $query->id,
			extensions: $extParsed,
			rendered: $results['rendered'],
		));
	}

	public function onContentDeleted(StatusDeleted $event): void {
		$prepared = $this->db->prepare('DELETE FROM statuses WHERE content_id = ?');
		$prepared->execute([$event->contentId->toByteString()]);
	}
}
