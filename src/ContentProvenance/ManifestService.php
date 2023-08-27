<?php

namespace Smolblog\ContentProvenance;

use Crell\Tukio\Listener;
use Psr\Log\LoggerInterface;
use Smolblog\ContentProvenance\Actions\Published;
use Smolblog\Core\Content\Media\HandleUploadedMedia;
use Smolblog\Core\Content\Media\MediaService;
use Smolblog\Core\Content\Media\MediaType;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;

/**
 * Service that intercepts an uploaded file command and attaches a manifest to it.
 */
class ManifestService extends Listener {
	/**
	 * Create the service.
	 *
	 * @param ContentProvenanceEnvironment $env  Environment with the c2patool path.
	 * @param LoggerInterface              $logs PSR-3 logger.
	 */
	public function __construct(
		private ContentProvenanceEnvironment $env,
		private LoggerInterface $logs,
	) {
	}

	/**
	 * Apply the given Manifest to the media file at the given path.
	 *
	 * @param ProvenanceManifest $manifest    Manifest to apply.
	 * @param string             $pathToMedia Path to the media file to apply the manifest to.
	 * @return void
	 */
	public function applyManifest(ProvenanceManifest $manifest, string $pathToMedia) {
		$toolPath = $this->env->getPathToC2patool();
		$manifestJson = str_replace("'", "\\'", json_encode($manifest));
		$result = shell_exec("$toolPath '$pathToMedia' --config '$manifestJson' --output '$pathToMedia' --force");

		if (!$result) {
			$this->logs->error("Could not apply manifest", [
				'manifest' => $manifest->toArray(),
				'pathToMedia' => $pathToMedia,
			]);
		}
	}

	/**
	 * Apply a Published manifest to an uploaded file.
	 *
	 * This edits the temporary file in-place so further services can handle the file in their own ways.
	 *
	 * @param HandleUploadedMedia $command Command to execute.
	 * @return void
	 */
	#[ExecutionLayerListener(earlier: 5)]
	public function onHandleUploadedMedia(HandleUploadedMedia $command) {
		if (MediaService::typeFromMimeType($command->file->getClientMediaType() ?? '') === MediaType::File) {
			return;
		}

		$this->applyManifest(
			manifest: new ProvenanceManifest(actions: [new Published()]),
			pathToMedia: $command->file->getStream()->getMetadata('uri'),
		);
	}
}
