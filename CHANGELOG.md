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

### Notes
- Dieses Release ist eine erste testbare Vorabversion
- Fokus liegt auf Architektur, Modul-Loading, Badge-Erkennung und erster Admin-Struktur
- Noch nicht als stabile Produktivversion gedacht
