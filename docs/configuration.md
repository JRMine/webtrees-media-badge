# Configuration

This document explains how to configure the **Media Badge** module in webtrees.

The module reads tagged values from media-related `NOTE` facts and turns them into visual badges shown next to the media title. Configuration happens on two levels:

1. Global note keys
2. Badge rules

Global note keys define what to read.  
Badge rules define how to display it.

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

> **Planned:** This section describes the intended configuration model for fine-grained badge visibility by page context. It documents the planned direction for a next development step.

Badges should not only be controlled by note key and value, but also by where the media object is shown.

Examples:

- `MEDIA LICENCE` on the media detail page, media list, and album/gallery view
- `MEDIA STATUS` only on the media detail page
- `MEDIA RIGHTS` on the media detail page and on linked media shown on person or family pages

This allows badge output to be configured more precisely without duplicating the underlying rule logic for label, tooltip, icon, or styling.

### Basic idea

Page-context visibility should be configured **per badge rule**.

Each rule can define the page contexts in which it is allowed to render.

The existing badge resolution flow stays the same in principle:

1. read tagged NOTE values from media objects and linked Shared Notes
2. find matching badge rules
3. check visibility for the current page context
4. only render badges that are allowed in that context

Views themselves should not contain custom visibility logic.  
Instead, each view should pass its current page context into the central badge resolution.

### Configuration model

A badge rule is expected to gain an additional field called `page_contexts`.

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

The following contexts are planned for the first implementation stage:

- `all`  
  The rule is active on all supported page types

- `media-page`  
  Media detail page

- `media-list`  
  Media list page

- `linked-media-table`  
  Linked media on person, family, or other record pages where the media table is used

- `album-tab`  
  Album, gallery, or thumbnail view

- `media-tab`  
  Multimedia / media tab on person or family pages

- `random-media-slide-show`  
  Random media / slide-show context, for example on the home page

More contexts can be added later if additional media-related entry points are supported.

### Backward compatibility and defaults

Existing configurations should continue to work without manual migration.

For that reason, the following behavior is recommended:

- if `page_contexts` is missing from a saved rule, it should be treated as `["all"]`
- an empty value should also be treated as `["all"]`
- invalid or unknown context values should be ignored during normalization
- if `all` is present, additional individual context values have no further effect

This keeps existing rules fully functional and preserves current output behavior by default.

### Recommended admin UI

Page-context visibility should be configured in the badge rule editor, not in the global NOTE-key configuration.

Recommended UI approach:

- a checkbox group or multi-select field
- human-readable labels instead of only technical context names
- default selection: **All pages**

Possible labels:

- All pages
- Media detail page
- Media list
- Linked media on record pages
- Album / gallery / thumbnail view
- Multimedia tab
- Random media slideshow

The admin overview should also show where a rule is active.

### Render logic

Page-context filtering should be part of the central badge resolution.

Recommended evaluation order:

1. extract NOTE values for a media object
2. collect candidate rules for the relevant key
3. keep only rules that:
   - are enabled
   - match the value
   - are visible in the current page context
4. choose the best matching visible rule
5. build the badge data
6. render the badge

Page-context visibility should be checked **before** HTML output is generated.

A badge that is not allowed in the current context must not appear:

- visibly in the output
- in a tooltip
- as hidden or empty badge markup

### Internal implementation direction

A central context parameter is recommended, for example:

```php
resolveBadgesForMedia($record, 'media-page')
```

Views can then pass their own context, for example:

- `media-page.phtml` → `media-page`
- `lists/media-table.phtml` → `linked-media-table`
- `modules/media-list/page.phtml` → `media-list`
- `modules/lightbox/tab.phtml` → `album-tab`
- `modules/media/tab.phtml` → `media-tab`
- `modules/random_media/slide-show.phtml` → `random-media-slide-show`

This keeps the visibility check centralized and avoids spreading logic across multiple templates.

### Examples

#### Example A: licence badge visible everywhere

Key:

`MEDIA LICENCE`

Contexts:

`all`

#### Example B: status badge only on the media detail page

Key:

`MEDIA STATUS`

Contexts:

`media-page`

#### Example C: rights badge only in selected views

Key:

`MEDIA RIGHTS`

Contexts:

`media-page`, `linked-media-table`

### Scope boundaries

This feature controls **where** a badge may appear.

It does **not** replace a future user- or access-based visibility system.

The intended separation is:

- **Page-context visibility**  
  On which page types may the badge be shown?

- **Access-level visibility**  
  For which users or user groups may the badge be shown?

Both can later be enforced in the same central resolution pipeline, but they should remain separate configuration concerns.

---

## 10. Default behavior

If no saved badge rules exist, the module uses a built-in default rule set.

The default rules are intended as a safe starting point and cover common media licence scenarios.

They currently include:

- a generic fallback rule for the primary note key
- a specific rule for `CC BY 4.0`
- a specific rule for `CC BY-SA 4.0`
- a specific rule for `Public Domain`
- a specific rule for values containing `private`

These defaults can be edited or replaced later through the admin UI.

---

## 11. Example configurations

### Example A: generic licence display

Global key:

`MEDIA LICENCE`

Shared note:

    MEDIA LICENCE: CC BY 4.0

Rule:

- key: `MEDIA LICENCE`
- match type: empty
- render mode: `text`
- label mode: `value`

Result:

- badge text is `CC BY 4.0`

### Example B: specific rule with fixed tooltip

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

- the badge uses the specific display rule for this licence
- the tooltip is a fixed explanatory label

### Example C: icon-only rule

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

- only the icon is shown

### Example D: SVG URL icon

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

- a graphic icon is shown together with the text

---

## 12. Current scope

At the current stage, badge rendering is focused on media-related page output, especially the media title area.

The current implementation already extends badge output across multiple media-related contexts, including media detail pages, media list output, linked media tables, album views, multimedia tabs, and random media slide-show output.

Future versions may extend this further with more granular visibility logic, for example:

- per page context
- per user group or access level
- theme-specific compatibility improvements where view overrides conflict

---

## 13. Best practices

Use short and stable note keys, for example:

    MEDIA LICENCE
    MEDIA RIGHTS
    MEDIA STATUS

Prefer generic rules where possible, and add specific rules only when you need special styling or special tooltips.

For portability and database safety, CSS class icons or URL-based icons are usually better than decorative Unicode symbols.

Keep rule sets small and intentional at first.  
A few well-structured rules are easier to maintain than many overlapping ones.

If page-context visibility is introduced, start with `all` and only restrict rules where there is a real need for context-specific output.

---

## 14. Related documents

For the internal structure of rules and how the matching logic works, see:

- `docs/rule-model.md`
- `docs/architecture.md`
