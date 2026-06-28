# Architecture

## Project structure

modules_v4/media-badge/
├── module.php
├── MediaBadgeModule.php
└── resources/
    ├── css/
    │   └── media-badge.css
    └── views/
        ├── media-page.phtml
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
        └── admin/
            ├── config.phtml
            ├── badges.phtml
            └── badge-edit.phtml

---

## Core responsibilities

The module architecture separates content extraction, rule resolution, visibility checks, and rendering.

### `module.php`

Entry point for loading the custom module in webtrees.

### `MediaBadgeModule.php`

Central module class.

Main responsibilities:

- register custom views
- load and normalize configuration
- read configured `NOTE_KEYS`
- extract tagged values from media-related `NOTE` facts
- resolve matching badge rules
- apply visibility logic
- compose final badge data for rendering

### `resources/css/media-badge.css`

Shared styling for badge output and admin UI elements.

### `resources/views/components/`

Reusable rendering components.

- `media-badge.phtml` renders a single badge
- `media-badge-list.phtml` renders a prepared badge list for a record and position

These components should stay as small and reusable as possible.

### `resources/views/admin/`

Admin configuration views.

- `config.phtml` manages global note keys
- `badges.phtml` shows the badge rule overview
- `badge-edit.phtml` edits a single badge rule

### `resources/views/...`

Context-specific integrations for badge output on media-related pages.

These views should pass context and render prepared badge output, but should not contain business logic for matching or visibility.

---

## Runtime flow

The badge pipeline is structured as follows:

1. extract tagged note values from media-related `NOTE` content
2. load and normalize configured badge rules
3. select matching rules for extracted values
4. apply visibility checks
5. compose final badge data
6. render badges through reusable view components

In short:

**Extraction → Normalization → Matching → Visibility → Badge composition → Rendering**

---

## Content vs presentation

The module follows a strict separation:

- **content** comes from Shared Notes or media-related `NOTE` facts
- **presentation** comes from badge rules
- **rendering** is handled by reusable views

This means:

- note content defines the raw value
- rules define how that value should look
- views only display already prepared badge data

---

## Page-context visibility

Badge visibility can be extended by page context through the rule field `page_contexts`.

Typical contexts:

- `all`
- `media-page`
- `media-list`
- `linked-media-table`
- `album-tab`
- `media-tab`
- `random-media-slide-show`

### Responsibility rule

Views provide the current page context.  
The module decides whether a rule is visible in that context.

This keeps visibility logic centralized and avoids duplicating condition checks across multiple templates.

### Recommended behavior

If a rule is not allowed in the current page context:

- no badge should be rendered
- no tooltip should be rendered
- no hidden or empty badge markup should be generated

For backward compatibility, missing `page_contexts` should be treated as:

```json
["all"]
```

---

## View-to-context mapping

The following mapping should be used consistently:

- `media-page.phtml` → `media-page`
- `lists/media-table.phtml` → `linked-media-table`
- `modules/media-list/page.phtml` → `media-list`
- `modules/lightbox/tab.phtml` → `album-tab`
- `modules/media/tab.phtml` → `media-tab`
- `modules/random_media/slide-show.phtml` → `random-media-slide-show`

This mapping connects concrete rendering entry points to the central badge visibility model.

---

## Architectural principle

The main architectural principle of the module is:

- extract note-based content once
- resolve rules centrally
- apply visibility centrally
- reuse rendering components across page contexts

This keeps the module maintainable and makes it easier to extend later with:

- page-context visibility
- user-group or access-level visibility
- additional media-related views
- theme-specific compatibility improvements

---

## Related documents

- `docs/configuration.md`
- `docs/rule-model.md`

Modules Compatibility Issues
Media Badge should prefer compatibility through small reusable rendering fragments over full template ownership wherever possible.
