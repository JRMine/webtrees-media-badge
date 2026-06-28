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
use function array_unique;
use function array_values;
use function implode;
use function in_array;
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
use const PHP_INT_MAX;

/**
 * Zentrale Modulklasse für Media Badge.
 *
 * Verantwortlichkeiten:
 * - Modul-Views registrieren
 * - globale NOTE_KEYS laden
 * - Badge-Regeln laden / normalisieren / speichern
 * - NOTE-Werte aus Medien extrahieren
 * - passende Regel für einen Wert bestimmen
 * - Seitenkontext-Sichtbarkeit prüfen
 * - finale Badge-Daten für die Views aufbereiten
 */
class MediaBadgeModule extends AbstractModule implements ModuleCustomInterface, ModuleGlobalInterface, ModuleConfigInterface
{
    use ModuleCustomTrait;
    use ModuleGlobalTrait;

    public const MODULE_NAME = '_media-badge_';

    private const PREF_NOTE_KEYS   = 'NOTE_KEYS';
    private const PREF_BADGE_RULES = 'BADGE_RULES';

    /**
     * Unterstützte Seitenkontexte für die Sichtbarkeitsprüfung.
     *
     * Diese Werte sollen zentral definiert bleiben, damit:
     * - Konfiguration
     * - Regelmodell
     * - Runtime-Logik
     * - Views
     * konsistent dieselben Bezeichner verwenden.
     */
    private const PAGE_CONTEXT_ALL                     = 'all';
    private const PAGE_CONTEXT_MEDIA_PAGE              = 'media-page';
    private const PAGE_CONTEXT_MEDIA_LIST              = 'media-list';
    private const PAGE_CONTEXT_LINKED_MEDIA_TABLE      = 'linked-media-table';
    private const PAGE_CONTEXT_ALBUM_TAB               = 'album-tab';
    private const PAGE_CONTEXT_MEDIA_TAB               = 'media-tab';
    private const PAGE_CONTEXT_RANDOM_MEDIA_SLIDE_SHOW = 'random-media-slide-show';

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

    /**
     * Registriert alle Modul-Views und die Core-Overrides,
     * über die Badge-Ausgabe in verschiedenen Seitenkontexten eingebunden wird.
     */
    public function boot(): void
    {
        View::registerNamespace($this->name(), $this->resourcesFolder() . 'views/');
        View::registerNamespace(self::MODULE_NAME, $this->resourcesFolder() . 'views/');

        View::registerCustomView('::media-page', $this->name() . '::media-page');
        View::registerCustomView('::lists/media-table', $this->name() . '::lists/media-table');
        View::registerCustomView('::modules/media-list/page', $this->name() . '::modules/media-list/page');
        View::registerCustomView('::modules/lightbox/tab', $this->name() . '::modules/lightbox/tab');
        View::registerCustomView('::modules/media/tab', $this->name() . '::modules/media/tab');
        View::registerCustomView('::modules/random_media/slide-show', $this->name() . '::modules/random_media/slide-show');
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

    /**
     * Admin-Seite: globale NOTE_KEYS konfigurieren.
     */
    public function getAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse($this->name() . '::admin/config', [
            'module'         => $this,
            'note_keys_text' => implode(
                "\n",
                self::configuredNoteKeys() !== [] ? self::configuredNoteKeys() : self::defaultNoteKeys()
            ),
            'title'          => I18N::translate('Media Badge settings'),
        ]);
    }

    /**
     * Speichert die globalen NOTE_KEYS.
     */
    public function postAdminAction(ServerRequestInterface $request): ResponseInterface
    {
        $body           = (array) ($request->getParsedBody() ?? []);
        $note_keys_text = (string) ($body['note_keys_text'] ?? '');

        $keys = self::normalizeNoteKeys($note_keys_text);
        $this->setPreference(self::PREF_NOTE_KEYS, implode("\n", $keys));

        return redirect($this->getConfigLink());
    }

    /**
     * Admin-Seite: Übersicht aller Badge-Regeln.
     */
    public function getBadgesAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        return $this->viewResponse($this->name() . '::admin/badges', [
            'module' => $this,
            'rules'  => self::badgeRules(),
            'title'  => I18N::translate('Badge rules'),
        ]);
    }

    /**
     * Admin-Seite: einzelne Badge-Regel laden oder neue Regel vorbereiten.
     */
    public function getBadgeEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->layout = 'layouts/administration';

        $query = $request->getQueryParams();
        $id    = (string) ($query['id'] ?? '');

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

    /**
     * Speichert eine einzelne Badge-Regel.
     *
     * Wichtig:
     * page_contexts wird bereits hier entgegengenommen, auch wenn
     * die Admin-UI dafür ggf. erst im nächsten Schritt ergänzt wird.
     */
    public function postBadgeEditAction(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);

        $submitted_rule = self::normalizeRule([
            'id'            => (string) ($body['id'] ?? ''),
            'enabled'       => ($body['enabled'] ?? '') === '1',
            'key'           => (string) ($body['key'] ?? ''),
            'match_type'    => (string) ($body['match_type'] ?? ''),
            'match_value'   => (string) ($body['match_value'] ?? ''),
            'render_mode'   => (string) ($body['render_mode'] ?? 'text'),
            'icon_type'     => (string) ($body['icon_type'] ?? 'class'),
            'icon_value'    => (string) ($body['icon_value'] ?? ''),
            'label_mode'    => (string) ($body['label_mode'] ?? 'value'),
            'label'         => (string) ($body['label'] ?? ''),
            'tooltip_mode'  => (string) ($body['tooltip_mode'] ?? 'auto'),
            'title'         => (string) ($body['title'] ?? ''),
            'class'         => (string) ($body['class'] ?? 'mbg-badge mbg-badge--generic'),
            'position'      => (string) ($body['position'] ?? 'after-title'),
            'sort_order'    => (int) ($body['sort_order'] ?? 0),
            'page_contexts' => $body['page_contexts'] ?? [],
        ]);

        $rules   = self::badgeRules();
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

    /**
     * Löscht eine Badge-Regel.
     */
    public function postBadgeDeleteAction(ServerRequestInterface $request): ResponseInterface
    {
        $body = (array) ($request->getParsedBody() ?? []);
        $id   = (string) ($body['id'] ?? '');

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

    /**
     * Standard-NOTE-Key, falls noch nichts konfiguriert wurde.
     */
    private static function defaultNoteKeys(): array
    {
        return ['MEDIA LICENCE'];
    }

    /**
     * Lädt gespeicherte NOTE_KEYS aus der Datenbank.
     */
    private static function configuredNoteKeys(): array
    {
        $value = DB::table('module_setting')
            ->where('module_name', '=', self::MODULE_NAME)
            ->where('setting_name', '=', self::PREF_NOTE_KEYS)
            ->value('setting_value');

        return self::normalizeNoteKeys((string) ($value ?? ''));
    }

    /**
     * Liefert den primären NOTE-Key.
     * Wird u. a. für Default-Regeln verwendet.
     */
    private static function primaryNoteKey(): string
    {
        $keys = self::noteKeys();

        return $keys[0] ?? self::defaultNoteKeys()[0];
    }

    /**
     * Liefert die aktuell aktiven NOTE_KEYS.
     */
    public static function noteKeys(): array
    {
        $keys = self::configuredNoteKeys();

        return $keys !== [] ? $keys : self::defaultNoteKeys();
    }

    /**
     * Liefert die zentral unterstützten Seitenkontexte.
     */
    public static function availablePageContexts(): array
    {
        return [
            self::PAGE_CONTEXT_ALL,
            self::PAGE_CONTEXT_MEDIA_PAGE,
            self::PAGE_CONTEXT_MEDIA_LIST,
            self::PAGE_CONTEXT_LINKED_MEDIA_TABLE,
            self::PAGE_CONTEXT_ALBUM_TAB,
            self::PAGE_CONTEXT_MEDIA_TAB,
            self::PAGE_CONTEXT_RANDOM_MEDIA_SLIDE_SHOW,
        ];
    }

    /**
     * Lädt Badge-Regeln aus der Datenbank und normalisiert sie.
     */
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

    /**
     * Speichert Badge-Regeln normalisiert als JSON.
     */
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
            // Absichtlich ohne JSON_UNESCAPED_UNICODE:
            // problematische Zeichen werden so escaped gespeichert.
            'setting_value' => json_encode($normalized, JSON_PRETTY_PRINT),
        ]);
    }

    /**
     * Zentrale Badge-Auflösung für ein Medienobjekt.
     *
     * Der zusätzliche Parameter $page_context erlaubt künftig eine
     * kontextabhängige Sichtbarkeitsprüfung pro Regel.
     *
     * Standardmäßig wird "media-page" angenommen, damit bestehende
     * Aufrufe rückwärtskompatibel bleiben.
     */
    public static function resolveBadgesForMedia(Media $record, string $page_context = self::PAGE_CONTEXT_MEDIA_PAGE): array
    {
        $values = self::extractTaggedValues($record);
        $rules  = self::badgeRules();

        $badges = [];

        foreach ($values as $value) {
            $rule = self::bestRuleForValue($rules, $value, $page_context);

            // Wenn gar keine sichtbare Regel für diesen Key existiert,
            // soll in diesem Kontext überhaupt nichts ausgegeben werden.
if ($rule === null) {
    $key = (string) ($value['key'] ?? '');

    $has_rules_for_key         = self::hasRulesForKey($rules, $key);
    $has_visible_rule_for_key  = self::hasVisibleRuleForKey($rules, $key, $page_context);

    // Fall B:
    // Es gibt Regeln für diesen Key, aber in diesem Kontext ist keine sichtbar.
    // => dann soll gar nichts gerendert werden.
    if ($has_rules_for_key && !$has_visible_rule_for_key) {
        continue;
    }

    // Fall A:
    // Es gibt gar keine Regeln für diesen Key.
    // => generischer Fallback wie bisher.
    //
    // Fall C:
    // Es gibt sichtbare Regeln, aber keine davon matcht.
    // => ebenfalls generischer Fallback.
    $badges[] = [
        'label'       => $value['value'],
        'class'       => 'mbg-badge mbg-badge--generic',
        'position'    => 'after-title',
        'sort_order'  => 999,
        'title'       => $value['key'] . ': ' . $value['value'],
        'render_mode' => 'text',
        'icon_type'   => 'class',
        'icon_value'  => '',
    ];

    continue;
}


            $badges[] = [
                'label'       => self::composeBadgeLabel($rule, $value),
                'class'       => $rule['class'] !== '' ? $rule['class'] : 'mbg-badge mbg-badge--generic',
                'position'    => $rule['position'],
                'sort_order'  => $rule['sort_order'],
                'title'       => self::composeBadgeTitle($rule, $value),
                'render_mode' => $rule['render_mode'],
                'icon_type'   => $rule['icon_type'],
                'icon_value'  => $rule['icon_value'],
            ];
        }

        usort(
            $badges,
            static fn (array $a, array $b): int => ($a['sort_order'] <=> $b['sort_order'])
        );

        return $badges;
    }

/**
 * Prüft, ob für einen Key überhaupt aktivierte Regeln existieren.
 *
 * Wichtig für die Fallback-Logik:
 * - keine Regeln für den Key => generischer Fallback erlaubt
 * - Regeln vorhanden, aber im Kontext unsichtbar => kein Badge
 */
private static function hasRulesForKey(array $rules, string $key): bool
{
    $normalized_key = strtolower(trim($key));

    foreach ($rules as $rule) {
        if (!(bool) ($rule['enabled'] ?? false)) {
            continue;
        }

        $rule_key = strtolower(trim((string) ($rule['key'] ?? '')));

        if ($rule_key !== '' && $rule_key === $normalized_key) {
            return true;
        }
    }

    return false;
}


    /**
     * Prüft, ob es für einen Key grundsätzlich mindestens eine
     * in diesem Seitenkontext sichtbare Regel gibt.
     *
     * Das ist wichtig, damit ein Fallback-Badge nicht in verbotenen
     * Kontexten "durchrutscht".
     */
    private static function hasVisibleRuleForKey(array $rules, string $key, string $page_context): bool
    {
        foreach ($rules as $rule) {
            if (!(bool) ($rule['enabled'] ?? false)) {
                continue;
            }

            $rule_key = strtolower(trim((string) ($rule['key'] ?? '')));

            if ($rule_key !== '' && $rule_key !== strtolower(trim($key))) {
                continue;
            }

            if (!self::ruleVisibleInPageContext($rule, $page_context)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * Extrahiert konfigurierte Key:Value-Zeilen aus NOTE-Facts eines Mediums.
     *
     * Unterstützt:
     * - direkt eingetragene NOTE-Inhalte
     * - verlinkte Shared Notes
     */
    private static function extractTaggedValues(Media $record): array
    {
        $keys    = self::noteKeys();
        $results = [];

        foreach ($record->facts(['NOTE']) as $fact) {
            $note_text = '';
            $target    = $fact->target();

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

    /**
     * Normalisiert die globale NOTE_KEY-Konfiguration.
     */
    private static function normalizeNoteKeys(string $text): array
    {
        $keys = preg_split('/\R/u', $text) ?: [];
        $keys = array_map(static fn (string $value): string => trim($value), $keys);
        $keys = array_values(array_filter($keys, static fn (string $value): bool => $value !== ''));

        return $keys;
    }

    /**
     * Eingebaute Standardregeln.
     *
     * page_contexts wird hier absichtlich nicht explizit gesetzt,
     * weil normalizeRule() fehlende Werte automatisch als ["all"]
     * behandelt.
     */
    private static function defaultBadgeRules(): array
    {
        $default_key = self::primaryNoteKey();

        return [
            self::normalizeRule([
                'enabled'      => true,
                'key'          => $default_key,
                'match_type'   => '',
                'match_value'  => '',
                'render_mode'  => 'text',
                'icon_type'    => 'class',
                'icon_value'   => '',
                'label_mode'   => 'value',
                'label'        => '',
                'tooltip_mode' => 'auto',
                'title'        => '',
                'class'        => 'mbg-badge mbg-badge--generic',
                'position'     => 'after-title',
                'sort_order'   => 100,
            ]),

            self::normalizeRule([
                'enabled'      => true,
                'key'          => $default_key,
                'match_type'   => 'exact',
                'match_value'  => 'CC BY 4.0',
                'render_mode'  => 'text',
                'icon_type'    => 'class',
                'icon_value'   => '',
                'label_mode'   => 'value',
                'label'        => '',
                'tooltip_mode' => 'fixed',
                'title'        => 'Creative Commons Attribution 4.0',
                'class'        => 'mbg-badge mbg-badge--ccby',
                'position'     => 'after-title',
                'sort_order'   => 10,
            ]),

            self::normalizeRule([
                'enabled'      => true,
                'key'          => $default_key,
                'match_type'   => 'exact',
                'match_value'  => 'CC BY-SA 4.0',
                'render_mode'  => 'text',
                'icon_type'    => 'class',
                'icon_value'   => '',
                'label_mode'   => 'value',
                'label'        => '',
                'tooltip_mode' => 'fixed',
                'title'        => 'Creative Commons Attribution-ShareAlike 4.0',
                'class'        => 'mbg-badge mbg-badge--ccbysa',
                'position'     => 'after-title',
                'sort_order'   => 20,
            ]),

            self::normalizeRule([
                'enabled'      => true,
                'key'          => $default_key,
                'match_type'   => 'exact',
                'match_value'  => 'Public Domain',
                'render_mode'  => 'text',
                'icon_type'    => 'class',
                'icon_value'   => '',
                'label_mode'   => 'value',
                'label'        => '',
                'tooltip_mode' => 'fixed',
                'title'        => 'Public Domain',
                'class'        => 'mbg-badge mbg-badge--public-domain',
                'position'     => 'after-title',
                'sort_order'   => 30,
            ]),

            self::normalizeRule([
                'enabled'      => true,
                'key'          => $default_key,
                'match_type'   => 'contains',
                'match_value'  => 'private',
                'render_mode'  => 'text',
                'icon_type'    => 'class',
                'icon_value'   => '',
                'label_mode'   => 'fixed',
                'label'        => 'Private',
                'tooltip_mode' => 'fixed',
                'title'        => 'Private / no reuse',
                'class'        => 'mbg-badge mbg-badge--private',
                'position'     => 'after-title',
                'sort_order'   => 40,
            ]),
        ];
    }

    /**
     * Normalisiert page_contexts auf eine stabile Array-Struktur.
     *
     * Regeln:
     * - fehlend / leer => ["all"]
     * - String => Array mit einem Wert
     * - ungültige Werte werden verworfen
     * - wenn "all" vorkommt, wird nur ["all"] gespeichert
     */
    private static function normalizePageContexts($page_contexts): array
    {
        if (is_string($page_contexts)) {
            $page_contexts = [$page_contexts];
        }

        if (!is_array($page_contexts)) {
            return [self::PAGE_CONTEXT_ALL];
        }

        $normalized = [];

        foreach ($page_contexts as $page_context) {
            if (!is_string($page_context)) {
                continue;
            }

            $page_context = trim($page_context);

            if ($page_context === '') {
                continue;
            }

            if (!in_array($page_context, self::availablePageContexts(), true)) {
                continue;
            }

            $normalized[] = $page_context;
        }

        $normalized = array_values(array_filter(array_unique($normalized), static fn (string $value): bool => $value !== ''));

        if ($normalized === [] || in_array(self::PAGE_CONTEXT_ALL, $normalized, true)) {
            return [self::PAGE_CONTEXT_ALL];
        }

        return $normalized;
    }

    /**
     * Normalisiert eine einzelne Badge-Regel.
     *
     * Hier wird das gespeicherte Konfigurationsarray in eine saubere,
     * konsistente Runtime-Struktur überführt.
     */
    private static function normalizeRule(array $rule): array
    {
        $match_type = trim((string) ($rule['match_type'] ?? ''));
        if (!in_array($match_type, ['', 'exact', 'contains', 'regex'], true)) {
            $match_type = '';
        }

        $render_mode = trim((string) ($rule['render_mode'] ?? 'text'));
        if (!in_array($render_mode, ['text', 'icon', 'icon-text', 'auto'], true)) {
            $render_mode = 'text';
        }

        $icon_type = trim((string) ($rule['icon_type'] ?? 'class'));
        if (!in_array($icon_type, ['class', 'text', 'url'], true)) {
            $icon_type = 'class';
        }

        $label_mode = trim((string) ($rule['label_mode'] ?? 'value'));
        if (!in_array($label_mode, ['value', 'fixed', 'none'], true)) {
            $label_mode = 'value';
        }

        $tooltip_mode = trim((string) ($rule['tooltip_mode'] ?? 'auto'));
        if (!in_array($tooltip_mode, ['auto', 'fixed', 'none'], true)) {
            $tooltip_mode = 'auto';
        }

        $position = trim((string) ($rule['position'] ?? 'after-title'));
        if (!in_array($position, ['before-title', 'after-title'], true)) {
            $position = 'after-title';
        }

        return [
            'id'            => trim((string) ($rule['id'] ?? uniqid('badge_', true))),
            'enabled'       => (bool) ($rule['enabled'] ?? true),
            'key'           => trim((string) ($rule['key'] ?? self::primaryNoteKey())),
            'match_type'    => $match_type,
            'match_value'   => trim((string) ($rule['match_value'] ?? '')),
            'render_mode'   => $render_mode,
            'icon_type'     => $icon_type,
            'icon_value'    => trim((string) ($rule['icon_value'] ?? '')),
            'label_mode'    => $label_mode,
            'label'         => trim((string) ($rule['label'] ?? '')),
            'tooltip_mode'  => $tooltip_mode,
            'title'         => trim((string) ($rule['title'] ?? '')),
            'class'         => trim((string) ($rule['class'] ?? 'mbg-badge mbg-badge--generic')),
            'position'      => $position,
            'sort_order'    => (int) ($rule['sort_order'] ?? 0),
            'page_contexts' => self::normalizePageContexts($rule['page_contexts'] ?? [self::PAGE_CONTEXT_ALL]),
        ];
    }

    /**
     * Sucht die beste sichtbare Regel für einen extrahierten Wert.
     *
     * Die Reihenfolge ist:
     * 1. enabled
     * 2. page_context sichtbar
     * 3. Match-Stärke
     * 4. sort_order
     */
    private static function bestRuleForValue(array $rules, array $value, string $page_context): ?array
    {
        $best_rule       = null;
        $best_score      = -1;
        $best_sort_order = PHP_INT_MAX;

        foreach ($rules as $rule) {
            if (!(bool) ($rule['enabled'] ?? false)) {
                continue;
            }

            if (!self::ruleVisibleInPageContext($rule, $page_context)) {
                continue;
            }

            $score = self::rulePriority($rule, $value);

            if ($score < 0) {
                continue;
            }

            $sort_order = (int) ($rule['sort_order'] ?? 0);

            if ($score > $best_score || ($score === $best_score && $sort_order < $best_sort_order)) {
                $best_rule       = $rule;
                $best_score      = $score;
                $best_sort_order = $sort_order;
            }
        }

        return $best_rule;
    }

    /**
     * Prüft, ob eine Regel im aktuellen Seitenkontext sichtbar ist.
     */
    private static function ruleVisibleInPageContext(array $rule, string $page_context): bool
    {
        $page_contexts = self::normalizePageContexts($rule['page_contexts'] ?? [self::PAGE_CONTEXT_ALL]);

        if (in_array(self::PAGE_CONTEXT_ALL, $page_contexts, true)) {
            return true;
        }

        return in_array(trim($page_context), $page_contexts, true);
    }

    /**
     * Ermittelt die Match-Priorität einer Regel für einen extrahierten Wert.
     *
     * Priorität:
     * - exact    => 400
     * - contains => 300
     * - regex    => 200
     * - generic  => 100
     * - kein Match => -1
     */
    private static function rulePriority(array $rule, array $value): int
    {
        $rule_key  = strtolower(trim((string) ($rule['key'] ?? '')));
        $value_key = strtolower(trim((string) ($value['key'] ?? '')));

        if ($rule_key !== '' && $rule_key !== $value_key) {
            return -1;
        }

        $match_type  = trim((string) ($rule['match_type'] ?? ''));
        $match_value = trim((string) ($rule['match_value'] ?? ''));
        $subject     = (string) ($value['value'] ?? '');

        if ($match_value === '') {
            return 100;
        }

        return match ($match_type) {
            'exact'    => strtolower(trim($subject)) === strtolower(trim($match_value)) ? 400 : -1,
            'contains' => str_contains(strtolower($subject), strtolower($match_value)) ? 300 : -1,
            'regex'    => @preg_match('/' . $match_value . '/iu', $subject) === 1 ? 200 : -1,
            default    => -1,
        };
    }

    /**
     * Baut den finalen Badge-Text.
     */
    private static function composeBadgeLabel(array $rule, array $value): string
    {
        $label_mode = (string) ($rule['label_mode'] ?? 'value');

        return match ($label_mode) {
            'none'  => '',
            'fixed' => trim((string) ($rule['label'] ?? '')) !== ''
                ? trim((string) ($rule['label'] ?? ''))
                : (string) ($value['value'] ?? ''),
            default => (string) ($value['value'] ?? ''),
        };
    }

    /**
     * Baut den finalen Tooltip-Text.
     */
    private static function composeBadgeTitle(array $rule, array $value): string
    {
        $tooltip_mode = (string) ($rule['tooltip_mode'] ?? 'auto');

        return match ($tooltip_mode) {
            'none'  => '',
            'fixed' => trim((string) ($rule['title'] ?? '')) !== ''
                ? trim((string) ($rule['title'] ?? ''))
                : ((string) ($value['key'] ?? '') . ': ' . (string) ($value['value'] ?? '')),
            default => (string) ($value['key'] ?? '') . ': ' . (string) ($value['value'] ?? ''),
        };
    }
}
