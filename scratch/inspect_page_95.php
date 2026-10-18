<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Page;
use Illuminate\Contracts\Console\Kernel;

$p = Page::find(95);
echo "Page 95 Details:\n";
echo 'Slug: '.$p->slug."\n";
echo 'Title: '.$p->title."\n";
echo 'Meta Title: '.$p->meta_title."\n";
echo 'Template: '.$p->template."\n";
echo 'Status: '.$p->status."\n";
echo 'Content length: '.strlen($p->content ?? '')."\n";
echo "Preview:\n".substr(strip_tags($p->content), 0, 400)."\n";
