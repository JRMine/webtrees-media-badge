# Rule Model

This document describes the internal rule model used by the Media Badge module.

## Purpose

The rule model separates **content** from **presentation**.

- Content comes from note values attached to media records.
- Presentation is controlled by configurable badge rules.

This means the note stores the actual information, while the rule model decides how that information is rendered, where it is visible, and which visual style is applied.

The same note value can therefore be reused across multiple media-related views without changing the source note format.

## Overview

The module resolves badge output in six logical steps:

1. Extract tagged note values from the media record
2. Load stored badge rules from module settings
3. Normalize rules into a stable internal structure
4. Filter and rank candidate rules by key, value, and page context
5. Compose the final badge payload
6. Render the prepared badge data in shared view components

This keeps rule matching centralized and prevents individual views from having to understand badge logic themselves.

## 1. Extracted note values

The module reads note lines based on the configured global note keys.

Example note content:

```text
MEDIA LICENCE: CC BY 4.0
MEDIA RIGHTS: Public Domain
MEDIA STATUS: Verified
```

Each matching line is converted into an internal value object with these fields:

- `key` — the configured note key, for example `MEDIA LICENCE`
- `value` — the text after the separator, for example `CC BY 4.0`
- `line` — the original full line, for example `MEDIA LICENCE: CC BY 4.0`

Example extracted value:

```text
key   = MEDIA LICENCE
value = CC BY 4.0
line  = MEDIA LICENCE: CC BY 4.0
```

## 2. Rule structure

Each badge rule is stored as a normalized array in the module settings.

A rule can contain the following fields:

- `id` — unique internal identifier
- `enabled` — whether the rule is active
- `key` — note key the rule applies to
- `match_type` — how the note value is matched
- `match_value` — value used for matching
- `render_mode` — how the badge is displayed
- `icon_type` — where the icon comes from
- `icon_value` — icon class, text, or URL
- `label_mode` — how the visible label is determined
- `label` — fixed label text, if used
- `tooltip_mode` — how the tooltip is determined
- `title` — fixed tooltip text, if used
- `class` — CSS classes used for styling
- `position` — placement relative to the media title
- `sort_order` — output order within the same position
- `page_contexts` — allowed page contexts for rendering

## 3. Normalized rule fields

After normalization, every rule uses a stable internal structure.

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

### Visibility fields

- `page_contexts`

## 4. Field meanings

### `enabled`

Controls whether the rule is active.

Supported values:

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

Contains the value used by `match_type`.

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

- `text` — show label only
- `icon` — show icon only
- `icon-text` — show icon and label
- `auto` — choose automatically based on available icon and label data

### `icon_type`

Defines what kind of icon source is used.

Supported values:

- `class`
- `text`
- `url`

Meaning:

- `class` — CSS icon class, for example `bi bi-lock-fill`
- `text` — plain text icon, for example `CC`
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
- `none` — do not display a label

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

- `auto` — generate the tooltip from key and value
- `fixed` — use the configured `title`
- `none` — do not output a tooltip

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

Defines where the badge appears relative to the media title.

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

### `page_contexts`

Defines on which page contexts the rule may be used.

Supported values typically include:

- `all`
- `media-page`
- `media-list`
- `linked-media-table`
- `album-tab`
- `media-tab`
- `random-media-slide-show`

Meaning:

- `all` — the rule is valid everywhere
- specific context names — the rule is only valid in those contexts

If a rule is not allowed in the current page context, it must not be used for badge output.

## 5. Rule normalization

The module normalizes all rules before using them.

Normalization ensures that:

- missing fields receive default values
- invalid values are replaced with safe defaults
- each rule has a stable `id`
- numeric fields are cast consistently
- empty fields are handled predictably
- visibility fields use a consistent internal structure

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
- `page_contexts` → `["all"]`

### Notes on normalizing `page_contexts`

The module should normalize `page_contexts` with the following behavior:

- if `page_contexts` is missing, it becomes `["all"]`
- if `page_contexts` is empty, it becomes `["all"]`
- invalid or unknown context names are removed
- if `all` is present, other context names are redundant and may be discarded internally

This preserves backward compatibility for older saved rules.

## 6. Matching model

The module tries to find the best matching rule for each extracted note value.

A rule only participates if:

- it is enabled
- its `key` matches the extracted key
- it is allowed in the current page context

Only then is the rule evaluated according to `match_type`.

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

## 7. Page-context filtering

Page-context filtering is part of normal rule resolution, not a future extension.

After key and value checks, the rule must also be valid for the current page context.

Example:

- rule key: `MEDIA STATUS`
- rule page contexts: `["media-page"]`
- current page context: `album-tab`
- result: rule must not be used

This filtering happens before the final badge object is built.

## 8. Rule priority

If multiple rules match the same extracted value, the module chooses the best one by priority.

Recommended priority order:

1. `exact`
2. `contains`
3. `regex`
4. generic key rule

If two rules have the same match strength, the lower `sort_order` wins.

This allows:

- highly specific rules for known values
- broader fallback rules for the same key
- predictable behavior when several rules exist

Page-context visibility does not replace rule priority. It reduces the candidate set before priority is applied.

## 9. Badge composition

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

The page context is normally resolved before this step and does not need to remain in the final badge payload.

## 10. Label composition

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

## 11. Tooltip composition

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

If a rule is not visible in the current page context, no tooltip is generated because no badge is rendered at all.

## 12. Render behavior

The shared badge view decides how to render the badge based on `render_mode`.

### `text`

Show text only.

### `icon`

Show icon only.

### `icon-text`

Show both icon and text.

### `auto`

Resolve automatically.

Typical behavior:

- if icon and label are available, render icon and text
- if only icon is available, render icon only
- if only label is available, render text only

Render behavior only applies after rule selection and visibility filtering are complete.

## 13. Fallback behavior

If no specific rule matches an extracted value, the module may still create a generic fallback badge.

Typical fallback behavior uses:

- the extracted value as label
- a generic CSS class
- `after-title` as position
- a high `sort_order`, for example `999`

### Effective fallback rules

The implemented behavior should be understood in three cases:

1. **No rules exist for the key**
   - create a generic fallback badge
2. **Rules exist for the key, but none are visible in the current context**
   - render nothing
3. **Visible rules exist for the key, but none match the current value**
   - create a generic fallback badge

This prevents badges from appearing in contexts where they were intentionally disabled, while still preserving sensible output where no specific value rule exists.

## 14. Storage model

The module stores its settings in module preferences.

The main stored values are:

- `NOTE_KEYS`
- `BADGE_RULES`

### `NOTE_KEYS`

Stores the global note keys entered in module configuration.

Typical format:

```text
MEDIA LICENCE
MEDIA RIGHTS
MEDIA STATUS
```

### `BADGE_RULES`

Stores the normalized badge rules as JSON.

Each rule is serialized with the fields described above, including `page_contexts`.

## 15. Example rule set

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

With page-context visibility, the same rule set could also define:

- licence badges visible on all media-related pages
- status badges visible only on the media detail page
- rights badges visible only on selected linked-media contexts

## 16. Design goals

The rule model is designed to support:

- multiple configurable note keys
- generic and specific rules
- stable fallback behavior
- flexible presentation
- context-aware visibility
- extension without changing the note format

## 17. Future extensions

The current rule model can be extended later with additional controls such as:

- user-group visibility
- access-level visibility
- icon presets
- local icon file selection
- import and export of rules
- grouped rule management in the admin UI
- additional badge sources beyond note values

## 18. Summary

The Media Badge rule model follows a simple principle:

- notes provide the content
- rules provide the presentation
- page-context visibility controls where a rule may appear

This keeps note content reusable and allows badges to be displayed in a flexible, centralized, and configurable way across different media-related contexts.

## Related documentation

- `docs/configuration.md`
- `docs/architecture.md`
