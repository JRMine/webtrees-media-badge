# Media Badge

**Media Badge** ist ein Custom-Modul für [webtrees](https://www.webtrees.net/), das Informationen aus verknüpften Medien-Notizen (`NOTE`) als visuelle Badges direkt **neben dem Medientitel** anzeigt.

Das Modul ist dafür gedacht, wiederverwendbare Informationen aus Shared Notes — zum Beispiel Lizenzen, Rechte, Status oder andere Kennzeichnungen — sichtbar und flexibel darzustellen, ohne dass diese erst in der Detailansicht gesucht werden müssen.

---

## Status

**Beta / Pre-Release**

Das Modul ist bereits testbar und im produktiven Alltag grundsätzlich nutzbar, befindet sich aber noch in aktiver Weiterentwicklung.

---

## Funktionen

### Medien-Badges aus NOTE-Einträgen

Das Modul durchsucht die an ein Medienobjekt verknüpften Notes nach konfigurierbaren Schlüsseln, z. B.:

MEDIA LICENCE: CC BY 4.0
MEDIA RIGHTS: Public Domain
MEDIA STATUS: geprüft
Der Wert hinter dem Schlüssel kann als Badge direkt neben dem Medientitel angezeigt werden.

Mehrere globale NOTE-Schlüssel
In der Modul-Konfiguration können mehrere Schlüssel hinterlegt werden — jeweils einer pro Zeile.

Beispiel:

CopyMEDIA LICENCE
MEDIA RIGHTS
MEDIA STATUS
Damit kann das Modul mehrere unterschiedliche Arten von Metadaten aus Notizen auslesen.

Konfigurierbare Badge-Regeln
Für gefundene Werte können Regeln definiert werden, die steuern:

ob eine Regel aktiv ist
für welchen Schlüssel sie gilt
wie Werte verglichen werden
wie das Badge dargestellt wird
in welcher Reihenfolge es erscheint
ob es vor oder nach dem Titel angezeigt wird
welcher Tooltip angezeigt wird
Unterstützte Match-Typen
Regeln können auf verschiedene Arten auf Werte angewendet werden:

beliebiger Wert
exact
contains
regex
Damit können sowohl generische Regeln als auch sehr spezifische Spezialfälle definiert werden.

Unterstützte Darstellungsmodi
Ein Badge kann auf verschiedene Weise gerendert werden:

text – nur Text
icon – nur Icon
icon-text – Icon und Text
auto – automatische Wahl anhand der Regel
Unterstützte Icon-Typen
Icons können auf verschiedene Arten eingebunden werden:

class – CSS-Klasse, z. B. für Icon-Fonts
text – einfacher Text
url – Bild- oder SVG-URL
Dadurch können Badges sowohl rein textbasiert als auch mit grafischen Symbolen dargestellt werden.

Badge-Text und Tooltip steuerbar
Pro Regel kann konfiguriert werden:

Text im Badge
Wert aus der Note verwenden
festen Text anzeigen
keinen Text anzeigen
Tooltip
automatisch aus Schlüssel + Wert erzeugen
festen Tooltip verwenden
keinen Tooltip anzeigen
Priorisierte Regelauswahl
Falls mehrere Regeln auf denselben Wert passen, wählt das Modul die passendste Regel nach Priorität:

exact
contains
regex
generische Schlüssel-Regel
Fallback
Dadurch bleibt die Ausgabe eindeutig und gut steuerbar.

Beispiel
Shared Note
CopyMEDIA LICENCE: CC BY 4.0
Mögliche Badge-Ausgabe
Text-Badge: CC BY 4.0
farbiges Badge mit Tooltip
Icon + Text
SVG-Icon + Text
Installation
Modulordner nach modules_v4/media-badge/ kopieren
webtrees aufrufen
Im Kontrollzentrum das Modul Media Badge aktivieren
Konfigurationsseite öffnen
gewünschte NOTE-Schlüssel hinterlegen
Badge-Regeln anlegen oder vorhandene Standardregeln verwenden
Konfiguration
1. NOTE-Schlüssel definieren
In der Modul-Konfiguration können die Schlüssel eingetragen werden, nach denen in Medien-Notizen gesucht werden soll.

Wichtig:
Mehrere Schlüssel sind möglich — bitte einen Schlüssel pro Zeile eintragen.

Beispiel:

CopyMEDIA LICENCE
MEDIA RIGHTS
MEDIA STATUS
2. Badge-Regeln definieren
In der Regelverwaltung können Regeln angelegt, bearbeitet und gelöscht werden.

Typische Felder einer Regel:

Schlüssel
Match-Typ
Vergleichswert
Darstellung
Icon-Typ
Icon-Wert
Textmodus
Tooltip-Modus
CSS-Klassen
Position
Sortierreihenfolge
Empfohlene Verwendung
Das Modul eignet sich besonders für:

Medienlizenzen
Nutzungsrechte
Veröffentlichungshinweise
Bearbeitungsstatus
Quellenkennzeichnung
Sichtbarkeitskennzeichen
Qualitätskennzeichen
Standardverhalten
Wenn keine passende Spezialregel gefunden wird, verwendet das Modul eine generische Standardregel für den jeweiligen Schlüssel.
So bleibt die Ausgabe auch dann sinnvoll, wenn nicht für jeden Wert eine eigene Regel definiert wurde.

Hinweise zu Icons
CSS-Klassen
Geeignet für vorhandene Icon-Fonts oder eigene Styles.

Beispiel:

Copybi bi-lock-fill
Text
Geeignet für einfache Textwerte oder kurze Symbole.

Beispiel:

CopyCopyright
URL
Geeignet für kleine Bild- oder SVG-Dateien.

Beispiel:

Copyhttps://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg
Hinweise zu externen Bild-/SVG-URLs
Externe Icon-URLs funktionieren grundsätzlich, sind aber abhängig von:

Erreichbarkeit des externen Hosts
Hotlinking-Regeln des Zielservers
Browser-/CSP-Verhalten
Langfristig kann es sinnvoll sein, häufig verwendete Icons direkt lokal im Modul zu verwalten.

Kompatibilität / Datenbank-Hinweis
Das Modul speichert Badge-Regeln als JSON in den webtrees-Moduleinstellungen.

In Umgebungen mit älteren MySQL-/MariaDB-Kollationen kann die Speicherung bestimmter Unicode-/Emoji-Zeichen problematisch sein.
Deshalb sind für Icons meist CSS-Klassen oder Bild-/SVG-URLs die robustere Wahl.

Projektstruktur
Copymodules_v4/media-badge/
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
Entwicklungsschwerpunkte der aktuellen Beta
zuverlässiges Laden des Moduls
stabile Speicherung der NOTE-Schlüssel
Regelverwaltung im Admin-Bereich
flexible Badge-Darstellung
Unterstützung von Text-, Klassen- und URL-Icons
Vermeidung früherer HTTP-500- und Speicherprobleme
Bekannte Einschränkungen
externe SVG-/Bild-URLs hängen vom jeweiligen Host ab
die Badge-Ausgabe ist aktuell auf die Medienseite fokussiert
weitere Ansichten (z. B. Listen, Galerien, andere Record-Kontexte) sind perspektivisch erweiterbar
Roadmap / Nächste Ideen
Mögliche nächste Ausbaustufen:

lokale Icons innerhalb des Moduls
zusätzliche Badge-Ausgabe in Listenansichten
bessere Vorschau in der Admin-Oberfläche
Import/Export von Badge-Regeln
feinere Validierung für URL- und Regex-Regeln
Lizenz
Noch festzulegen.

Empfehlung: GPLv3, passend zum webtrees-Umfeld.

Mitwirken
Feedback, Ideen und Tests sind willkommen – insbesondere zu:

sinnvollen Regeltypen
typischen Shared-Note-Strukturen
sinnvollen Standard-Presets
UI/UX der Regelverwaltung
realen Anwendungsfällen für Medienkennzeichnungen
