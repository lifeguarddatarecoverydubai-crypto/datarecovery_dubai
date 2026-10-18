<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\Post;
use App\Support\SeoTitleNormalizer;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class FixSeoTitlesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cms:fix-seo-titles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One-time cleanup: strip duplicated brand suffix from meta_title and title-case lowercase titles on existing Posts and Pages';

    public function handle(): int
    {
        $this->fix(Post::query(), 'Posts');
        $this->fix(Page::query(), 'Pages');

        return self::SUCCESS;
    }

    protected function fix(Builder $query, string $label): void
    {
        $updated = 0;

        $query->chunkById(100, function ($items) use (&$updated) {
            foreach ($items as $item) {
                $changes = [];

                if (! empty($item->meta_title)) {
                    $cleanMetaTitle = SeoTitleNormalizer::stripSiteSuffix($item->meta_title);
                    if ($cleanMetaTitle !== $item->meta_title) {
                        $changes['meta_title'] = $cleanMetaTitle;
                    }
                }

                if (! empty($item->title)) {
                    $cleanTitle = SeoTitleNormalizer::normalizeTitleCase($item->title);
                    if ($cleanTitle !== $item->title) {
                        $changes['title'] = $cleanTitle;
                    }
                }

                if (! empty($changes)) {
                    $item->forceFill($changes)->save();
                    $updated++;
                }
            }
        });

        $this->info("  {$label}: updated {$updated} rows.");
    }
}
