<?php

namespace Smolblog\WP\Helpers;

use Exception;
use Smolblog\Api\Exceptions\BadRequest;
use Smolblog\Core\User\UpdateProfile;
use WP_User;
use WP_User_Query;
use Smolblog\Core\User\User;
use Smolblog\Core\User\UserById;
use Smolblog\Core\User\UserCanEditProfile;
use Smolblog\Core\User\UserSites;
use Smolblog\Foundation\Service\Messaging\Listener;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

class UserHelper implements Listener {

	public function onUpdateProfile(UpdateProfile $command) {
		$user_id = self::UuidToInt($command->profileId);

		$update_params = array_filter([
			'user_login' => $command->handle,
			'display_name' => $command->displayName,
		]);

		if ($update_params) {
			$response = wp_update_user( [
				'ID' => $user_id,
				...$update_params
			] );

			if ($response != $user_id) {
				throw new Exception($response->get_error_message());
			}
		}

		if (isset($command->pronouns)) {
			update_user_meta( $user_id, 'smolblog_pronouns', $command->pronouns );
		}
	}

	public function onUserById(UserById $query) {
		$user_id = self::UuidToInt($query->userId);
		$query->setResults(self::UserFromWpUser(get_userdata( $user_id )));
	}

	public function onUserCanEditProfile(UserCanEditProfile $query) {
		if ($query->profileId == $query->userId) {
			$query->setResults(true);
			return;
		}

		$query->setResults(user_can( self::UuidToInt($query->userId), 'edit_users' ));
	}

	public function onUserSites(UserSites $query) {
		$user_id = self::UuidToInt($query->userId);

		$query->setResults(array_values(array_map(
			fn($site) => SiteHelper::SiteFromWpId($site->userblog_id),
			array_filter(get_blogs_of_user( $user_id ), function($site) use($user_id) {
				switch_to_blog( $site->userblog_id );
				$can = user_can( $user_id, 'publish_posts' );
				restore_current_blog();
				return $can;
			})
		)));
	}

	public static function UserFromWpUser(WP_User $wp_user): User {
		$user_id = self::IntToUuid( $wp_user->ID );

		return new User(
			id: $user_id,
			handle: $wp_user->user_login,
			displayName: $wp_user->display_name,
			pronouns: $wp_user->smolblog_pronouns ?? '',
			email: $wp_user->user_email,
			features: ['create-site'],
		);
	}

	public static function UuidToInt(Identifier $uuid) {
		global $wpdb;

		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'smolblog_user_id' AND meta_value = %s",
				$uuid->toString()
			)
		);
	}

	public static function IntToUuid(int $dbid) {
		if ($dbid <= 0) {
			return null;
		}

		$meta_value = get_user_meta( $dbid, 'smolblog_user_id', true );

		if (empty($meta_value)) {
			$new_id = new RandomIdentifier();
			update_user_meta( $dbid, 'smolblog_user_id', $new_id->toString() );

			return $new_id;
		}

		return Identifier::fromString( $meta_value );
	}
}
