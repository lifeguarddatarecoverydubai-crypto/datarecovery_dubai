<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use Illuminate\Contracts\Console\Kernel;

// Load scraped slugs
// We can run the check from scratch/inspect_author_urls.php
// Let's save the scraped lists to a JSON file so we don't have to rescrape!
