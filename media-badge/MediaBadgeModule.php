<?php

declare(strict_types=1);

namespace Vendor\Webtrees\Module\MediaBadge;

use Fisharebest\Webtrees\DB;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Media;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleConfigInterface;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleGlobalInterface;
use Fisharebest\Webtrees\Module\ModuleGlobalTrait;
use Fisharebest\Webtrees\Note;
use Fisharebest\Webtrees\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use function array_filter;
use function array_map;
use function array_values;
use function implode;
use function is_array;
use function is_string;
use function json_decode;
use function json_encode;
use function preg_match;
use function preg_quote;
use function preg_split;
use function redirect;
use function route;
use function str_contains;
use function strtolower;
use function trim;
use function uniqid;
use function usort;

use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_UNICODE;

class MediaBadgeModule extends AbstractModule implements ModuleCustomInterface, ModuleGlobalInterface, ModuleConfigInterface
{
    use ModuleCustomTrait;
    use ModuleGlobalTrait;

    public const MODULE_NAME = 'media-badge';
    private const PREF_NOTE_KEYS = 'NOTE_KEYS';
    private const PREF_BADGE_RULES = 'BADGE_RULES';

    public function title(): string
    {
        return 'Media Badge';
    }

    public function description(): string
    {
        return 'Zeigt konfigurierbare Badges aus Medien-Notizen neben dem Medientitel an.';
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

    public function getConfigLink(): string
    {
        return route('module', [
            'module' => $this->name(),
            'action' => 'Admin',
        ]);
    }

    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse($this->name() . '::admin/config', [
            'module'         => $this,
            'note_keys_text' => implode("\n", self::noteKeys()),
            'title'          => I18N::translate('Media Badge settings'),
        ]);
    }

    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $note_keys_text = (string) ($body['note_keys_text'] ?? '');

        $keys = self::normalizeNoteKeys($note_keys_text);
        $this->setPreference(self::PREF_NOTE_KEYS, implode("\n", $keys));

        return redirect($this->getConfigLink());
    }

    public function getBadgesAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse($this->name() . '::admin/badges', [
            'module' => $this,
            'rules'  => self::badgeRules(),
            'title'  => I18N::translate('Badge rules'),
        ]);
    }

    public function getBadgeEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $query = $request->getQueryParams();
        $id = (string) ($query['id'] ?? '');

        $rule = null;

        foreach (self::badgeRules() as $candidate) {
            if (($candidate['id'] ?? '') === $id) {
                $rule = $candidate;
                break;
            }
        }

        if ($rule === null) {
            $rule = self::normalizeRule([]);
        }

        return $this->viewResponse($this->name() . '::admin/badge-edit', [
            'module' => $this,
            'rule'   => $rule,
            'title'  => $id === ''
                ? I18N::translate('Add badge rule')
                : I18N::translate('Edit badge rule'),
        ]);
    }

    public function postBadgeEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);

        $submitted_rule = self::normalizeRule([
            'id'          => (string) ($body['id'] ?? ''),
            'enabled'     => ($body['enabled'] ?? '') === '1',
            'key'         => (string) ($body['key'] ?? ''),
            'match_type'  => (string) ($body['match_type'] ?? 'exact'),
            'match_value' => (string) ($body['match_value'] ?? ''),
            'label'       => (string) ($body['label'] ?? ''),
            'class'       => (string) ($body['class'] ?? 'mbg-badge mbg-badge--generic'),
            'position'    => (string) ($body['position'] ?? 'after-title'),
            'sort_order'  => (int) ($body['sort_order'] ?? 0),
            'title'       => (string) ($body['title'] ?? ''),
        ]);

        $rules = self::badgeRules();
        $updated = false;

        foreach ($rules as $index => $rule) {
            if (($rule['id'] ?? '') === $submitted_rule['id']) {
                $rules[$index] = $submitted_rule;
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            $rules[] = $submitted_rule;
        }

        self::saveBadgeRules($rules);

        return redirect(route('module', [
            'module' => $this->name(),
            'action' => 'Badges',
        ]));
    }

    public function postBadgeDeleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $id = (string) ($body['id'] ?? '');

        $rules = array_values(array_filter(
            self::badgeRules(),
            static fn (array $rule): bool => ($rule['id'] ?? '') !== $id
        ));

        self::saveBadgeRules($rules);

        return redirect(route('module', [
            'module' => $this->name(),
            'action' => 'Badges',
        ]));
    }

    public static function noteKeys(): array
    {
        $value = DB::table('module_setting')
            ->where('module_name', '=', self::MODULE_NAME)
            ->where('setting_name', '=', self::PREF_NOTE_KEYS)
            ->value('setting_value');

        return self::normalizeNoteKeys((string) ($value ?? 'MEDIA LICENCE'));
    }

    public static function badgeRules(): array
    {
        $value = DB::table('module_setting')
            ->where('module_name', '=', self::MODULE_NAME)
            ->where('setting_name', '=', self::PREF_BADGE_RULES)
            ->value('setting_value');

        if (!is_string($value) || trim($value) === '') {
            return self::defaultBadgeRules();
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return self::defaultBadgeRules();
        }

        $rules = array_map(
            static fn (array $rule): array => self::normalizeRule($rule),
            array_filter($decoded, static fn ($rule): bool => is_array($rule))
        );

        usort(
            $rules,
            static fn (array $a, array $b): int => ($a['sort_order'] <=> $b['sort_order'])
        );

        return $rules;
    }

    public static function saveBadgeRules(array $rules): void
    {
        $normalized = array_map(
            static fn (array $rule): array => self::normalizeRule($rule),
            $rules
        );

        usort(
            $normalized,
            static fn (array $a, array $b): int => ($a['sort_order'] <=> $b['sort_order'])
        );

        DB::table('module_setting')->updateOrInsert([
            'module_name'  => self::MODULE_NAME,
            'setting_name' => self::PREF_BADGE_RULES,
        ], [
            'setting_value' => json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }

    public static function resolveBadgesForMedia(Media $record): array
    {
        $values = self::extractTaggedValues($record);
        $rules  = self::badgeRules();

        $badges = [];

        foreach ($values as $value) {
            $matched = false;

            foreach ($rules as $rule) {
                if (!$rule['enabled']) {
                    continue;
                }

                if (!self::ruleMatches($rule, $value)) {
                    continue;
                }

                $matched = true;

                $badges[] = [
                    'label'      => $rule['label'] !== '' ? $rule['label'] : $value['value'],
                    'class'      => $rule['class'] !== '' ? $rule['class'] : 'mbg-badge mbg-badge--generic',
                    'position'   => $rule['position'],
                    'sort_order' => $rule['sort_order'],
                    'title'      => $rule['title'] !== ''
                        ? $rule['title']
                        : ($value['key'] . ': ' . $value['value']),
                ];
            }

            if (!$matched) {
                $badges[] = [
                    'label'      => $value['value'],
                    'class'      => 'mbg-badge mbg-badge--generic',
                    'position'   => 'after-title',
                    'sort_order' => 999,
                    'title'      => $value['key'] . ': ' . $value['value'],
                ];
            }
        }

        usort(
            $badges,
            static fn (array $a, array $b): int => ($a['sort_order'] <=> $b['sort_order'])
        );

        return $badges;
    }

    private static function extractTaggedValues(Media $record): array
    {
        $keys = self::noteKeys();
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

    private static function ruleMatches(array $rule, array $value): bool
    {
        $rule_key = strtolower(trim((string) $rule['key']));
        $value_key = strtolower(trim((string) $value['key']));

        if ($rule_key !== '' && $rule_key !== $value_key) {
            return false;
        }

        $match_type  = (string) $rule['match_type'];
        $match_value = (string) $rule['match_value'];
        $subject     = (string) $value['value'];

        if ($match_value === '') {
            return true;
        }

        return match ($match_type) {
            'contains' => str_contains(strtolower($subject), strtolower($match_value)),
            'regex'    => @preg_match('/' . $match_value . '/iu', $subject) === 1,
            default    => strtolower(trim($subject)) === strtolower(trim($match_value)),
        };
    }

    private static function normalizeNoteKeys(string $text): array
    {
        $keys = preg_split('/\R/u', $text) ?: [];
        $keys = array_map(static fn (string $value): string => trim($value), $keys);
        $keys = array_values(array_filter($keys, static fn (string $value): bool => $value !== ''));

        return $keys === [] ? ['MEDIA LICENCE'] : $keys;
    }

    private static function defaultBadgeRules(): array
    {
        return [
            self::normalizeRule([
                'enabled'     => true,
                'key'         => 'MEDIA LICENCE',
                'match_type'  => 'exact',
                'match_value' => 'CC BY 4.0',
                'label'       => 'CC BY 4.0',
                'class'       => 'mbg-badge mbg-badge--ccby',
                'position'    => 'after-title',
                'sort_order'  => 10,
                'title'       => 'Creative Commons Attribution 4.0',
            ]),
            self::normalizeRule([
                'enabled'     => true,
                'key'         => 'MEDIA LICENCE',
                'match_type'  => 'exact',
                'match_value' => 'CC BY-SA 4.0',
                'label'       => 'CC BY-SA 4.0',
                'class'       => 'mbg-badge mbg-badge--ccbysa',
                'position'    => 'after-title',
                'sort_order'  => 20,
                'title'       => 'Creative Commons Attribution-ShareAlike 4.0',
            ]),
            self::normalizeRule([
                'enabled'     => true,
                'key'         => 'MEDIA LICENCE',
                'match_type'  => 'exact',
                'match_value' => 'Public Domain',
                'label'       => 'Public Domain',
                'class'       => 'mbg-badge mbg-badge--public-domain',
                'position'    => 'after-title',
                'sort_order'  => 30,
                'title'       => 'Public Domain',
            ]),
            self::normalizeRule([
                'enabled'     => true,
                'key'         => 'MEDIA LICENCE',
                'match_type'  => 'contains',
                'match_value' => 'private',
                'label'       => 'Private',
                'class'       => 'mbg-badge mbg-badge--private',
                'position'    => 'after-title',
                'sort_order'  => 40,
                'title'       => 'Private / no reuse',
            ]),
        ];
    }

    private static function normalizeRule(array $rule): array
    {
        return [
            'id'          => trim((string) ($rule['id'] ?? uniqid('badge_', true))),
            'enabled'     => (bool) ($rule['enabled'] ?? true),
            'key'         => trim((string) ($rule['key'] ?? 'MEDIA LICENCE')),
            'match_type'  => trim((string) ($rule['match_type'] ?? 'exact')),
            'match_value' => trim((string) ($rule['match_value'] ?? '')),
            'label'       => trim((string) ($rule['label'] ?? '')),
            'class'       => trim((string) ($rule['class'] ?? 'mbg-badge mbg-badge--generic')),
            'position'    => trim((string) ($rule['position'] ?? 'after-title')),
            'sort_order'  => (int) ($rule['sort_order'] ?? 0),
            'title'       => trim((string) ($rule['title'] ?? '')),
        ];
    }
}
