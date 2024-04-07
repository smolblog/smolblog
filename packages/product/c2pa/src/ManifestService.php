<?php

namespace Smolblog\ContentProvenance;

use smolblog\Framework\Messages\Listener;
use Psr\Log\LoggerInterface;
use Smolblog\ContentProvenance\Actions\Published;
use Smolblog\Core\ContentV1\Media\HandleUploadedMedia;
use Smolblog\Core\ContentV1\Media\MediaService;
use Smolblog\Core\ContentV1\Media\MediaType;
use Smolblog\Framework\Messages\Attributes\ExecutionLayerListener;
use Elephox\Mimey\MimeTypesInterface;

/**
 * Service that intercepts an uploaded file command and attaches a manifest to it.
 */
class ManifestService implements Listener {
	/**
	 * Create the service.
	 *
	 * @param ContentProvenanceEnvironment $env   Environment with the c2patool path.
	 * @param MimeTypesInterface           $mimes MIME-file extension converter.
	 * @param LoggerInterface              $logs  PSR-3 logger.
	 */
	public function __construct(
		private ContentProvenanceEnvironment $env,
		private MimeTypesInterface $mimes,
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

		$this->logs->debug("ManifestService::applyManifest", [
			'command' => "$toolPath $pathToMedia --config '$manifestJson' --output $pathToMedia --force",
		]);

		$output = [];
		$resultCode = 0;
		$result = exec(
			"$toolPath $pathToMedia --config '$manifestJson' --output $pathToMedia --force",
			$output,
			$resultCode
		);

		if (!$result) {
			$this->logs->error("Could not apply manifest", [
				'result_code' => $resultCode,
				'output' => $output,
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
		$this->logs->debug('ManifestService::onHandleUploadedMedia', [
			'command' => $command->toArray(),
			'Media type' => $command->file->getClientMediaType(),
			'Expected file' => $command->file->getStream()->getMetadata('uri'),
		]);

		if (MediaService::typeFromMimeType($command->file->getClientMediaType() ?? '') === MediaType::File) {
			return;
		}

		$fileName = $command->file->getClientFilename();
		$oldPath = $command->file->getStream()->getMetadata('uri');
		$newPath = isset($fileName) ? "/tmp/$fileName" : $oldPath . '.' .
			$this->mimes->getExtension($command->file->getClientMediaType() ?? mime_content_type($oldPath));
		shell_exec("mv $oldPath $newPath");

		$this->applyManifest(
			manifest: new ProvenanceManifest(actions: [new Published()]),
			pathToMedia: $newPath,
		);

		shell_exec("mv $newPath $oldPath");
	}
}
