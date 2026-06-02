# Media Badge

**Media Badge** ist ein Custom-Modul für [webtrees](https://github.com/fisharebest/webtrees), das Informationen aus verknüpften Medien-Notizen (`NOTE`) als visuelle Badges direkt neben Medientiteln anzeigt.

Das Modul ist dafür gedacht, wiederverwendbare Informationen aus Shared Notes — zum Beispiel Lizenzen, Rechte, Statuskennzeichen oder andere Markierungen — direkt dort sichtbar zu machen, wo Medienobjekte angezeigt werden.

Statt diese Informationen nur in der Detailansicht eines Medienobjekts zu verstecken, bringt Media Badge sie in Listen, Tabs, Album-Ansichten und weitere medienbezogene Kontexte.

## Status

**Beta / Pre-Release**

Das Modul ist bereits testbar und im praktischen Einsatz grundsätzlich nutzbar, befindet sich aber weiterhin in aktiver Weiterentwicklung auf dem Weg zu Version 1.0.

Aktueller Release-Stand:

- `0.4.0-beta`

## Funktionen

### Badge-Ausgabe aus Medien-Notizen

Das Modul liest konfigurierbare Schlüssel aus medienbezogenen `NOTE`-Inhalten aus.

Beispiel:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

Der Wert hinter dem Schlüssel kann direkt neben dem Medientitel als Badge angezeigt werden.

### Mehrere globale NOTE-Schlüssel

In der Modul-Konfiguration können mehrere Schlüssel definiert werden, jeweils einer pro Zeile.

Beispiel:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

Dadurch kann das Modul unterschiedliche Arten wiederverwendbarer Metadaten aus Shared Notes auslesen.

### Konfigurierbare Badge-Regeln

Badge-Regeln steuern, wie gefundene Werte dargestellt werden.

Regeln können festlegen:

- ob die Regel aktiv ist
- für welchen Schlüssel sie gilt
- wie Werte verglichen werden
- wie das Badge gerendert wird
- ob das Badge vor oder nach dem Titel erscheint
- die Sortierreihenfolge
- das Tooltip-Verhalten
- die verwendeten CSS-Klassen
- das Icon-Verhalten
- feste Labels oder wertbasierte Labels

### Unterstützte Match-Typen

Regeln unterstützen derzeit:

- beliebiger Wert
- `exact`
- `contains`
- `regex`

Dadurch sind sowohl generische Schlüsselregeln als auch sehr spezifische wertbasierte Regeln möglich.

### Unterstützte Darstellungsmodi

Badges können wie folgt gerendert werden:

- `text`
- `icon`
- `icon-text`
- `auto`

### Unterstützte Icon-Typen

Icons können bereitgestellt werden als:

- `class` — CSS-Klassennamen, zum Beispiel für Icon-Fonts
- `text` — einfache textbasierte Icons
- `url` — Bild- oder SVG-URLs

### Wiederverwendbares Badge-Rendering

Das Badge-Rendering wurde zentralisiert, sodass dieselbe Ausgabelogik in mehreren Views und Medienkontexten wiederverwendet werden kann.

Das reduziert Duplikate und erleichtert spätere Erweiterungen.

## Unterstützte Ausgabekontexte

Seit `0.4.0-beta` kann Media Badge Badges in diesen Kontexten ausgeben:

- Medien-Detailseite
- Medienlisten-Seite
- verknüpfte Medien-Tabellen auf datensatzbezogenen Seiten
- Standard-Multimedia-Tab
- Album-Ansicht im Standard-Theme
- Random-Media-Slideshow / Startseiten-Diashow

Damit sind Badges nicht mehr nur auf die Medien-Detailseite beschränkt.

## Beispiel

### Beispiel-Shared-Note

    MEDIA LICENCE: CC BY 4.0

### Mögliche Ausgabe

- Text-Badge: `CC BY 4.0`
- farbiges Badge mit Tooltip
- Icon + Text
- SVG-Icon + Text

## Installation

1. Den Modulordner an einen der folgenden Pfade kopieren:

       modules_v4/media-badge/
       modules_v4/webtrees-media-badge/

   Die zweite Variante ist praktisch, wenn das Repository-Archiv direkt von
   GitHub installiert wird.

2. webtrees öffnen.
3. Das Modul im Kontrollzentrum aktivieren.
4. Die Konfigurationsseite des Moduls öffnen.
5. Einen oder mehrere globale NOTE-Schlüssel eintragen.
6. Badge-Regeln anlegen oder die Standardregeln verwenden.

## Konfiguration

### Globale NOTE-Schlüssel

In der Modul-Konfiguration können ein oder mehrere Schlüssel definiert werden, nach denen in Medien-Notizen gesucht werden soll.

Wichtig: Bitte einen Schlüssel pro Zeile eintragen.

Beispiel:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

### Badge-Regeln

Badge-Regeln können in der Regelverwaltung angelegt, bearbeitet und gelöscht werden.

Typische Regelfelder sind:

- Schlüssel
- Match-Typ
- Vergleichswert
- Darstellungsmodus
- Icon-Typ
- Icon-Wert
- Label-Modus
- Tooltip-Modus
- CSS-Klasse
- Position
- Sortierreihenfolge

Weitere Details sind in den Dateien unter `docs/` dokumentiert.

## Standardverhalten

Wenn keine spezifische Regel auf einen Wert passt, kann das Modul auf eine generische Regel für den jeweiligen Schlüssel zurückfallen.

Dadurch bleibt die Ausgabe konsistent, auch wenn nicht jeder mögliche Wert eine eigene Spezialregel besitzt.

## Empfohlene Einsatzbereiche

Media Badge eignet sich besonders für:

- Medienlizenzen
- Nutzungsrechte
- Veröffentlichungsstatus
- Bearbeitungsstatus
- interne Workflow-Kennzeichnungen
- Quellenkennzeichnungen
- Sichtbarkeitsmarkierungen
- Qualitätskennzeichen

## Hinweise zu Icons

### CSS-Klassen

Geeignet für Icon-Fonts oder vorhandene Projekt-Styles.

Beispiel:

    bi bi-lock-fill

### Text

Geeignet für einfache kurze Werte.

Beispiel:

    Copyright

### URL

Geeignet für kleine Bild- oder SVG-Dateien.

Beispiel:

    https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg

## Hinweise zu externen Bild- und SVG-URLs

Externe Bild- oder SVG-URLs funktionieren grundsätzlich, hängen aber ab von:

- Erreichbarkeit des externen Hosts
- Hotlinking-Beschränkungen
- Browser-Verhalten
- Content-Security-Policy-Einstellungen

Für häufig verwendete Icons kann es in zukünftigen Versionen sinnvoll sein, lokale Assets innerhalb des Moduls zu verwalten.

## Kompatibilität

Das Modul ist derzeit am besten mit dem **Standard-Theme von webtrees** getestet.

Wichtiger Hinweis:

- theme-spezifische Konflikte können auftreten, wenn ein anderes Theme dieselben medienbezogenen Core-Views überschreibt
- Album-Rendering im Standard-Theme wird unterstützt
- für Themes mit eigenen Media-Templates kann zusätzliche Kompatibilitätsarbeit nötig sein

## Datenbank- und Speicherhinweis

Badge-Regeln werden als JSON in den webtrees-Moduleinstellungen gespeichert.

In Umgebungen mit älteren MySQL- oder MariaDB-Kollationen können bestimmte Unicode- oder Emoji-Zeichen Probleme verursachen. Deshalb sind CSS-Klassen oder Bild- bzw. SVG-URLs für Icons meist die robustere Wahl.

## Dokumentation

Zusätzliche Dokumentation befindet sich in:

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`

## Projektstruktur

    modules_v4/media-badge/
    ├── module.php
    ├── MediaBadgeModule.php
    └── resources/
        ├── css/
        │   └── media-badge.css
        └── views/
            ├── components/
            │   ├── media-badge.phtml
            │   └── media-badge-list.phtml
            ├── lists/
            │   └── media-table.phtml
            ├── modules/
            │   ├── lightbox/
            │   │   └── tab.phtml
            │   ├── media/
            │   │   └── tab.phtml
            │   ├── media-list/
            │   │   └── page.phtml
            │   └── random_media/
            │       └── slide-show.phtml
            ├── admin/
            │   ├── config.phtml
            │   ├── badges.phtml
            │   └── badge-edit.phtml
            └── media-page.phtml

## Aktueller Beta-Fokus

Die aktuelle Beta konzentriert sich auf:

- zuverlässiges Laden des Moduls
- stabile Speicherung der NOTE-Schlüssel
- wiederverwendbares Badge-Rendering
- Unterstützung mehrerer medienbezogener Seitenkontexte
- flexible Badge-Ausgabe mit Text, Icons und URLs
- Reduzierung doppelter Rendering-Logik
- Schaffung einer soliden Basis für spätere Sichtbarkeits- und Rechte-Regeln

## Bekannte Einschränkungen

- theme-spezifische Konflikte können weiterhin auftreten, wenn andere Themes dieselben Core-Media-Templates überschreiben
- eine Suchintegration für Medien ist nicht enthalten, da die Core-Suche derzeit keine standardisierte Medien-Ergebnisansicht bereitstellt
- Seitenkontext-spezifische Sichtbarkeitssteuerung ist noch nicht implementiert
- Badge-Sichtbarkeit nach Benutzergruppe oder Zugriffslevel ist noch nicht implementiert

## Roadmap

Mögliche nächste Entwicklungsschritte sind:

- Badge-Sichtbarkeit nach Benutzergruppe / Zugriffslevel
- Badge-Sichtbarkeit je Seitenkontext
- Unterstützung lokaler Icon-Assets
- bessere Vorschau in der Admin-Oberfläche
- Import / Export von Badge-Regeln
- strengere Validierung für URL- und Regex-Regeln
- zusätzliche Theme-Kompatibilitätsarbeit

## Lizenz

Noch festzulegen.

Empfehlung:

- GPLv3, passend zum webtrees-Ökosystem

## Mitwirken

Feedback, Ideen und Tests sind willkommen, insbesondere zu:

- sinnvollen Regeltypen
- realen Shared-Note-Strukturen
- sinnvollen Standard-Presets
- UI und UX der Regelverwaltung
- medienbezogenen Kontexten, die Badge-Unterstützung erhalten sollten
- zukünftiger Sichtbarkeits- und Rechte-Logik

## Release-Hinweise

Die neuesten Änderungen finden sich in:

- `CHANGELOG.md`
