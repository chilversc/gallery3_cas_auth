<?php defined("SYSPATH") or die("No direct script access.");

class cas_auth_event
{
	static function system_ready()
	{
	}

	static function gallery_ready()
	{
		if (!cas_auth::is_enabled())
			return;
		self::_init_cas();
		phpCAS::checkAuthentication();
		self::_sign_in_user_from_cas();
	}

	static function user_menu($menu, $theme)
	{
		if (!cas_auth::is_enabled())
			return;

		$menu->remove("user_menu_login");
		$menu->remove("user_menu_logout");

		$user = identity::active_user();

		if ($user->guest) {
			$menu->append(Menu::factory("link")
				->id("user_menu_login")
				->css_id("g-login-link")
				->url(url::site("cas/login"))
				->label(t("Login")));
		} else {
			$csrf = access::csrf_token();
			$menu->append(Menu::factory("link")
				->id("user_menu_logout")
				->css_id("g-logout-link")
				->url(url::site("cas/logout?csrf=$csrf"))
				->label(t("Logout")));
		}
	}

	static function admin_menu($menu, $theme)
	{
		$menu->get("settings_menu")
			->append(Menu::factory("link")
				->id("cas_settings")
				->label(t("CAS settings"))
				->url(url::site("admin/cas_settings")));
	}


	static function user_logout($user)
	{
		if (!cas_auth::is_enabled())
			return;
		phpCAS::logout();
	}


	static function post_authenticate_callback()
	{
		self::_sign_in_user_from_cas();
	}


	private static function _init_cas()
	{
		require_once Kohana::find_file("vendor", "phpCAS/CAS");

		$settings = cas_auth::get_settings();

		phpCAS::client(SAML_VERSION_1_1, $settings->host, $settings->port, $settings->context, true);

		// TODO: replace this with setCasServerCACert
		phpCAS::setNoCasServerValidation();

		phpCAS::setPostAuthenticateCallback(array("cas_auth_event", "post_authenticate_callback"));
        phpCAS::setSessionHandler(new CAS_Gallery_Session_Adapter());

		phpCAS::handleLogoutRequests();
	}

	private static function _sign_in_user_from_cas()
	{
		if (!phpCAS::isAuthenticated())
			return;

		$cas_user_name = phpCAS::getUser();
		$user = identity::active_user();

		if ($user->guest || $user->name != $cas_user_name) {
			$new_user = identity::lookup_user_by_name($cas_user_name);
			if ($new_user == null) {
				Kohana_Log::add("info", "Could not authenticate user '$cas_user_name': No matching user found in gallery database");
			}

			try {
				identity::set_active_user($new_user);
			} catch(Exception $e) {
				Kohana_Log::add("error", "Couldn't authenticate as $cas_user_name: " .
					$e->getMessage() . "\n" . $e->getTraceAsString());
			}
		}
	}
}
