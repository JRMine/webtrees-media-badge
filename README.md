# Media Badge

**Media Badge** is a custom module for webtrees that displays information from linked media notes (`NOTE`) as visual badges directly next to the media title.

The module is designed to make reusable information from Shared Notes — such as licences, rights, status markers, or other labels — visible and flexible, without requiring users to open the detail view to find them.

## Status

Beta / Pre-release

The module is already testable and generally usable in everyday production workflows, but it is still under active development.

## Features

### Media badges from NOTE entries

The module scans notes linked to a media object for configurable keys, for example:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

The value after the key can be displayed as a badge directly next to the media title.

### Multiple global NOTE keys

Multiple keys can be defined in the module configuration — one key per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

This allows the module to read different kinds of metadata from notes.

### Configurable badge rules

Rules can be defined for detected values to control:

- whether a rule is active
- which key it applies to
- how values are matched
- how the badge is rendered
- in which order it appears
- whether it is shown before or after the title
- which tooltip is displayed
- which CSS classes are used

### Supported match types

Rules can be applied to values in different ways:

- any value
- `exact`
- `contains`
- `regex`

This makes it possible to define both generic rules and very specific special cases.

### Supported render modes

A badge can be rendered in different ways:

- `text` — text only
- `icon` — icon only
- `icon-text` — icon and text
- `auto` — automatic choice based on the rule

### Supported icon types

Icons can be provided in different ways:

- `class` — CSS class, for example for icon fonts
- `text` — plain text
- `url` — image or SVG URL

This allows badges to be displayed either as plain text or with graphical symbols.

### Configurable badge text and tooltip

Each rule can define:

- badge text:
  - use the value from the note
  - show fixed text
  - show no text
- tooltip:
  - generate automatically from key and value
  - use a fixed tooltip
  - show no tooltip

### Prioritized rule selection

If multiple rules match the same value, the module selects the most appropriate rule by priority:

1. `exact`
2. `contains`
3. `regex`
4. generic key rule
5. fallback

This keeps the output predictable and easy to control.

## Example

### Example Shared Note

    MEDIA LICENCE: CC BY 4.0

### Possible badge output

- text badge: `CC BY 4.0`
- colored badge with tooltip
- icon + text
- SVG icon + text

## Installation

1. Copy the module folder to `modules_v4/media-badge/`
2. Open webtrees
3. Activate the **Media Badge** module in the control panel
4. Open the configuration page
5. Add the desired NOTE keys
6. Create badge rules or use the default rules

## Configuration

### Define NOTE keys

In the module configuration, you can define the keys that should be searched for in media notes.

Important: Multiple keys are supported — enter one key per line.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

### Define badge rules

In the rule management page, rules can be created, edited, and deleted.

Typical rule fields include:

- key
- match type
- match value
- render mode
- icon type
- icon value
- label mode
- tooltip mode
- CSS classes
- position
- sort order

Further details are documented in the files under `docs/`.

## Recommended use cases

The module is especially useful for:

- media licences
- usage rights
- publication notices
- editorial status
- source labels
- visibility markers
- quality markers

## Default behavior

If no specific rule matches, the module uses a generic default rule for the corresponding key.

This ensures meaningful output even if not every possible value has its own dedicated rule.

## Notes on icons

### CSS classes

Suitable for existing icon fonts or custom styles.

Example:

    bi bi-lock-fill

### Text

Suitable for simple text values or short symbols.

Example:

    Copyright

### URL

Suitable for small image or SVG files.

Example:

    https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg

## Notes on external image and SVG URLs

External icon URLs generally work, but they depend on:

- availability of the external host
- hotlinking rules of the target server
- browser or CSP behavior

In the long term, it may be preferable to manage frequently used icons locally inside the module.

## Compatibility and database note

The module stores badge rules as JSON in the webtrees module settings.

In environments with older MySQL or MariaDB collations, storing certain Unicode or emoji characters may cause problems. For that reason, CSS classes or image and SVG URLs are usually the more robust choice for icons.

## Project structure

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

## Focus areas of the current beta

- reliable module loading
- stable storage of NOTE keys
- admin rule management
- flexible badge rendering
- support for text, class, and URL icons
- prevention of earlier HTTP 500 and storage issues

## Known limitations

- external SVG or image URLs depend on the respective host
- badge output is currently focused on the media page
- additional views such as lists, galleries, or other record contexts can be added later

## Roadmap / Next ideas

Possible next steps include:

- local icons inside the module
- additional badge output in list views
- better preview in the admin interface
- import and export of badge rules
- stricter validation for URL and regex rules
- configurable visibility per page, key, or user group

## License

To be decided.

Recommended: GPLv3, to match the webtrees ecosystem.

## Contributing

Feedback, ideas, and testing are welcome — especially regarding:

- useful rule types
- typical Shared Note structures
- sensible default presets
- UI/UX of the rule editor
- real-world use cases for media labels

## Additional documentation

- `docs/configuration.md`
- `docs/rule-model.md`
- `docs/architecture.md`
