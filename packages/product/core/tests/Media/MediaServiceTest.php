<?php

namespace Smolblog\Core\Media\Services;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Test\MediaTestBase;

final class MediaServiceTest extends MediaTestBase {
	public function testMediaTypeFromMimeType() {
		$this->assertEquals(MediaType::Audio, MediaService::typeFromMimeType('audio/mpeg'));
		$this->assertEquals(MediaType::Video, MediaService::typeFromMimeType('video/mp4'));
		$this->assertEquals(MediaType::Image, MediaService::typeFromMimeType('image/png'));
		$this->assertEquals(MediaType::File, MediaService::typeFromMimeType('application/pdf'));
		$this->assertEquals(MediaType::File, MediaService::typeFromMimeType('markdown'));
	}

	public function testUserCannotEditNonexistentMedia() {
		$this->contentRepo->method('mediaById')->willReturn(null);

		$this->assertFalse(
			$this->app->container->get(MediaService::class)->
				userCanEditMedia(userId: $this->randomId(), mediaId: $this->randomId())
		);
	}
}
