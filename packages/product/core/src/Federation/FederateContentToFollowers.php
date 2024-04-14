<?php

namespace Smolblog\Core\Federation;

use Smolblog\Core\ContentV1\Content;
use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Async command to federate the given content to the given followers through the given provider.
 */
readonly class FederateContentToFollowers extends Command {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidCommandParametersException When $followers is empty or has incorrect followers.
	 *
	 * @param Content $content   Content to federate.
	 * @param array   $followers Followers to federate to.
	 * @param string  $provider  Provider to use.
	 */
	public function __construct(
		public readonly Content $content,
		public readonly array $followers,
		public readonly string $provider,
	) {
		if (empty($followers)) {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'At least one follower must be given.'
			);
		}
		if (!empty(array_filter($followers, fn($fw) => $fw->provider !== $provider))) {
			throw new InvalidCommandParametersException(
				command: $this,
				message: 'Followers must match given provider.'
			);
		}
	}

	/**
	 * Serialize the object.
	 *
	 * @return array
	 */
	public function serializeValue(): array {
		return [
			'content' => $this->content->serializeValue(),
			'followers' => array_map(fn($fl) => $fl->serializeValue(), $this->followers),
			'provider' => $this->provider,
		];
	}

	/**
	 * Deserialize the object.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		return new FederateContentToFollowers(
			content: Content::deserializeValue($data['content']),
			followers: array_map(fn($fl) => Follower::deserializeValue($fl), $data['followers']),
			provider: $data['provider'],
		);
	}
}
