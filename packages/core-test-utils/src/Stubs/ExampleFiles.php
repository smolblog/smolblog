<?php

namespace Smolblog\Core\Test\Stubs;

use Nyholm\Psr7\UploadedFile;

final class ExampleFiles {
	public static function artemisOneRocketVideo(array $overrides = []) {
		// Courtesy NASA: https://images.nasa.gov/details/Artemis%20I%20-%20Rocket%20Camera%20Footage
		return new UploadedFile(
			streamOrFile: $overrides['streamOrFile'] ?? fopen(__DIR__ . '/media/Artemis I - Rocket Camera Footage~small.mp4', 'r'),
			size: $overrides['size'] ?? 6_310_212,
			errorStatus: $overrides['errorStatus'] ?? UPLOAD_ERR_OK,
			clientFilename: $overrides['clientFilename'] ?? 'Artemis I - Rocket Camera Footage~small.mp4',
			clientMediaType: $overrides['clientMediaType'] ?? 'video/mp4'
		);
	}
	public static function artemisTwoEarthsetPicture(array $overrides = []) {
		// Courtesy NASA: https://www.nasa.gov/image-detail/art002e009288/
		return new UploadedFile(
			streamOrFile: $overrides['streamOrFile'] ?? fopen(__DIR__ . '/media/art002e009288orig.jpg', 'r'),
			size: $overrides['size'] ?? 884_095,
			errorStatus: $overrides['errorStatus'] ?? UPLOAD_ERR_OK,
			clientFilename: $overrides['clientFilename'] ?? 'art002e009288orig.jpg',
			clientMediaType: $overrides['clientMediaType'] ?? 'image/jpeg'
		);
	}
}
