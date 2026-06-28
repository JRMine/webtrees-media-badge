# Media Badge

**Media Badge** is a custom module for [webtrees](https://github.com/fisharebest/webtrees) that displays information from linked media notes (`NOTE`) as visual badges next to media titles.

The module is designed to make reusable information from Shared Notes — such as licences, rights, status markers, or other labels — visible directly where media objects are shown.

Instead of hiding that information inside media detail pages, Media Badge brings it into lists, tabs, album views, and other media-related contexts.

## Status

**Beta / Pre-release**

The module is already usable and testable in real-world setups, but it is still under active development on the road to version 1.0.

Current release target:

- `0.4.0-beta`

## Features

### Badge output from media notes

The module reads configured keys from media-related `NOTE` content.

Example:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

The value after the key can be rendered as a badge next to the media title.

### Multiple global NOTE keys

You can define multiple keys in the module configuration, one per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

This allows the module to extract different kinds of reusable metadata from Shared Notes.

### Configurable badge rules

Badge rules control how matching values are rendered.

Rules can define:

- whether the rule is enabled
- which key the rule applies to
- how values are matched
- how the badge is rendered
- whether the badge appears before or after the title
- sort order
- tooltip behavior
- CSS classes
- icon behavior
- fixed labels or value-based labels

### Supported match types

Rules currently support:

- any value
- `exact`
- `contains`
- `regex`

This allows both generic key-based rules and highly specific value-based rules.

### Supported render modes

Badges can be rendered as:

- `text`
- `icon`
- `icon-text`
- `auto`

### Supported icon types

Icons can be provided as:

- `class` — CSS class names, for example icon-font classes
- `text` — simple text-based icons
- `url` — image or SVG URLs

### Reusable badge rendering

Badge rendering has been centralized so the same output logic can be reused across multiple views and media contexts.

This reduces duplication and makes future expansion easier.

## Supported output contexts

As of `0.4.0-beta`, Media Badge can render badges in these contexts:

- media detail page
- media list page
- linked media tables on record-related pages
- standard multimedia tab
- album view in the standard theme
- random media slideshow / homepage slideshow

This means badges are no longer limited to the media detail page.

## Example

### Shared Note

    MEDIA LICENCE: CC BY 4.0

### Possible output

- text badge: `CC BY 4.0`
- colored badge with tooltip
- icon + text
- SVG icon + text

## Installation

1. Copy the module folder to one of these locations:

       modules_v4/media-badge/
       modules_v4/webtrees-media-badge/

   The second form is useful when installing the repository archive directly
   from GitHub.

2. Open webtrees.
3. Enable the module in the control panel.
4. Open the module configuration page.
5. Enter one or more global NOTE keys.
6. Create badge rules or use the default rules.

## Configuration

### Global NOTE keys

The module configuration allows you to define one or more keys that should be searched in media notes.

Enter one key per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

### Badge rules

Badge rules can be created, edited, and deleted in the rule management area.

Typical rule fields include:

- key
- match type
- match value
- render mode
- icon type
- icon value
- label mode
- tooltip mode
- CSS class
- position
- sort order

## Default behavior

If no specific rule matches a value, the module can still fall back to a generic rule for the corresponding key.

This helps keep output consistent even when not every possible value has a dedicated rule.

## Recommended use cases

Media Badge is especially useful for:

- media licences
- usage rights
- publication status
- editorial status
- internal workflow labels
- source indicators
- visibility markers
- quality markers

## Notes on icons

### CSS class icons

Suitable for icon fonts or existing project styles.

Example:

    bi bi-lock-fill

### Text icons

Suitable for simple short values.

Example:

    Copyright

### URL icons

Suitable for small image or SVG files.

Example:

    https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg

## Notes on external image and SVG URLs

External image or SVG URLs generally work, but depend on:

- external host availability
- hotlinking restrictions
- browser behavior
- content security policy settings

For frequently used icons, local asset management may become preferable in future versions.

## Compatibility

The module is currently best tested with the **standard webtrees theme**.

Important note:

- theme-specific conflicts may occur if another theme overrides the same core media-related views
- album rendering in the standard theme is supported
- theme-specific compatibility work may still be needed for themes with their own custom media templates

## Database and storage note

Badge rules are stored as JSON in the webtrees module settings.

In environments with older MySQL or MariaDB collations, certain Unicode or emoji characters may cause problems. For that reason, CSS classes or image / SVG URLs are usually the safer icon choice.

## Documentation

Additional documentation is available in:

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`

## Project structure

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

## Current beta focus

The current beta focuses on:

- reliable module loading
- stable NOTE key persistence
- reusable badge rendering
- support for multiple media-related page contexts
- flexible badge output with text, icons, and URLs
- reduction of duplicated rendering logic
- preparing a solid foundation for future visibility and permission rules

## Known limitations

- theme-specific conflicts may still occur when other themes override the same core media templates
- media-related search integration is not included because core search does not currently expose a standard media result view
- page-context visibility control is not yet implemented
- user-group / access-level based badge visibility is not yet implemented

## Roadmap

Possible next development steps include:

- badge visibility by user group / access level
- page-context-specific badge visibility
- local icon asset support
- improved admin previews
- import / export of badge rules
- stricter validation for URL and regex rules
- additional theme compatibility work

## License

To be decided.

Recommended:

- GPLv3, to fit the webtrees ecosystem

## Contributing

Feedback, ideas, and testing are welcome, especially regarding:

- useful rule types
- real-world Shared Note structures
- default rule presets
- UI and UX of the rule editor
- media-related contexts that should support badges
- future visibility and permission handling

## Release notes

For the latest changes, see:

- `CHANGELOG.md`
