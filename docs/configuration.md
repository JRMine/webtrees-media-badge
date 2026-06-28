# Configuration

This document explains how to configure the **Media Badge** module in webtrees.

The module reads tagged values from media-related `NOTE` facts and turns them into visual badges shown next to the media title.

Configuration happens on two levels:

1. Global note keys
2. Badge rules

Global note keys define **what to read**.  
Badge rules define **how to display it**.

---

## 1. Global note keys

Global note keys tell the module which tagged note lines should be recognized.

Each key must be entered on its own line in the module configuration.

Example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

With this configuration, the module will scan linked media notes for lines such as:

    MEDIA LICENCE: CC BY 4.0
    MEDIA RIGHTS: Public Domain
    MEDIA STATUS: verified

If a note line starts with one of the configured keys, the part after the colon is treated as the badge value.

### Important behavior

- Matching is based on the configured key followed by a colon.
- Empty lines in the configuration are ignored.
- Multiple keys are supported.
- If no custom keys are saved, the module falls back to its default key.

Default key:

`MEDIA LICENCE`

---

## 2. Badge rules

Badge rules define how detected values should be displayed.

A rule can:

- apply to all values of a given key
- apply only to specific values
- control whether text, icons, or both are shown
- define tooltip behavior
- set ordering and position
- assign CSS classes for styling
- define on which page contexts the badge may appear

If multiple rules match the same value, the module selects the best matching rule automatically.

---

## 3. Rule matching

Each detected note value is evaluated against the configured badge rules.

A rule may either be:

- generic, meaning it applies to any value of a key
- specific, meaning it only applies if the value matches a condition

Supported match types:

- empty / generic
- `exact`
- `contains`
- `regex`

### Example

A note contains:

    MEDIA LICENCE: CC BY 4.0

Possible rules:

- a generic rule for `MEDIA LICENCE`
- an exact rule for `CC BY 4.0`

The exact rule wins because it is more specific.

---

## 4. Render modes

The module supports several display modes.

### `text`

Only the badge label is shown.

### `icon`

Only the icon is shown.

### `icon-text`

Both icon and text are shown together.

### `auto`

The module decides automatically:

- if an icon value exists, use icon + text
- otherwise use text only

---

## 5. Icon types

The module supports three icon sources.

### `class`

Use a CSS class, for example from an icon font.

Example:

`bi bi-lock-fill`

### `text`

Use plain text as the icon value.

Example:

`Copyright`

This can be useful for very simple text-based markers.

### `url`

Use an external image or SVG URL.

Example:

`https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg`

This is useful for graphic icons that should not depend on a local icon font.

### Notes on URL icons

External image or SVG URLs depend on the remote host being reachable and allowing direct loading. If a URL icon does not appear, the issue may be caused by the remote server rather than the module itself.

---

## 6. Label modes

The label mode controls which text is shown inside the badge.

### `value`

Use the detected value from the note.

Example note line:

    MEDIA LICENCE: CC BY 4.0

Badge text:

`CC BY 4.0`

### `fixed`

Use the fixed text stored in the rule.

Example:

`Private`

### `none`

Do not show text at all.

This is useful for icon-only badges.

---

## 7. Tooltip modes

Tooltip mode controls the hover text.

### `auto`

Automatically generate a tooltip from key and value.

Example:

`MEDIA LICENCE: CC BY 4.0`

### `fixed`

Use the text stored in the rule’s `title` field.

Example:

`Creative Commons Attribution 4.0`

### `none`

Do not render a tooltip.

---

## 8. Position and ordering

Each rule can define where the badge appears relative to the media title.

Supported positions:

- `before-title`
- `after-title`

Each rule can also define a numeric `sort_order`.

Lower numbers are shown earlier.

Example:

- `10` appears before `20`
- `20` appears before `100`

---

## 9. Badge visibility by page context

Badge visibility can be configured per badge rule using the field `page_contexts`.

This allows badge output to be restricted not only by note key and note value, but also by **where the media object is currently shown**.

Examples:

- `MEDIA LICENCE` on the media detail page, media list, and album/gallery view
- `MEDIA STATUS` only on the media detail page
- `MEDIA RIGHTS` on the media detail page and on linked media shown on person or family pages

The existing badge resolution flow stays the same in principle:

1. read tagged NOTE values from media objects and linked Shared Notes
2. find matching badge rules
3. check visibility for the current page context
4. only render badges that are allowed in that context

Views themselves should not contain custom visibility logic.  
Instead, each view should pass its current page context into the central badge resolution.

### Configuration model

Each badge rule stores an additional field called `page_contexts`.

Example:

```json
{
  "id": "badge_123",
  "enabled": true,
  "key": "MEDIA LICENCE",
  "match_type": "exact",
  "match_value": "CC BY 4.0",
  "render_mode": "icon-text",
  "icon_type": "class",
  "icon_value": "bi bi-unlock-fill",
  "label_mode": "value",
  "label": "",
  "tooltip_mode": "auto",
  "title": "",
  "class": "mbg-badge mbg-badge--ccby",
  "position": "after-title",
  "sort_order": 10,
  "page_contexts": ["media-page", "media-list", "album-tab"]
}
```

### Supported page contexts

The following contexts are currently supported:

- `all`  
  The rule is active on all supported page types.

- `media-page`  
  Media detail page.

- `media-list`  
  Media list page.

- `linked-media-table`  
  Linked media on person, family, or other record pages where the media table is used.

- `album-tab`  
  Album, gallery, or thumbnail view.

- `media-tab`  
  Multimedia tab and related fact/event media rendering paths that use the same integration context.

- `random-media-slide-show`  
  Random media / slide-show context, for example on the home page.

More contexts can be added later if additional media-related entry points are supported.

### Backward compatibility and defaults

Existing configurations continue to work without manual migration.

The module uses the following behavior:

- if `page_contexts` is missing from a saved rule, it is treated as `["all"]`
- an empty value is also treated as `["all"]`
- invalid or unknown context values are ignored during normalization
- if `all` is present, additional individual context values have no further effect

This keeps existing rules fully functional and preserves previous output behavior by default.

### Admin UI

Page-context visibility is configured in the badge rule editor, not in the global NOTE-key configuration.

The admin UI provides:

- a page-context selection in `admin/badge-edit.phtml`
- a rule overview in `admin/badges.phtml` showing where a rule is active

This keeps context visibility attached to the individual rule where it belongs.

---

## 10. Current output scope

The module currently renders badges in these kinds of media-related output:

- media detail pages
- media list pages
- linked media tables on record pages
- album / gallery / thumbnail views
- multimedia tab output
- random media slideshow output
- media objects shown in fact and event views
- nested media references inside facts and events

Not every output path has its own dedicated page-context identifier. Some related render paths share the same configured context where this keeps the model simpler and avoids unnecessary duplication.

---

## 11. Default behavior

If no saved badge rules exist, the module uses a built-in default rule set.

This includes:

- a generic fallback rule for the primary key
- specific rules for `CC BY 4.0`
- specific rules for `CC BY-SA 4.0`
- specific rules for `Public Domain`
- a rule for values containing `private`

This ensures that a useful badge output is available even before a custom rule set has been defined.

---

## 12. Example configurations

### A. Generic licence display

Global note key:

    MEDIA LICENCE

Shared note:

    MEDIA LICENCE: CC BY 4.0

Rule:

- key: `MEDIA LICENCE`
- match type: empty
- render mode: `text`
- label mode: `value`

Result:

A text badge showing:

`CC BY 4.0`

### B. Specific rule with fixed tooltip

Shared note:

    MEDIA LICENCE: CC BY 4.0

Rule:

- key: `MEDIA LICENCE`
- match type: `exact`
- match value: `CC BY 4.0`
- render mode: `icon-text`
- tooltip mode: `fixed`
- title: `Creative Commons Attribution 4.0`

Result:

A badge for `CC BY 4.0` with a fixed tooltip.

### C. Icon-only rule

Shared note:

    MEDIA STATUS: verified

Rule:

- key: `MEDIA STATUS`
- match type: `exact`
- match value: `verified`
- render mode: `icon`
- icon type: `class`
- icon value: `bi bi-check-circle`
- label mode: `none`

Result:

Only the icon is shown.

### D. SVG URL icon

Shared note:

    MEDIA RIGHTS: Public Domain

Rule:

- key: `MEDIA RIGHTS`
- match type: `exact`
- match value: `Public Domain`
- render mode: `icon-text`
- icon type: `url`
- icon value: `https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg`
- label mode: `value`

Result:

A badge with an SVG icon plus text.

### E. Context-limited rule

Shared note:

    MEDIA STATUS: verified

Rule:

- key: `MEDIA STATUS`
- match type: `exact`
- match value: `verified`
- render mode: `text`
- page contexts: `media-page`

Result:

The badge is shown on the media detail page, but not on media list pages, linked media tables, or album views.

---

## 13. Best practices

Recommended guidelines:

- use short, stable note keys such as `MEDIA LICENCE`, `MEDIA RIGHTS`, or `MEDIA STATUS`
- prefer generic rules unless a specific value needs special styling
- keep rule sets focused and easy to understand
- use CSS-class icons or image / SVG URLs when portability matters
- use page-context visibility only where output really needs to differ between views
- start with `all` unless a rule truly belongs to only one or a few contexts

---

## 14. Related documents

For more technical details, see:

- `docs/rule-model.md`
- `docs/architecture.md`
