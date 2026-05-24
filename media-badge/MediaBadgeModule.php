<?php

declare(strict_types=1);

namespace Vendor\Webtrees\Module\MediaBadge;

use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleGlobalInterface;
use Fisharebest\Webtrees\Module\ModuleGlobalTrait;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\View;

use function preg_match;
use function preg_quote;
use function preg_split;
use function str_contains;
use function strtolower;
use function trim;

class MediaBadgeModule extends AbstractModule implements ModuleCustomInterface, ModuleGlobalInterface
{
    use ModuleCustomTrait;
    use ModuleGlobalTrait;

    public function title(): string
    {
        return 'Media Badge';
    }

    public function description(): string
    {
        return 'Zeigt Badges aus Medien-Notizen neben dem Medientitel an.';
    }

    public function customModuleAuthorName(): string
    {
        return 'Dein Name';
    }

    public function customModuleVersion(): string
    {
        return '0.2.0-beta.1';
    }

    public function customModuleSupportUrl(): string
    {
        return '';
    }

    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    public function boot(): void
    {
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
        View::registerCustomView('::media-page', $this->name() . '::media-page');
    }

    public function headContent(): string
    {
        return '<link rel="stylesheet" href="' . $this->assetUrl('css/media-badge.css') . '">';
    }

    public static function resolveBadgesForMedia(Media $record): array
    {
        $values = self::extractTaggedValues($record);
        $badges = [];

        foreach ($values as $value) {
            $badges[] = [
                'label'      => $value['value'],
                'class'      => self::badgeClassForValue($value['key'], $value['value']),
                'position'   => 'after-title',
                'sort_order' => 100,
                'title'      => $value['key'] . ': ' . $value['value'],
            ];
        }

        return $badges;
    }

    private static function extractTaggedValues(Media $record): array
    {
        $keys = ['MEDIA LICENCE'];
        $results = [];

        foreach ($record->facts(['NOTE']) as $fact) {
            $note_text = '';
            $target = $fact->target();

            if ($target instanceof Note) {
                $note_text = $target->getNote();
            } else {
                $note_text = $fact->value();
            }

            $lines = preg_split('/\R/u', $note_text) ?: [];

            foreach ($lines as $line) {
                foreach ($keys as $key) {
                    $pattern = '/^\s*' . preg_quote($key, '/') . '\s*:\s*(.+?)\s*$/ui';

                    if (preg_match($pattern, $line, $match) === 1) {
                        $results[] = [
                            'key'   => $key,
                            'value' => trim($match[1]),
                            'line'  => trim($line),
                        ];
                    }
                }
            }
        }

        return $results;
    }

    private static function badgeClassForValue(string $key, string $value): string
    {
        $normalized = strtolower(trim($value));

        if ($key === 'MEDIA LICENCE' && $normalized === 'cc by 4.0') {
            return 'mbg-badge mbg-badge--ccby';
        }

        if ($key === 'MEDIA LICENCE' && $normalized === 'cc by-sa 4.0') {
            return 'mbg-badge mbg-badge--ccbysa';
        }

        if ($key === 'MEDIA LICENCE' && ($normalized === 'public domain' || $normalized === 'gemeinfrei')) {
            return 'mbg-badge mbg-badge--public-domain';
        }

        if (
            $key === 'MEDIA LICENCE'
            && (
                str_contains($normalized, 'private')
                || $normalized === 'privat'
                || $normalized === 'nicht nachnutzen'
            )
        ) {
            return 'mbg-badge mbg-badge--private';
        }

        return 'mbg-badge mbg-badge--generic';
    }
}
