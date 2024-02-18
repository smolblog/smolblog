<?php

namespace Smolblog\WP;

use Illuminate\Database\ConnectionInterface;
use wpdb;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Smolblog\Api;
use Smolblog\Core;
use Smolblog\MicroBlog;
use Smolblog\ActivityPub;
use Smolblog\IndieWeb;
use Smolblog\ContentProvenance;
use Smolblog\Tumblr;
use Smolblog\Framework\Infrastructure\AppKit;
use Smolblog\Framework\Infrastructure\DefaultModel;
use Smolblog\Framework\Infrastructure\ServiceRegistry;
use Smolblog\Framework\Messages\MessageBus;
use Smolblog\Framework\Objects\DomainModel;
use Smolblog\WP\Helpers\DatabaseHelper;
use Smolblog\WP\Helpers\DebugEndpoint;

class Smolblog {
	use AppKit;

	public readonly ServiceRegistry $container;
	private array $depMap = [];

	public function __construct( array $plugin_models = [] ) {
		// $this->container = $this->buildDefaultContainer( [
		// 	Core\Model::class,
		// 	Api\Model::class,
		// 	MicroBlog\Model::class,
		// 	ActivityPub\Model::class,
		// 	$this->wordpress_model(),
		// 	...$plugin_models,
		// ] );

		$this->depMap = $this->buildDependencyMap([
			DefaultModel::class,
			Core\Model::class,
			Api\Model::class,
			MicroBlog\Model::class,
			ActivityPub\Model::class,
			IndieWeb\Model::class,
			ContentProvenance\Model::class,
			Tumblr\Model::class,
			$this->wordpress_model(),
			...$plugin_models,
		]);
		$this->depMap[DebugEndpoint::class]['depMap'] = fn() => $this->depMap;

		$this->container = new ServiceRegistry($this->depMap);
	}

	private function wordpress_model(): string {
		$model = new class extends DomainModel {
			public static function getDependencyMap(): array {
				global $wpdb;

				$wpdb->show_errors();
				define( 'DIEONDBERROR', true );

				return [
					Api\ApiEnvironment::class => fn() => new class implements Api\ApiEnvironment {
						public function getApiUrl( string $endpoint = '/' ): string {
							return get_rest_url( null, '/smolblog/v2' . $endpoint );
						}
					},
					ContentProvenance\ContentProvenanceEnvironment::class =>
						fn() => new class implements ContentProvenance\ContentProvenanceEnvironment {
							public function getPathToC2patool(): string {
								return ABSPATH . 'c2patool';
							}
						},
					EndpointRegistrar::class => [ 'container' => ContainerInterface::class ],
					wpdb::class => fn() => $wpdb,
					ConnectionInterface::class => fn() => DatabaseHelper::getLaravelConnection(),
					\Elephox\Mimey\MimeTypesInterface::class => \Elephox\Mimey\MimeTypes::class,
					\Elephox\Mimey\MimeTypes::class => [],

					ClientInterface::class => \GuzzleHttp\Client::class,
					\GuzzleHttp\Client::class => fn() => new \GuzzleHttp\Client(['verify' => false]),

					Core\Connector\Services\AuthRequestStateRepo::class => Helpers\AuthRequestStateHelper::class,
					Core\Content\Types\Reblog\ExternalContentService::class => Helpers\EmbedHelper::class,

					Helpers\PostProjection::class => ['bus' => MessageBus::class, 'log' => LoggerInterface::class],
					Helpers\AsyncHelper::class => [],
					Helpers\AuthRequestStateHelper::class => [],
					Helpers\SiteHelper::class => [],
					Helpers\UserHelper::class => [],
					Helpers\EmbedHelper::class => [],
					Helpers\MediaLibraryHelper::class => ['log' => LoggerInterface::class],
					DebugEndpoint::class => ['depMap' => null, 'db' => ConnectionInterface::class],

					WordPressLogger::class => [],
					// LoggerInterface::class => WordPressLogger::class,
					LoggerInterface::class => \Monolog\Logger::class,
					\Monolog\Logger::class => function() {
						$logger = new \Monolog\Logger('smolblog');
						$logger->pushHandler(new \Monolog\Handler\RotatingFileHandler(
							filename: '/var/www/html/wp-content/logs/sblog',
							maxFiles: 14,
						));
						return $logger;
					}
				];
			}
		};

		return get_class($model);
	}
}
