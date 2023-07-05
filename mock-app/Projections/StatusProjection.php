<?php

namespace Smolblog\Mock\Projections;

use DateTimeImmutable;
use PDO;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Core\Content\Events\ContentDeleted;
use Smolblog\Core\Content\Types\Note\Note;
use Smolblog\Core\Content\Types\Note\NoteBodyEdited;
use Smolblog\Core\Content\Types\Note\NoteById;
use Smolblog\Core\Content\Types\Note\NoteCreated;
use Smolblog\Core\Content\Types\Note\NoteDeleted;
use Smolblog\Framework\Objects\Identifier;

class NoteProjection {
	public function __construct(private PDO $db) {}

	public function onNoteCreated(NoteCreated $event) {
		$prepared = $this->db->prepare('INSERT INTO notes (content_id, body) VALUES (?, ?)');
		$prepared->execute([$event->contentId->toByteString(), $event->text]);
	}

	public function onNoteBodyEdited(NoteBodyEdited $event) {
		$prepared = $this->db->prepare('UPDATE notes SET body = ? WHERE content_id = ?');
		$prepared->execute([$event->text, $event->contentId->toByteString()]);
	}

	public function onNoteById(NoteById $query) {
		$prepared = $this->db->prepare('SELECT
				notes.body AS "text",
				standard_content.site_id AS "siteId",
				standard_content.author_id AS "authorId",
				standard_content.permalink AS "permalink",
				standard_content.publish_timestamp AS "publishTimestamp",
				standard_content.visibility AS "visibility",
				standard_content.extensions AS "extensions",
				standard_content.body AS "rendered"
			FROM
				notes
				INNER JOIN standard_content ON notes.content_id = standard_content.content_id
			WHERE
				notes.content_id = ?');
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

		$query->setResults(new Note(
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

	public function onContentDeleted(NoteDeleted $event): void {
		$prepared = $this->db->prepare('DELETE FROM notes WHERE content_id = ?');
		$prepared->execute([$event->contentId->toByteString()]);
	}
}
