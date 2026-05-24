# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

## [0.2.0-beta.1] - 2026-05-24

### Added
- Grundstruktur des Moduls `media-badge` angelegt
- Einstiegspunkt `module.php` ergänzt
- Modulklasse `MediaBadgeModule.php` hinzugefügt
- CSS-Datei `resources/css/media-badge.css` hinzugefügt
- erste angepasste `media-page.phtml` für die Ausgabe von Badges ergänzt
- Unterstützung für das Auslesen von `NOTE`-Fakten an Medienobjekten hinzugefügt
- Unterstützung für verknüpfte Shared Notes ergänzt
- Erkennung von markierten Notizzeilen wie `MEDIA LICENCE: ...` implementiert
- erste Badge-Auflösung für Medienobjekte eingeführt
- konfigurierbare Note-Keys ergänzt
- erste Badge-Regelverwaltung als Modul-Grundlage hinzugefügt
- Admin-Views für Einstellungen und Badge-Regeln angelegt:
  - `admin/config.phtml`
  - `admin/badges.phtml`
  - `admin/badge-edit.phtml`
- Standard-Regeln für erste Lizenz-Badges ergänzt
- Fallback-Badge für nicht speziell gematchte Werte implementiert

### Changed
- Ausgabe am Medientitel von statischer Lizenzdarstellung auf regelbasierte Badge-Ausgabe umgestellt
- Modulstruktur auf Erweiterbarkeit für weitere Badge-Typen vorbereitet
- CSS von einer einfachen Startversion auf Badge- und Admin-Styling erweitert

### Fixed
- Einstiegspunkt für das Laden des Moduls in webtrees ergänzt
- Grundstruktur für Admin-Seiten vorbereitet, damit erste Konfigurationsseiten testbar werden

## [0.2.0-beta.2] - 2026-05-24

### Added
- Erweiterte Badge-Regeln mit konfigurierbaren Darstellungsmodi:
  - `text`
  - `icon`
  - `icon-text`
  - `auto`
- Unterstützung zusätzlicher Icon-Typen:
  - CSS-Klassen (`class`)
  - Textwerte (`text`)
  - Bild-/SVG-URLs (`url`)
- Erweiterte Regeloptionen für:
  - Badge-Text (`label_mode`)
  - Tooltip (`tooltip_mode`)
  - Position vor/nach dem Medientitel
  - Sortierreihenfolge
- Badge-Regelverwaltung im Admin-Bereich erweitert
- Vorschau für Badge-Regeln in der Admin-Übersicht
- Unterstützung für Bild-/SVG-Icons in der Medienansicht
- Direkte Navigation von der Modul-Konfiguration zur Badge-Regelverwaltung

### Changed
- Standard-Badge-Regeln wurden auf robustere Default-Werte umgestellt
- Darstellung und Inhalt der Badges wurden sauberer getrennt:
  - Inhalte stammen aus den Shared Notes / NOTE-Einträgen
  - Darstellung wird über Regeln gesteuert
- Konfiguration der globalen NOTE-Schlüssel wurde stabilisiert
- Mehrere globale NOTE-Schlüssel werden unterstützt und zeilenweise verarbeitet
- Badge-Rendering auf der Medienseite wurde erweitert und flexibler gemacht

### Fixed
- Fehler bei der Speicherung von Modul-Einstellungen behoben
- Interner Modulname für Custom-Module korrekt an webtrees angepasst
- HTTP-500/Fatal-Error durch doppelte Methodendefinitionen in `MediaBadgeModule.php` behoben
- Probleme mit der Badge-Anzeige nach dem Speichern der NOTE-Schlüssel behoben
- Problematische Emoji-basierte Default-Werte entfernt
- Speicherung von Badge-Regeln robuster gemacht, um Probleme mit Datenbank-/Kollationsgrenzen zu vermeiden

### Notes
- Diese Version ist weiterhin als Pre-Release / Beta gedacht
- Externe Bild-/SVG-URLs als Icons werden unterstützt, hängen aber von der Erreichbarkeit des jeweiligen Hosts ab
- Ein möglicher nächster Ausbauschritt wäre die Unterstützung lokaler Icons innerhalb des Moduls

### Notes
- Dieses Release ist eine erste testbare Vorabversion
- Fokus liegt auf Architektur, Modul-Loading, Badge-Erkennung und erster Admin-Struktur
- Noch nicht als stabile Produktivversion gedacht
