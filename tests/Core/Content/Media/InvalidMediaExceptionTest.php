<?php

namespace Smolblog\Core\Content\Media;

use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Test\TestCase;

final class InvalidMediaExceptionTest extends TestCase {
	public function testItCanBeThrown() {
		$this->expectException(InvalidMediaException::class);

		throw new InvalidMediaException("Can't let you do that, Star Fox...");
	}

	public function testItCanBeThrownWithAUrl() {
		$this->expectException(InvalidMediaException::class);

		throw new InvalidMediaException(
			"Can't let you do that, Star Fox...",
			url: '//cdn.smol.blog/sf64.gif',
		);
	}

	public function testItCanBeThrownWithAFile() {
		$this->expectException(InvalidMediaException::class);

		throw new InvalidMediaException(
			"Can't let you do that, Star Fox...",
			upload: $this->createStub(UploadedFileInterface::class),
		);
	}

	public function testItCanBeThrownWithAPreviousException() {
		$this->expectException(InvalidMediaException::class);

		throw new InvalidMediaException(
			"Can't let you do that, Star Fox...",
			url: '//cdn.smol.blog/sf64.gif',
			previous: new Exception(),
		);
	}
}
