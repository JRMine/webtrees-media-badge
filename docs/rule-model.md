# Rule Model

This document describes the internal rule model used by the Media Badge module.

## Purpose

The rule model separates content from presentation.

Content comes from Shared Notes linked to media records.

Presentation is controlled by badge rules.

This means the note defines the actual value, while the rule defines how that value is displayed.

## Overview

The module processes badge output in four steps:

1. Extract note values from linked notes
2. Load and normalize configured badge rules
3. Select the best matching rule for each extracted value
4. Build the final badge output for rendering

## 1. Extracted note values

The module reads note lines based on the configured global note keys.

Example note content:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: Verified

Each matching line is converted into an internal value object with these fields:

- `key` — the configured note key, for example `MEDIA LICENCE`
- `value` — the text after the separator, for example `CC BY 4.0`
- `line` — the original full line from the note

Example extracted value:

- `key`: `MEDIA LICENCE`
- `value`: `CC BY 4.0`
- `line`: `MEDIA LICENCE: CC BY 4.0`

## 2. Rule structure

Each badge rule is stored as a normalized array in the module settings.

A rule can contain the following fields:

- `id` — unique internal identifier
- `enabled` — whether the rule is active
- `key` — note key the rule applies to
- `match_type` — how the value is matched
- `match_value` — value used for matching
- `render_mode` — how the badge should be displayed
- `icon_type` — where the icon comes from
- `icon_value` — icon class, text, or URL
- `label_mode` — how the badge label is determined
- `label` — fixed label text, if used
- `tooltip_mode` — how the tooltip is determined
- `title` — fixed tooltip text, if used
- `class` — CSS classes for badge styling
- `position` — placement relative to the media title
- `sort_order` — output order of badges

## 3. Normalized rule fields

After normalization, each rule uses a stable internal structure.

### Core fields

- `id`
- `enabled`
- `key`
- `match_type`
- `match_value`

### Presentation fields

- `render_mode`
- `icon_type`
- `icon_value`
- `label_mode`
- `label`
- `tooltip_mode`
- `title`
- `class`
- `position`
- `sort_order`

## 4. Field meanings

### `enabled`

Controls whether the rule is active.

Possible values:

- `true`
- `false`

Disabled rules are ignored completely.

### `key`

Defines which configured note key the rule applies to.

Examples:

- `MEDIA LICENCE`
- `MEDIA RIGHTS`
- `MEDIA STATUS`

A rule is only considered if its `key` matches the extracted value key.

### `match_type`

Defines how the extracted note value is compared to `match_value`.

Supported values:

- empty value — generic key rule
- `exact`
- `contains`
- `regex`

### `match_value`

The value used by `match_type`.

Examples:

- `CC BY 4.0`
- `Public Domain`
- `private`
- `^CC `

### `render_mode`

Defines how the badge is rendered.

Supported values:

- `text`
- `icon`
- `icon-text`
- `auto`

Meaning:

- `text` — output label only
- `icon` — output icon only
- `icon-text` — output icon and label
- `auto` — choose the most sensible output based on available icon and label data

### `icon_type`

Defines what kind of icon source is used.

Supported values:

- `class`
- `text`
- `url`

Meaning:

- `class` — CSS icon class, for example `bi bi-lock-fill`
- `text` — plain text icon, for example `PD`
- `url` — image or SVG URL

### `icon_value`

Contains the actual icon data.

Examples:

- `bi bi-lock-fill`
- `CC`
- `/modules_v4/media-badge/resources/icons/cc-by.svg`
- `https://example.org/icon.svg`

### `label_mode`

Defines how the badge label is created.

Supported values:

- `value`
- `fixed`
- `none`

Meaning:

- `value` — use the extracted note value
- `fixed` — use the configured `label`
- `none` — no label is displayed

### `label`

Stores fixed label text if `label_mode` is set to `fixed`.

Example:

- `Creative Commons`

### `tooltip_mode`

Defines how the tooltip is created.

Supported values:

- `auto`
- `fixed`
- `none`

Meaning:

- `auto` — generate tooltip from key and value
- `fixed` — use the configured `title`
- `none` — no tooltip is displayed

### `title`

Stores fixed tooltip text if `tooltip_mode` is set to `fixed`.

Example:

- `Creative Commons Attribution 4.0`

### `class`

CSS classes used for visual styling.

Examples:

- `mbg-badge mbg-badge--generic`
- `mbg-badge mbg-badge--private`
- `mbg-badge mbg-badge--ccby`

### `position`

Defines where the badge appears in relation to the media title.

Supported values:

- `before-title`
- `after-title`

### `sort_order`

Defines the display order within the same position.

Lower values are rendered first.

Examples:

- `10`
- `20`
- `100`

## 5. Rule normalization

The module normalizes all rules before using them.

Normalization ensures that:

- missing fields receive default values
- invalid values are replaced with safe defaults
- each rule has a unique `id`
- numeric fields are cast correctly
- empty fields are handled consistently

Typical default values include:

- `enabled` → `true`
- `match_type` → empty
- `match_value` → empty
- `render_mode` → `text`
- `icon_type` → `class`
- `icon_value` → empty
- `label_mode` → `value`
- `label` → empty
- `tooltip_mode` → `auto`
- `title` → empty
- `class` → `mbg-badge mbg-badge--generic`
- `position` → `after-title`
- `sort_order` → `0`

## 6. Matching model

The module tries to find the best matching rule for each extracted note value.

A rule only participates if:

- it is enabled
- its `key` matches the extracted key

Then the module evaluates the rule according to `match_type`.

### Generic rule

A rule with an empty `match_type` is treated as a generic rule for the key.

It matches every value of that key.

Example:

- key: `MEDIA LICENCE`
- match type: empty
- result: matches all licence values

### Exact match

Matches only when the extracted value is exactly equal to `match_value`.

Example:

- extracted value: `CC BY 4.0`
- rule match type: `exact`
- rule match value: `CC BY 4.0`
- result: match

### Contains match

Matches when the extracted value contains `match_value`.

Example:

- extracted value: `private use only`
- rule match type: `contains`
- rule match value: `private`
- result: match

### Regex match

Matches when the regular expression matches the extracted value.

Example:

- extracted value: `CC BY-SA 4.0`
- rule match type: `regex`
- rule match value: `^CC `
- result: match

## 7. Rule priority

If multiple rules match the same extracted value, the module chooses the best one by priority.

Recommended priority order:

1. `exact`
2. `contains`
3. `regex`
4. generic key rule

If two rules have the same match strength, the lower `sort_order` should win.

This allows:

- highly specific rules for known values
- broader fallback rules for the same key
- predictable behavior when several rules exist

## 8. Badge composition

Once the best rule is selected, the module builds the final badge object.

A final badge typically contains:

- `label`
- `title`
- `class`
- `position`
- `sort_order`
- `render_mode`
- `icon_type`
- `icon_value`

This final structure is what the view uses for rendering.

## 9. Label composition

The badge label is determined by `label_mode`.

### `label_mode = value`

Use the extracted note value.

Example:

- note line: `MEDIA LICENCE: CC BY 4.0`
- label result: `CC BY 4.0`

### `label_mode = fixed`

Use the rule's fixed `label`.

Example:

- rule label: `Licence`
- label result: `Licence`

### `label_mode = none`

Do not display any label.

This is useful for icon-only badges.

## 10. Tooltip composition

The badge tooltip is determined by `tooltip_mode`.

### `tooltip_mode = auto`

Create a tooltip automatically from the extracted key and value.

Example:

- key: `MEDIA LICENCE`
- value: `CC BY 4.0`
- tooltip result: `MEDIA LICENCE: CC BY 4.0`

### `tooltip_mode = fixed`

Use the configured `title`.

Example:

- title: `Creative Commons Attribution 4.0`
- tooltip result: `Creative Commons Attribution 4.0`

### `tooltip_mode = none`

Do not output a tooltip.

## 11. Render behavior

The view decides how to render the badge based on `render_mode`.

### `text`

Show text only.

Possible output:

- label only
- no icon

### `icon`

Show icon only.

Possible output:

- icon only
- no label

### `icon-text`

Show both icon and text.

Possible output:

- icon followed by label

### `auto`

Resolve automatically.

Typical behavior:

- if icon and label are available, render as icon and text
- if only icon is available, render icon only
- if only label is available, render text only

## 12. Fallback behavior

If no specific rule matches an extracted value, the module can still create a generic fallback badge.

Typical fallback behavior:

- use extracted value as label
- use a generic CSS class
- place the badge after the title
- use a high `sort_order`, for example `999`

This ensures badge output still works even when no value-specific rule exists.

## 13. Storage model

The module stores its settings in module preferences.

The main stored values are:

- `NOTE_KEYS`
- `BADGE_RULES`

### `NOTE_KEYS`

Stores the global note keys entered in module configuration.

Typical format:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

### `BADGE_RULES`

Stores the normalized badge rules as JSON.

Each rule is serialized with the fields described above.

## 14. Example rule set

A typical configuration may include:

- one generic rule for `MEDIA LICENCE`
- one exact rule for `CC BY 4.0`
- one exact rule for `Public Domain`
- one contains rule for `private`

Example logic:

- generic licence values use a neutral badge
- `CC BY 4.0` gets a specific icon and class
- `Public Domain` gets a specific icon and class
- values containing `private` get a warning style

## 15. Design goals

The rule model is designed to support:

- multiple configurable note keys
- generic and specific rules
- stable fallback behavior
- flexible presentation
- future extension without changing the note format

## 16. Future extensions

The current rule model can be extended later with additional controls such as:

- page context visibility
- per-key visibility on specific views
- user group visibility
- icon presets
- local icon file selection
- import and export of rules
- grouped rule management in the admin UI

## 17. Summary

The Media Badge rule model follows a simple principle:

- notes provide the content
- rules provide the presentation

This keeps Shared Notes reusable and allows badges to be displayed in a flexible and configurable way across different media-related contexts.

## Related documentation

- `docs/configuration.md`
- `docs/architecture.md`
