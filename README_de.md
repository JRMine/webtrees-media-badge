# Media Badge

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
