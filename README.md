# Media Badge

## Deutsch

**Media Badge** ist ein Custom-Modul für webtrees, das Informationen aus verknüpften Medien-Notizen (`NOTE`) als visuelle Badges direkt neben dem Medientitel anzeigt.

Das Modul ist dafür gedacht, wiederverwendbare Informationen aus Shared Notes — zum Beispiel Lizenzen, Rechte, Status oder andere Kennzeichnungen — sichtbar und flexibel darzustellen, ohne dass diese erst in der Detailansicht gesucht werden müssen.

## Status

Beta / Pre-Release

Das Modul ist bereits testbar und im produktiven Alltag grundsätzlich nutzbar, befindet sich aber noch in aktiver Weiterentwicklung.

## Funktionen

### Medien-Badges aus NOTE-Einträgen

Das Modul durchsucht die an ein Medienobjekt verknüpften Notes nach konfigurierbaren Schlüsseln, zum Beispiel:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

Der Wert hinter dem Schlüssel kann als Badge direkt neben dem Medientitel angezeigt werden.

### Mehrere globale NOTE-Schlüssel

In der Modul-Konfiguration können mehrere Schlüssel hinterlegt werden — jeweils einer pro Zeile.

Beispiel:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

Damit kann das Modul mehrere unterschiedliche Arten von Metadaten aus Notizen auslesen.

### Konfigurierbare Badge-Regeln

Für gefundene Werte können Regeln definiert werden, die steuern:

- ob eine Regel aktiv ist
- für welchen Schlüssel sie gilt
- wie Werte verglichen werden
- wie das Badge dargestellt wird
- in welcher Reihenfolge es erscheint
- ob es vor oder nach dem Titel angezeigt wird
- welcher Tooltip angezeigt wird
- welche CSS-Klassen verwendet werden

### Unterstützte Match-Typen

Regeln können auf verschiedene Arten auf Werte angewendet werden:

- beliebiger Wert
- `exact`
- `contains`
- `regex`

Damit können sowohl generische Regeln als auch sehr spezifische Spezialfälle definiert werden.

### Unterstützte Darstellungsmodi

Ein Badge kann auf verschiedene Weise gerendert werden:

- `text` — nur Text
- `icon` — nur Icon
- `icon-text` — Icon und Text
- `auto` — automatische Wahl anhand der Regel

### Unterstützte Icon-Typen

Icons können auf verschiedene Arten eingebunden werden:

- `class` — CSS-Klasse, zum Beispiel für Icon-Fonts
- `text` — einfacher Text
- `url` — Bild- oder SVG-URL

Dadurch können Badges sowohl rein textbasiert als auch mit grafischen Symbolen dargestellt werden.

### Badge-Text und Tooltip steuerbar

Pro Regel kann konfiguriert werden:

- Text im Badge:
  - Wert aus der Note verwenden
  - festen Text anzeigen
  - keinen Text anzeigen
- Tooltip:
  - automatisch aus Schlüssel und Wert erzeugen
  - festen Tooltip verwenden
  - keinen Tooltip anzeigen

### Priorisierte Regelauswahl

Falls mehrere Regeln auf denselben Wert passen, wählt das Modul die passendste Regel nach Priorität:

1. `exact`
2. `contains`
3. `regex`
4. generische Schlüssel-Regel
5. Fallback

Dadurch bleibt die Ausgabe eindeutig und gut steuerbar.

## Beispiel

### Beispiel Shared Note

    MEDIA LICENCE: CC BY 4.0

### Mögliche Badge-Ausgabe

- Text-Badge: `CC BY 4.0`
- farbiges Badge mit Tooltip
- Icon + Text
- SVG-Icon + Text

## Installation

1. Modulordner nach `modules_v4/media-badge/` kopieren
2. webtrees aufrufen
3. Im Kontrollzentrum das Modul **Media Badge** aktivieren
4. Konfigurationsseite öffnen
5. Gewünschte NOTE-Schlüssel hinterlegen
6. Badge-Regeln anlegen oder vorhandene Standardregeln verwenden

## Konfiguration

### NOTE-Schlüssel definieren

In der Modul-Konfiguration können die Schlüssel eingetragen werden, nach denen in Medien-Notizen gesucht werden soll.

Wichtig: Mehrere Schlüssel sind möglich — bitte einen Schlüssel pro Zeile eintragen.

Beispiel:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

### Badge-Regeln definieren

In der Regelverwaltung können Regeln angelegt, bearbeitet und gelöscht werden.

Typische Felder einer Regel sind:

- Schlüssel
- Match-Typ
- Vergleichswert
- Darstellung
- Icon-Typ
- Icon-Wert
- Textmodus
- Tooltip-Modus
- CSS-Klassen
- Position
- Sortierreihenfolge

Weitere Details finden sich in den Dateien unter `docs/`.

## Empfohlene Verwendung

Das Modul eignet sich besonders für:

- Medienlizenzen
- Nutzungsrechte
- Veröffentlichungshinweise
- Bearbeitungsstatus
- Quellenkennzeichnung
- Sichtbarkeitskennzeichen
- Qualitätskennzeichen

## Standardverhalten

Wenn keine passende Spezialregel gefunden wird, verwendet das Modul eine generische Standardregel für den jeweiligen Schlüssel.

So bleibt die Ausgabe auch dann sinnvoll, wenn nicht für jeden Wert eine eigene Regel definiert wurde.

## Hinweise zu Icons

### CSS-Klassen

Geeignet für vorhandene Icon-Fonts oder eigene Styles.

Beispiel:

    bi bi-lock-fill

### Text

Geeignet für einfache Textwerte oder kurze Symbole.

Beispiel:

    Copyright

### URL

Geeignet für kleine Bild- oder SVG-Dateien.

Beispiel:

    https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg

## Hinweise zu externen Bild- und SVG-URLs

Externe Icon-URLs funktionieren grundsätzlich, sind aber abhängig von:

- Erreichbarkeit des externen Hosts
- Hotlinking-Regeln des Zielservers
- Browser- oder CSP-Verhalten

Langfristig kann es sinnvoll sein, häufig verwendete Icons direkt lokal im Modul zu verwalten.

## Kompatibilität und Datenbank-Hinweis

Das Modul speichert Badge-Regeln als JSON in den webtrees-Moduleinstellungen.

In Umgebungen mit älteren MySQL- oder MariaDB-Kollationen kann die Speicherung bestimmter Unicode- oder Emoji-Zeichen problematisch sein. Deshalb sind für Icons meist CSS-Klassen oder Bild- beziehungsweise SVG-URLs die robustere Wahl.

## Projektstruktur

    modules_v4/media-badge/
    ├── module.php
    ├── MediaBadgeModule.php
    └── resources/
        ├── css/
        │   └── media-badge.css
        └── views/
            ├── media-page.phtml
            └── admin/
                ├── config.phtml
                ├── badges.phtml
                └── badge-edit.phtml

## Entwicklungsschwerpunkte der aktuellen Beta

- zuverlässiges Laden des Moduls
- stabile Speicherung der NOTE-Schlüssel
- Regelverwaltung im Admin-Bereich
- flexible Badge-Darstellung
- Unterstützung von Text-, Klassen- und URL-Icons
- Vermeidung früherer HTTP-500- und Speicherprobleme

## Bekannte Einschränkungen

- externe SVG- oder Bild-URLs hängen vom jeweiligen Host ab
- die Badge-Ausgabe ist aktuell auf die Medienseite fokussiert
- weitere Ansichten, zum Beispiel Listen, Galerien oder andere Record-Kontexte, sind perspektivisch erweiterbar

## Roadmap / Nächste Ideen

Mögliche nächste Ausbaustufen:

- lokale Icons innerhalb des Moduls
- zusätzliche Badge-Ausgabe in Listenansichten
- bessere Vorschau in der Admin-Oberfläche
- Import und Export von Badge-Regeln
- feinere Validierung für URL- und Regex-Regeln
- konfigurierbare Sichtbarkeit je Seite, Schlüssel oder Benutzergruppe

## Lizenz

Noch festzulegen.

Empfehlung: GPLv3, passend zum webtrees-Umfeld.

## Mitwirken

Feedback, Ideen und Tests sind willkommen — insbesondere zu:

- sinnvollen Regeltypen
- typischen Shared-Note-Strukturen
- sinnvollen Standard-Presets
- UI/UX der Regelverwaltung
- realen Anwendungsfällen für Medienkennzeichnungen

## Weiterführende Dokumentation

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`

---

## English

**Media Badge** is a custom module for webtrees that displays information from linked media notes (`NOTE`) as visual badges directly next to the media title.

The module is designed to make reusable information from Shared Notes — such as licences, rights, status markers, or other labels — visible and flexible, without requiring users to open the detail view to find them.

## Status

Beta / Pre-release

The module is already testable and generally usable in everyday production workflows, but it is still under active development.

## Features

### Media badges from NOTE entries

The module scans notes linked to a media object for configurable keys, for example:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

The value after the key can be displayed as a badge directly next to the media title.

### Multiple global NOTE keys

Multiple keys can be defined in the module configuration — one key per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

This allows the module to read different kinds of metadata from notes.

### Configurable badge rules

Rules can be defined for detected values to control:

- whether a rule is active
- which key it applies to
- how values are matched
- how the badge is rendered
- in which order it appears
- whether it is shown before or after the title
- which tooltip is displayed
- which CSS classes are used

### Supported match types

Rules can be applied to values in different ways:

- any value
- `exact`
- `contains`
- `regex`

This makes it possible to define both generic rules and very specific special cases.

### Supported render modes

A badge can be rendered in different ways:

- `text` — text only
- `icon` — icon only
- `icon-text` — icon and text
- `auto` — automatic choice based on the rule

### Supported icon types

Icons can be provided in different ways:

- `class` — CSS class, for example for icon fonts
- `text` — plain text
- `url` — image or SVG URL

This allows badges to be displayed either as plain text or with graphical symbols.

### Configurable badge text and tooltip

Each rule can define:

- badge text:
  - use the value from the note
  - show fixed text
  - show no text
- tooltip:
  - generate automatically from key and value
  - use a fixed tooltip
  - show no tooltip

### Prioritized rule selection

If multiple rules match the same value, the module selects the most appropriate rule by priority:

1. `exact`
2. `contains`
3. `regex`
4. generic key rule
5. fallback

This keeps the output predictable and easy to control.

## Example

### Example Shared Note

    MEDIA LICENCE: CC BY 4.0

### Possible badge output

- text badge: `CC BY 4.0`
- colored badge with tooltip
- icon + text
- SVG icon + text

## Installation

1. Copy the module folder to `modules_v4/media-badge/`
2. Open webtrees
3. Activate the **Media Badge** module in the control panel
4. Open the configuration page
5. Add the desired NOTE keys
6. Create badge rules or use the default rules

## Configuration

### Define NOTE keys

In the module configuration, you can define the keys that should be searched for in media notes.

Important: Multiple keys are supported — enter one key per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

### Define badge rules

In the rule management page, rules can be created, edited, and deleted.

Typical rule fields include:

- key
- match type
- match value
- render mode
- icon type
- icon value
- label mode
- tooltip mode
- CSS classes
- position
- sort order

Further details are documented in the files under `docs/`.

## Recommended use cases

The module is especially useful for:

- media licences
- usage rights
- publication notices
- editorial status
- source labels
- visibility markers
- quality markers

## Default behavior

If no specific rule matches, the module uses a generic default rule for the corresponding key.

This ensures meaningful output even if not every possible value has its own dedicated rule.

## Notes on icons

### CSS classes

Suitable for existing icon fonts or custom styles.

Example:

    bi bi-lock-fill

### Text

Suitable for simple text values or short symbols.

Example:

    Copyright

### URL

Suitable for small image or SVG files.

Example:

    https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg

## Notes on external image and SVG URLs

External icon URLs generally work, but they depend on:

- availability of the external host
- hotlinking rules of the target server
- browser or CSP behavior

In the long term, it may be preferable to manage frequently used icons locally inside the module.

## Compatibility and database note

The module stores badge rules as JSON in the webtrees module settings.

In environments with older MySQL or MariaDB collations, storing certain Unicode or emoji characters may cause problems. For that reason, CSS classes or image and SVG URLs are usually the more robust choice for icons.

## Project structure

    modules_v4/media-badge/
    ├── module.php
    ├── MediaBadgeModule.php
    └── resources/
        ├── css/
        │   └── media-badge.css
        └── views/
            ├── media-page.phtml
            └── admin/
                ├── config.phtml
                ├── badges.phtml
                └── badge-edit.phtml

## Focus areas of the current beta

- reliable module loading
- stable storage of NOTE keys
- admin rule management
- flexible badge rendering
- support for text, class, and URL icons
- prevention of earlier HTTP 500 and storage issues

## Known limitations

- external SVG or image URLs depend on the respective host
- badge output is currently focused on the media page
- additional views such as lists, galleries, or other record contexts can be added later

## Roadmap / Next ideas

Possible next steps include:

- local icons inside the module
- additional badge output in list views
- better preview in the admin interface
- import and export of badge rules
- stricter validation for URL and regex rules
- configurable visibility per page, key, or user group

## License

To be decided.

Recommended: GPLv3, to match the webtrees ecosystem.

## Contributing

Feedback, ideas, and testing are welcome — especially regarding:

- useful rule types
- typical Shared Note structures
- sensible default presets
- UI/UX of the rule editor
- real-world use cases for media labels

## Additional documentation

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`
