<?php

class CAS_Controller extends Controller
{
	function login()
	{
		phpCAS::forceAuthentication();
		url::redirect(item::root()->abs_url());
	}

	function logout()
	{
		$user = identity_Core::active_user();
		if (!$user->guest) {
			access::verify_csrf();
			auth::logout();
		}
		url::redirect(item::root()->abs_url());
	}
}
