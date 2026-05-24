# Media Badge

**Media Badge** ist ein benutzerdefiniertes Modul für [webtrees](https://www.webtrees.net/), das Informationen aus an Medienobjekte verknüpften Notizen ausliest und als visuelle Badges neben dem Medientitel anzeigt.

Der erste Anwendungsfall ist die Anzeige von Lizenzinformationen, die in einer Shared Note oder Inline-Note mit einem Präfix wie `MEDIA LICENCE:` hinterlegt sind.

Beispiel:
MEDIA LICENCE: CC BY 4.0
Daraus kann auf der Medienseite automatisch ein Badge wie CC BY 4.0 erzeugt werden.

Ziel des Projekts
In vielen webtrees-Installationen werden Lizenzinformationen, Nutzungsrechte oder andere medienbezogene Hinweise bereits als Notizen gepflegt, sind aber nur in der Detailansicht sichtbar und gestalterisch wenig präsent.

Dieses Modul soll solche Informationen:

direkt auf der Medienseite sichtbar machen,
optisch ansprechend als Badge darstellen,
später flexibel konfigurierbar machen,
und perspektivisch auch in weiteren Medienansichten nutzbar machen.
Geplanter Funktionsumfang
Die Entwicklung ist in mehreren Ausbaustufen gedacht.

Version 1: Minimaler Startpunkt
Die erste Version konzentriert sich auf einen klaren Anwendungsfall:

Auslesen von NOTE-Fakten eines Medienobjekts
Unterstützung von verknüpften Shared Notes und Inline-Notes
Erkennung von Zeilen im Format MEDIA LICENCE: ...
Anzeige des extrahierten Werts als Badge neben dem Medientitel auf der Medienseite
Version 2: Konfigurierbare Badge-Regeln
In der nächsten Ausbaustufe soll das Modul eine eigene Verwaltungsoberfläche erhalten, ähnlich dem Ansatz von Vesta bei den „Name badges“.

Geplant sind unter anderem:

eigene Einstellungsseite im Modul
Badge-Regeln hinzufügen, bearbeiten und löschen
konfigurierbare Erkennungsschlüssel, z. B.
MEDIA LICENCE:, MEDIA RIGHTS:, MEDIA STATUS:
verschiedene Match-Typen, z. B.
exakte Übereinstimmung, Teilstring oder regulärer Ausdruck
konfigurierbare Badge-Texte, Farben und CSS-Klassen
Reihenfolge und Position von Badges
Mögliche spätere Erweiterungen
Langfristig kann das Modul über die Medien-Detailseite hinaus erweitert werden, etwa für:

Medienlisten
Thumbnail-Ansichten
Album-/Galerieansichten
verknüpfte Medien in Personen-, Familien- oder Quellenansichten
Projektstruktur
Aktuell ist folgende Struktur vorgesehen:

Copymodules_v4/media-badge/
├── MediaBadgeModule.php
├── metadata.json
└── resources/
    ├── css/
    │   └── media-badge.css
    └── views/
        ├── media-page.phtml
        └── admin/
            ├── config.phtml
            ├── badges.phtml
            └── badge-edit.phtml
Die Datei media-page.phtml überschreibt die Standardansicht der Medienseite und ergänzt dort die Badge-Anzeige.
Die Admin-Views sind für die geplante Konfigurationsoberfläche vorgesehen.

Beispiel für die Datennutzung
Beispiel einer Shared Note
CopyMEDIA LICENCE: CC BY 4.0
Quelle: Beispielarchiv
Erwartetes Verhalten
Das Modul liest die Notiz aus, erkennt die Zeile mit MEDIA LICENCE: und zeigt auf der Medienseite ein Badge mit dem Text CC BY 4.0 an.

Konzeptioneller Ansatz
Das Modul verfolgt zwei getrennte Schritte:

1. Extraktion
Zuerst wird aus einer Notiz ein strukturierter Wert extrahiert, zum Beispiel:

CopyMEDIA LICENCE: CC BY 4.0
→ extrahierter Wert: CC BY 4.0

2. Darstellung
Danach wird dieser Wert gegen konfigurierbare Badge-Regeln geprüft und in ein sichtbares Badge umgewandelt, zum Beispiel:

CC BY 4.0 → blaues Badge
CC BY-SA 4.0 → violettes Badge
Public Domain → grünes Badge
Private / no reuse → rotes Badge
Dadurch bleibt die Datenerfassung im GEDCOM-/Notizmodell flexibel, während die Darstellung gezielt konfiguriert werden kann.

Geplantes Regelmodell
Für konfigurierbare Badge-Regeln ist derzeit ungefähr folgendes Datenmodell vorgesehen:

Copy[
    [
        'enabled'     => true,
        'key'         => 'MEDIA LICENCE',
        'match_type'  => 'exact',
        'match_value' => 'CC BY 4.0',
        'label'       => 'CC BY 4.0',
        'class'       => 'mbg-badge--ccby',
        'sort_order'  => 10,
        'position'    => 'after-title',
    ],
]
Dieses Modell ist noch nicht final, bildet aber die geplante Richtung gut ab.

Installation
Geplante Installation
Das Modul wird wie ein übliches benutzerdefiniertes webtrees-Modul im Verzeichnis modules_v4/ abgelegt.

Beispiel:

Copymodules_v4/media-badge/
Nach dem Kopieren des Modulordners kann das Modul in webtrees im Kontrollzentrum unter den Modulen aktiviert werden.

Hinweis: Die genaue Aktivierung und eventuelle Composer-/Autoload-Anpassungen hängen von der finalen Modulstruktur ab.

Aktueller Status
Dieses Projekt befindet sich noch in der Entwicklung.

Der aktuelle Fokus liegt auf:

einem sauberen Minimalbeispiel
einer klaren Projektstruktur
einer späteren Erweiterbarkeit
einer Admin-Oberfläche für frei definierbare Badge-Regeln
Das Modul ist also bewusst als kleine, erweiterbare Basis gedacht und nicht als bereits vollständig abgeschlossenes Produkt.

Entwicklungsziele
Bei der Entwicklung stehen folgende Ziele im Vordergrund:

möglichst geringe Eingriffe in bestehende Datenstrukturen
Nutzung vorhandener webtrees-Mechanismen
saubere Trennung zwischen Datenauswertung und Darstellung
einfache Erweiterbarkeit für weitere Medien-Metadaten
gute Wartbarkeit auch bei späteren Feature-Erweiterungen
Mögliche Konfigurationsoptionen
Für die spätere Admin-Seite sind unter anderem folgende Optionen denkbar:

Standard-Präfix für auszulesende Notizzeilen
mehrere Badge-Regeln
Groß-/Kleinschreibung ignorieren
Standardstil für unbekannte Werte
Badge-Position relativ zum Medientitel
Anzeige nur auf Detailseiten oder zusätzlich in Listen
Beispielhafte Anwendungsfälle
Neben Lizenzen könnten später auch andere Informationen als Badge dargestellt werden, zum Beispiel:

MEDIA RIGHTS: nur privat
MEDIA STATUS: Entwurf
MEDIA QUALITY: ungeprüft
MEDIA SOURCE: Archivkopie
Damit wäre das Modul nicht auf Lizenzhinweise beschränkt, sondern grundsätzlich für strukturierte Medienmarkierungen nutzbar.

Roadmap
Kurzfristig
Grundmodul anlegen
Medienseite überschreiben
MEDIA LICENCE: aus Shared Notes auslesen
Badge neben dem Medientitel anzeigen
einfache CSS-Styles bereitstellen
Mittelfristig
eigene Modulkonfiguration
Badge-Regeln verwalten
Regeln aktivieren/deaktivieren
Farben und Klassen definieren
Match-Typen konfigurieren
Langfristig
Badges in weiteren Medienansichten
bessere Mehrfachregel-Unterstützung
eventuell Export/Import von Regeldefinitionen
Lizenz
Noch festzulegen.

Empfehlenswert wäre eine Lizenz, die mit dem üblichen webtrees-Modul-Ökosystem kompatibel ist, zum Beispiel GPLv3.

Mitwirken
Ideen, Verbesserungsvorschläge und Beiträge sind willkommen.

Besonders interessant sind Rückmeldungen zu:

sinnvoller Struktur für Badge-Regeln
geeigneter Admin-Oberfläche
typischen Notizformaten in realen webtrees-Installationen
weiteren sinnvollen Einsatzfällen neben Lizenzangaben
