<?php

namespace Smolblog\Core\Test\Stubs;

use Nyholm\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

final class TestUploadedFileInterface implements UploadedFileInterface {
	public function getStream(): StreamInterface {
		return new Stream('');
	}
	public function moveTo(string $targetPath): void {}
	public function getSize(): ?int {
		return null;
	}
	public function getError(): int {
		return \UPLOAD_ERR_OK;
	}
	public function getClientFilename(): ?string {
		return null;
	}
	public function getClientMediaType(): ?string {
		return null;
	}
}
