<?php

namespace Smolblog\WP\Helpers;

use Psr\Log\LoggerInterface;
use Smolblog\Core\Federation\SiteByResourceUri;
use Smolblog\Core\Site\{CreateSite, GetSiteKeypair, GetSiteSettings, LinkSiteAndUser, Site, SiteById, SiteSettings, SiteUsers, UpdateSettings, UserCanCreateSites, UserHasPermissionForSite};
use Smolblog\Framework\Infrastructure\KeypairGenerator;
use Smolblog\Framework\Messages\Listener;
use Smolblog\Framework\Objects\{Identifier, Keypair, RandomIdentifier};

class SiteHelper implements Listener {
	public function __construct(private LoggerInterface $log) {
	}

	public function onCreateSite(CreateSite $command) {
		$user_id = UserHelper::UuidToInt($command->userId);

		$site_id = wpmu_create_blog(
			$command->handle . '.smol.blog',
			'/',
			$command->displayName,
			$user_id,
			[],
			get_current_network_id()
		);

		if (is_wp_error( $site_id )) {
			throw new \Exception( $site_id->get_error_message() );
		}

		update_site_meta( $site_id, 'smolblog_site_id', $command->siteId->toString() );
		update_site_meta( $site_id, 'smolblog_site_handle', $command->handle );
	}

	public function onUserCanCreateSites(UserCanCreateSites $query) {
		$query->setResults(true);
	}

	public function onGetSiteSettings(GetSiteSettings $query) {
		$site_id = self::UuidToInt($query->siteId);

		$query->setResults(new SiteSettings(
			siteId: $query->siteId,
			title: get_blog_option( $site_id, 'blogname', '' ),
			tagline: get_blog_option( $site_id, 'blogdescription', '' ),
		));
	}

	public function onLinkSiteAndUser(LinkSiteAndUser $command) {
		$user_id = UserHelper::UuidToInt($command->linkedUserId);
		$site_id = self::UuidToInt($command->siteId);

		$wp_role = 'subscriber';
		if ($command->isAuthor) {
			$wp_role = 'author';
		}
		if ($command->isAdmin) {
			$wp_role = 'administrator';
		}

		add_user_to_blog( $site_id, $user_id, $wp_role );
	}

	public function onSiteById(SiteById $query) {
		$site_id = self::UuidToInt($query->siteId);
		$query->setResults(self::SiteFromWpId($site_id, $query->siteId));
	}

	public function onGetSiteKeypair(GetSiteKeypair $query) {
		$site_id = self::UuidToInt($query->siteId);
		$query->setResults(self::getSiteKeypair($site_id));
	}

	public function onSiteUsers(SiteUsers $query) {
		$site_id = self::UuidToInt($query->siteId);

		$query->setResults(array_map(
			fn($user) => [
				'user' => UserHelper::UserFromWpUser($user),
				'isAdmin' => user_can( $user->ID, 'activate_plugins'),
				'isAuthor' => user_can( $user->ID, 'edit_posts' ),
			],
			get_users( [ 'blog_id' => $site_id, 'role__not_in' => ['subscriber'] ] ),
		));
	}

	public function onUpdateSettings(UpdateSettings $command) {
		$site_id = self::UuidToInt($command->siteId);
		update_blog_option( $site_id, 'blogname', $command->siteName );
		update_blog_option( $site_id, 'blogdescription', $command->siteTagline );
	}

	public function onUserHasPermissionForSite(UserHasPermissionForSite $query) {
		$site_id = self::UuidToInt($query->siteId);
		$user_id = UserHelper::UuidToInt($query->userId);
		switch_to_blog( $site_id );

		$query->setResults(
			(!$query->mustBeAuthor || user_can( $user_id, 'edit_posts' )) &&
			(!$query->mustBeAdmin || user_can( $user_id, 'manage_options'))
		);

		restore_current_blog();
	}

	public function onSiteByResourceUri(SiteByResourceUri $query) {
		global $wpdb;

		$wpid = null;
		$parts = parse_url($query->resource);

		switch ($parts['scheme']) {
			case 'acct':
				$wpid = $this->getDbIdFromActivityPubHandle($parts['path']) ?? $this->getDbIdFromSiteHandle($parts['path']);
				break;

			case 'http':
			case 'https':
				$wpid = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT blog_id FROM $wpdb->blogs WHERE domain LIKE %s",
						$parts['host'],
					)
				);
				break;

			default:
				throw new \Exception('Unknown scheme ' . $parts['scheme'] . "; given $query->resource");
		}

		if (is_int($wpid)) {
			$query->setResults(self::SiteFromWpId($wpid));
		}
	}

	private function getDbIdFromActivityPubHandle(string $handle): ?int {
		global $wpdb;

		$table_name = $wpdb->base_prefix . 'sb_activitypub_handles';
		$siteId = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT site_uuid FROM $table_name WHERE handle = %s",
				$handle,
			)
		);

		if (is_wp_error( $siteId )) {
			$this->log->error('Could not find site for ActivityPub handle ' . $handle, $siteId->get_error_messages());
			return null;
		}

		return isset($siteId) ? self::UuidToInt($siteId) : null;
	}

	/**
	 * This function exists because Mastodon will always do a Webfinger search for the Actor's `preferredUsername` at the
	 * actor's domain. Since Smolblog sites can be hosted on any domain, this function will find the site based on the
	 * handle if the domain is the same as the base domain.
	 */
	private function getDbIdFromSiteHandle(string $handle): ?int {
		global $wpdb;

		$atIndex = strpos($handle, '@');
		if ($atIndex === false) {
			return null;
		}

		$domain = substr($handle, $atIndex + 1);
		$base = get_site( 1 )->domain;
		if ($domain !== $base) {
			return null;
		}

		$slug = substr($handle, 0, $atIndex);
		$siteId = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT blog_id FROM $wpdb->blogmeta WHERE meta_key = 'smolblog_site_handle' AND meta_value LIKE %s",
				$slug
			)
		);

		if (is_wp_error( $siteId )) {
			$this->log->error('Could not find site for site handle ' . $handle, $siteId->get_error_messages());
			return null;
		}

		return $siteId;
	}

	public static function SiteFromWpId(int $site_id, ?Identifier $site_uuid = null): Site {
		$site_uuid ??= self::IntToUuid($site_id);
		$details   = get_blog_details( $site_id );

		return new Site(
			id: $site_uuid,
			handle: get_site_meta( $site_id, 'smolblog_site_handle', true ),
			displayName: $details->blogname,
			baseUrl: $details->home,
			publicKey: self::getSiteKeypair($site_id)->publicKey,
		);
	}

	public static function UuidToInt(Identifier|string $uuid) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT blog_id FROM $wpdb->blogmeta WHERE meta_key = 'smolblog_site_id' AND meta_value = %s",
				strval($uuid)
			)
		);
	}

	public static function IntToUuid(int $dbid) {
		$meta_value = get_site_meta( $dbid, 'smolblog_site_id', true );

		if (empty($meta_value)) {
			// If the site does not have an ID, give it one.
			$new_id = new RandomIdentifier();
			update_site_meta( $dbid, 'smolblog_site_id', $new_id->toString() );

			return $new_id;
		}

		return Identifier::fromString( $meta_value );
	}

	private static function getSiteKeypair(int $dbid): Keypair {
		$meta_value = get_site_meta( $dbid, 'smolblog_keypair', true );

		if (empty($meta_value)) {
			// If the site does not have a keypair, give it one.
			$new_key = (new KeypairGenerator())->generate();
			update_site_meta( $dbid, 'smolblog_keypair', base64_encode( wp_json_encode( $new_key ) ) );

			return $new_key;
		}

		return Keypair::jsonDeserialize( base64_decode( $meta_value ) );
	}
}
