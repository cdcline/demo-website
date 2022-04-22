<?php declare(strict_types=1);

// 1. Autoload files so we can find the things
require_once __DIR__ . '/vendor/autoload.php';

// 2. Setup google error reporting so we can fix things
if (Utils\Server::onLiveSite()) {
   Google\Cloud\ErrorReporting\Bootstrap::init();
}

// 3. Figure out what page to run and do it
Utils\SiteRunner::runPage();

// 4. Make sure we put out all the text we need want to display
flush();

// 5. Exit with no errors
exit(/*success*/0);
