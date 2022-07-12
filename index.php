<?php declare(strict_types=1);

// 1. Setup Autoload and define objects we'll use in the file
require_once __DIR__ . '/vendor/autoload.php';

use Google\Cloud\ErrorReporting\Bootstrap;
use Utils\ServerUtils;
use Utils\SiteRunner;

// 2. Setup google error reporting so we can fix things
if (ServerUtils::shouldLoadGoogleTools()) {
   Bootstrap::init();
}

// 3. Figure out what page to run and do it
SiteRunner::runPage();

// 4. Make sure we put out all the text we need want to display
flush();

// 5. Exit with no errors
exit(/*success*/0);
