<?php

// Backwards-compatible shim for legacy seeder path used by older test bootstrap.
// This file proxies to the namespaced seeder class located in database/seeders
// when running under modern Laravel. It avoids syntax errors when tests try to
// include the legacy file.

if (! class_exists('\Database\Seeders\TestUsersTableSeeder')) {
	// If the namespaced seeder isn't available, define a no-op class so older
	// calls to the legacy seeder won't break tests.
	class TestUsersTableSeeder
	{
		public function run()
		{
			// Intentionally empty; the namespaced seeder will be used when
			// available via the TestCase bootstrap.
		}
	}
} else {
	// If the namespaced seeder exists, provide a tiny shim function that will
	// instantiate and run it for legacy callers that expect this file.
	$shim = function () {
		$s = new \Database\Seeders\TestUsersTableSeeder();
		$s->run();
	};
	// Execute the shim immediately to mimic legacy behaviour when included.
	$shim();
}


