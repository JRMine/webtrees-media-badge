# Media Badge

**Media Badge** is a custom module for [webtrees](https://github.com/fisharebest/webtrees) that displays information from linked media notes (`NOTE`) as visual badges directly next to media titles.

The module is designed to make reusable information from Shared Notes — such as licences, rights, status markers, or other labels — visible directly where media objects are shown.

Instead of hiding this information only in the detail view of a media object, Media Badge brings it into lists, tabs, album views, and other media-related contexts.

## Status

**Beta / Pre-release**

The module is already testable and generally usable in real-world scenarios, but it is still under active development on the way to version 1.0.

Current release status:

- `0.5.0-beta`

## Features

### Badge output from media notes

The module reads configurable keys from media-related `NOTE` content.

Example:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

The value after the key can be displayed directly next to the media title as a badge.

### Multiple global NOTE keys

The module configuration allows multiple keys to be defined, one per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

This allows the module to extract different kinds of reusable metadata from Shared Notes.

### Configurable badge rules

Badge rules control how detected values are displayed.

Rules can define:

- whether the rule is enabled
- which key it applies to
- how values are matched
- how the badge is rendered
- whether the badge appears before or after the title
- sort order
- tooltip behavior
- the CSS classes to use
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

- `class` — CSS class names, for example for icon fonts
- `text` — simple text-based icons
- `url` — image or SVG URLs

### Reusable badge rendering

Badge rendering has been centralized so the same output logic can be reused across multiple views and media contexts.

This reduces duplication and makes future extensions easier.

## Supported output contexts

As of `0.5.0-beta`, Media Badge can output badges in these contexts:

- media detail page
- media list page
- linked media tables on record pages
- standard multimedia tab
- album / gallery view in the standard theme
- random media / homepage slideshow
- media objects shown in facts and events views
- nested media references inside facts and events

This means badges are no longer limited to the media detail page.

## Example

### Example shared note

    MEDIA LICENCE: CC BY 4.0

### Possible output

- text badge: `CC BY 4.0`
- colored badge with tooltip
- icon + text
- SVG icon + text

## Installation

1. Copy the module folder to one of the following locations:

   ```text
   modules_v4/media-badge/
   modules_v4/webtrees-media-badge/
   ```

   The second form is useful when installing the repository archive directly from GitHub.

2. Open webtrees.

3. Enable the module in the control panel.

4. Open the module configuration.

5. Enter one or more global NOTE keys.

6. Create badge rules or use the default rules.

## Configuration

### Global NOTE keys

The module configuration allows one or more keys to be defined that should be searched in media notes.

Important: enter one key per line.

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

Further details are documented in the files under `docs/`.

## Default behavior

If no badge rules are stored, the module uses a built-in default rule set.

This includes:

- a generic fallback rule for the primary key
- specific rules for `CC BY 4.0`
- specific rules for `CC BY-SA 4.0`
- specific rules for `Public Domain`
- a rule for values containing `private`

If no specific rule matches an extracted value, the module can still output a generic badge for that key.

### Page-context visibility

The visibility of a badge rule can be controlled per page context.

The following contexts are currently supported:

- `all`
- `media-page`
- `media-list`
- `linked-media-table`
- `album-tab`
- `media-tab`
- `random-media-slide-show`

If `page_contexts` is missing or empty, the rule is treated as visible on all supported contexts.

If `all` is set, additional individual contexts have no further effect.

## Recommended use cases

Media Badge is especially useful for:

- media licences
- usage rights
- publication status
- editorial status
- internal workflow markers
- source markers
- visibility markers
- quality indicators

## Notes on icons

### CSS classes

Suitable for icon fonts or existing project styles.

Example:

    bi bi-lock-fill

### Text

Suitable for simple short values.

Example:

    Copyright

### URL

Suitable for small image or SVG files.

Example:

    https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg

## Notes on external image and SVG URLs

External image or SVG URLs generally work, but depend on:

- availability of the external host
- hotlinking restrictions
- browser behavior
- content security policy settings

For frequently used icons, it may become preferable in future versions to manage local assets inside the module.

## Compatibility

The module is currently best tested with the **standard webtrees theme**.

Important notes:

- theme-specific differences may still occur when a theme overrides the same core views
- album / gallery rendering in the standard theme is supported
- for selected conflict scenarios with themes or third-party modules, the module prefers small reusable rendering fragments over full template ownership
- optional compatibility with other modules can be added in a targeted way without introducing hard runtime dependencies

### Compatibility with Source Transcription

For installations using the `hh_source_transcription` module, Media Badge includes optional compatibility for selected media-related views.

If both modules override the same webtrees views, Media Badge attempts to render the source-transcription badges defensively as an additional fragment when the other module is available.

If the module is not installed or its service cannot be resolved, no additional output is generated.

## Database and storage note

Badge rules are stored as JSON in the webtrees module settings.

In environments with older MySQL or MariaDB collations, certain Unicode or emoji characters may cause problems. For that reason, CSS classes or image / SVG URLs are usually the more robust choice for icons.

## Documentation

Additional documentation is available in:

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`

## Project structure

See `docs/architecture.md`.

## Current beta focus

The current beta focuses on:

- reliable module loading
- stable persistence of NOTE keys
- reusable badge rendering
- support for multiple media-related page contexts
- flexible badge output using text, icons, and URLs
- reduction of duplicated rendering logic
- providing a solid foundation for future visibility and permission rules

## Known limitations

- theme-specific conflicts may still occur when a theme uses its own variants of the same media-related templates
- depending on the theme, additional compatibility work may be needed for some views
- badge visibility by user or group is not yet implemented
- management of local custom icon assets is not yet fully developed

## Roadmap

Possible next development steps include:

- badge visibility by user group / access level
- local icon assets instead of external URLs
- improved admin preview
- import / export of badge rules
- stricter validation for URL and regex rules
- further theme compatibility
- additional badge sources besides NOTE, such as other GEDCOM fields or metadata

## License

To be decided.

Recommended:

- GPLv3, to fit the webtrees ecosystem

## Contributing

Feedback, ideas, and testing are welcome, especially regarding:

- useful rule types
- real-world Shared Note structures
- useful default presets
- UI and UX of the rule editor
- media-related contexts that should support badges
- future visibility and permission logic

## Release notes

The latest changes can be found in:

- `CHANGELOG.md`
