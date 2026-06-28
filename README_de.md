# Media Badge

**Media Badge** ist ein Custom-Modul für [webtrees](https://github.com/fisharebest/webtrees), das Informationen aus verknüpften Medien-Notizen (`NOTE`) als visuelle Badges direkt neben Medientiteln anzeigt.

Das Modul ist dafür gedacht, wiederverwendbare Informationen aus Shared Notes — zum Beispiel Lizenzen, Rechte, Statuskennzeichen oder andere Markierungen — direkt dort sichtbar zu machen, wo Medienobjekte angezeigt werden.

Statt diese Informationen nur in der Detailansicht eines Medienobjekts zu verstecken, bringt Media Badge sie in Listen, Tabs, Album-Ansichten und weitere medienbezogene Kontexte.

## Status

**Beta / Pre-Release**

Das Modul ist bereits testbar und im praktischen Einsatz grundsätzlich nutzbar, befindet sich aber weiterhin in aktiver Weiterentwicklung auf dem Weg zu Version 1.0.

Aktueller Release-Stand:

- `0.5.0-beta`

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

Ab `0.5.0-beta` kann Media Badge Badges in diesen Kontexten ausgeben:

- Medien-Detailseite
- Medienlisten-Seite
- verknüpfte Medien-Tabellen auf Datensatzseiten
- Standard-Multimedia-Tab
- Album-/Galerieansicht im Standard-Theme
- Zufallsmedien-/Startseiten-Slideshow
- Medienobjekte in Tatsachen- und Ereignisansichten
- verschachtelte Medienreferenzen innerhalb von Fakten/Ereignissen (über denselben allgemeinen Renderpfad)


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
   ```text
   modules_v4/media-badge/
   modules_v4/webtrees-media-badge/
```
   Die zweite Variante ist praktisch, wenn das Repository-Archiv direkt von
   GitHub installiert wird.
2. webtrees öffnen.
3. Das Modul im Kontrollzentrum aktivieren.
4. Die Modulkonfiguration öffnen.
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

## Standardverhalten

Wenn keine gespeicherten Badge-Regeln vorhanden sind, verwendet das Modul ein eingebautes Standard-Regelset.

Dieses enthält:

- eine generische Fallback-Regel für den primären Schlüssel
- spezifische Regeln für `CC BY 4.0`
- spezifische Regeln für `CC BY-SA 4.0`
- spezifische Regeln für `Public Domain`
- eine Regel für Werte, die `private` enthalten

Wenn keine spezifische Regel auf einen extrahierten Wert passt, kann weiterhin ein generisches Badge für diesen Schlüssel ausgegeben werden.

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

### Sichtbarkeit nach Seitenkontext

Die Sichtbarkeit einer Badge-Regel kann pro Seitenkontext gesteuert werden.

Unterstützte Kontexte sind derzeit:

- `all`
- `media-page`
- `media-list`
- `linked-media-table`
- `album-tab`
- `media-tab`
- `random-media-slide-show`

Wenn `page_contexts` fehlt oder leer ist, wird die Regel als auf allen Seiten sichtbar behandelt.

Wenn `all` gesetzt ist, haben zusätzliche Einzelkontexte keine weitere Bedeutung.

## Kompatibilität

Das Modul ist derzeit am besten mit dem **Standard-Theme von webtrees** getestet.

Wichtige Hinweise:

- Theme-spezifische Unterschiede sind weiterhin möglich, wenn ein Theme dieselben Core-Views überschreibt
- die Album-/Galerieausgabe im Standard-Theme wird unterstützt
- für bestimmte Konfliktfälle mit Themes oder Drittmodulen bevorzugt das Modul kleine wiederverwendbare Render-Fragmente statt vollständiger Template-Übernahmen
- optionale Kompatibilität zu anderen Modulen kann gezielt ergänzt werden, ohne harte Laufzeit-Abhängigkeiten zu erzwingen

### Kompatibilität mit Source Transcription

Für Installationen mit dem Modul `hh_source_transcription` enthält Media Badge eine optionale Kompatibilität für ausgewählte medienbezogene Ansichten.

Wenn beide Module dieselben webtrees-Views überschreiben, versucht Media Badge die Source-Transcription-Badges zusätzlich defensiv mit auszugeben, sofern das andere Modul verfügbar ist.

Wenn das Modul nicht installiert ist oder sein Service nicht aufgelöst werden kann, wird einfach keine zusätzliche Ausgabe erzeugt.

## Datenbank- und Speicherhinweis

Badge-Regeln werden als JSON in den webtrees-Moduleinstellungen gespeichert.

In Umgebungen mit älteren MySQL- oder MariaDB-Kollationen können bestimmte Unicode- oder Emoji-Zeichen Probleme verursachen. Deshalb sind CSS-Klassen oder Bild- bzw. SVG-URLs für Icons meist die robustere Wahl.

## Dokumentation

Zusätzliche Dokumentation befindet sich in:

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`

## Projektstruktur

Siehe docs/architecture.md

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

- Theme-spezifische Konflikte sind weiterhin möglich, wenn ein Theme eigene Varianten derselben media-bezogenen Templates verwendet
- je nach Theme kann für einzelne Ansichten zusätzliche Kompatibilitätsarbeit nötig sein
- benutzer- oder gruppenabhängige Badge-Sichtbarkeit ist derzeit noch nicht implementiert
- lokale Verwaltung eigener Icon-Assets ist noch nicht vollständig ausgebaut

## Roadmap

Mögliche nächste Entwicklungsschritte:

- Badge-Sichtbarkeit nach Benutzergruppe / Zugriffslevel
- lokale Icon-Assets statt externer URLs
- verbesserte Admin-Vorschau
- Import / Export von Badge-Regeln
- strengere Validierung für URL- und Regex-Regeln
- weitere Theme-Kompatibilität
- zusätzliche Badge-Quellen neben NOTE, z. B. weitere GEDCOM-Felder oder Metadaten

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
