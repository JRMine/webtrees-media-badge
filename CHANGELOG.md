# Changelog

All notable changes to this project are documented in this file.

## [0.2.0-beta.1] - 2026-05-24

### Added
- Created the initial structure of the `media-badge` module
- Added the `module.php` entry point
- Added the `MediaBadgeModule.php` module class
- Added the CSS file `resources/css/media-badge.css`
- Added an initial customized `media-page.phtml` for badge output
- Added support for reading `NOTE` facts from media objects
- Added support for linked shared notes
- Implemented detection of tagged note lines such as `MEDIA LICENCE: ...`
- Introduced initial badge resolution for media objects
- Added configurable note keys
- Added the first badge rule management foundation for the module
- Created admin views for settings and badge rules:
  - `admin/config.phtml`
  - `admin/badges.phtml`
  - `admin/badge-edit.phtml`
- Added default rules for the first license badges
- Implemented a fallback badge for values without a specific match

### Changed
- Switched media title output from a static license display to rule-based badge rendering
- Prepared the module structure for future extensibility with additional badge types
- Expanded CSS from a simple starting version to include badge and admin styling

### Fixed
- Added the module entry point required for loading the module in webtrees
- Prepared the basic structure for admin pages so the first configuration pages could be tested

## [0.3.0-beta] - 2026-05-24

### Added
- Extended badge rules with configurable render modes:
  - `text`
  - `icon`
  - `icon-text`
  - `auto`
- Added support for additional icon types:
  - CSS classes (`class`)
  - text values (`text`)
  - image / SVG URLs (`url`)
- Added extended rule options for:
  - badge text (`label_mode`)
  - tooltip (`tooltip_mode`)
  - position before / after the media title
  - sort order
- Expanded badge rule management in the admin area
- Added badge rule previews to the admin overview
- Added support for image / SVG icons in the media view
- Added direct navigation from module configuration to badge rule management

### Changed
- Updated default badge rules to use more robust default values
- Improved separation of badge content and presentation:
  - content comes from shared notes / `NOTE` entries
  - presentation is controlled through rules
- Stabilized configuration of global `NOTE` keys
- Added support for multiple global `NOTE` keys processed line by line
- Extended and made badge rendering on the media page more flexible

### Fixed
- Fixed issues with saving module settings
- Corrected the internal module name for custom modules to match webtrees requirements
- Fixed HTTP 500 / fatal error caused by duplicate method definitions in `MediaBadgeModule.php`
- Fixed problems with badge display after saving `NOTE` keys
- Removed problematic emoji-based default values
- Made badge rule storage more robust to avoid issues with database / collation limits

### Notes
- This version is still intended as a pre-release / beta
- External image / SVG URLs as icons are supported, but depend on the availability of the respective host
- A possible next enhancement would be support for local icons within the module


## [0.4.0-beta] - 2026-05-26

### Added

- Added badge rendering beyond the media detail page.
- Added badge support for the media list page.
- Added badge support for linked media tables on record-related pages through the shared media table view.
- Added badge support for the standard multimedia tab on individual pages.
- Added badge support for the Album view in the standard theme.
- Added badge support for the random media slideshow / homepage slideshow.
- Added reusable shared badge view components for centralized badge output.
- Added support for rendering badges consistently across multiple media-related contexts using the same rule system.
- Added support for multiple global NOTE keys in module configuration.

### Changed

- Refactored badge rendering so badge output is no longer duplicated unnecessarily across multiple views.
- Centralized reusable badge markup into shared rendering components.
- Reused the same badge rules and resolution logic across all supported output contexts.
- Improved project documentation for the current beta feature set.
- Updated README and technical documentation to reflect the current architecture and supported contexts.

### Fixed

- Fixed persistence and rendering issues introduced during earlier badge rendering refactors.
- Fixed album badge rendering in the standard theme.
- Fixed multimedia tab badge rendering for linked media records.
- Fixed view-level issues caused by ineffective imports in custom templates.
- Improved stability of badge output across different page contexts.
- Preserved existing badge rendering on the media detail page without regression.

### Notes

- Generic core search results do not currently expose a standard media result view, so no media-search integration was added in this release.
- Theme-specific conflicts may still exist when other themes override the same core media templates, especially in album-related contexts.
- Badge visibility by user group or access level is not part of this release and remains planned for a future version.

### Known limitations

- Theme compatibility is currently best with the standard webtrees theme.
- Themes that override media-related core views may require additional compatibility work.
- Badge visibility control by user role, access level, or page-specific visibility rules is not yet implemented.

### Notes
- Dieses Release ist eine erste testbare Vorabversion
- Fokus liegt auf Architektur, Modul-Loading, Badge-Erkennung und erster Admin-Struktur
- Noch nicht als stabile Produktivversion gedacht
