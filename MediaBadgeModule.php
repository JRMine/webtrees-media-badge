<?php

declare(strict_types=1);

namespace Vendor\Webtrees\Module\MediaBadge;

use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleGlobalInterface;
use Fisharebest\Webtrees\Module\ModuleGlobalTrait;
use Fisharebest\Webtrees\View;

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
        return '0.1.0';
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
}
