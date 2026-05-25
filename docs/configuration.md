# Configuration

This document explains how to configure the **Media Badge** module in webtrees.

The module reads tagged values from media-related `NOTE` facts and turns them into visual badges shown next to the media title. Configuration happens on two levels:

1. **Global note keys**
2. **Badge rules**

Global note keys define *what to read*.  
Badge rules define *how to display it*.

---

## 1. Global note keys

Global note keys tell the module which tagged note lines should be recognized.

Each key must be entered on its own line in the module configuration.

Example:

MEDIA LICENCE
MEDIA RIGHTS
MEDIA STATUS
With this configuration, the module will scan linked media notes for lines such as:

CopyMEDIA LICENCE: CC BY 4.0
MEDIA RIGHTS: Public Domain
MEDIA STATUS: verified
If a note line starts with one of the configured keys, the part after the colon is treated as the badge value.

Important behavior
Matching is based on the configured key followed by a colon.
Empty lines in the configuration are ignored.
Multiple keys are supported.
If no custom keys are saved, the module falls back to its default key.
Default key:

CopyMEDIA LICENCE
2. Badge rules
Badge rules define how detected values should be displayed.

A rule can:

apply to all values of a given key
apply only to specific values
control whether text, icons, or both are shown
define tooltip behavior
set ordering and position
assign CSS classes for styling
If multiple rules match the same value, the module selects the best matching rule automatically.

3. Rule matching
Each detected note value is evaluated against the configured badge rules.

A rule may either be:

generic, meaning it applies to any value of a key
specific, meaning it only applies if the value matches a condition
Supported match types:

empty / generic
exact
contains
regex
Example
A note contains:

CopyMEDIA LICENCE: CC BY 4.0
Possible rules:

generic rule for MEDIA LICENCE
exact rule for CC BY 4.0
The exact rule wins because it is more specific.

4. Render modes
The module supports several display modes.

text
Only the badge label is shown.

icon
Only the icon is shown.

icon-text
Both icon and text are shown together.

auto
The module decides automatically:

if an icon value exists, use icon + text
otherwise use text only
5. Icon types
The module supports three icon sources.

class
Use a CSS class, for example from an icon font.

Example:

Copybi bi-lock-fill
text
Use plain text as the icon value.

Example:

CopyCopyright
This can be useful for very simple text-based markers.

url
Use an external image or SVG URL.

Example:

Copyhttps://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg
This is useful for graphic icons that should not depend on a local icon font.

Notes on URL icons
External image or SVG URLs depend on the remote host being reachable and allowing direct loading.
If a URL icon does not appear, the issue may be caused by the remote server rather than the module itself.

6. Label modes
The label mode controls which text is shown inside the badge.

value
Use the detected value from the note.

Example:

CopyMEDIA LICENCE: CC BY 4.0
Badge text:

CopyCC BY 4.0
fixed
Use the fixed text stored in the rule.

Example:

CopyPrivate
none
Do not show text at all.

This is useful for icon-only badges.

7. Tooltip modes
Tooltip mode controls the hover text.

auto
Automatically generate a tooltip from key and value.

Example:

CopyMEDIA LICENCE: CC BY 4.0
fixed
Use the text stored in the rule’s title field.

Example:

CopyCreative Commons Attribution 4.0
none
Do not render a tooltip.

8. Position and ordering
Each rule can define where the badge appears relative to the media title.

Supported positions:

before-title
after-title
Each rule can also define a numeric sort_order.

Lower numbers are shown earlier.

Example:

10 appears before 20
20 appears before 100
9. Default behavior
If no saved badge rules exist, the module uses a built-in default rule set.

The default rules are intended as a safe starting point and cover common media licence scenarios.

They currently include:

a generic fallback rule for the primary note key
a specific rule for CC BY 4.0
a specific rule for CC BY-SA 4.0
a specific rule for Public Domain
a specific rule for values containing private
These defaults can be edited or replaced later through the admin UI.

10. Example configurations
Example A: generic licence display
Global key:

CopyMEDIA LICENCE
Shared note:

CopyMEDIA LICENCE: CC BY 4.0
Rule:

key: MEDIA LICENCE
match type: empty
render mode: text
label mode: value
Result:

badge text = CC BY 4.0
Example B: specific rule with fixed tooltip
Shared note:

CopyMEDIA LICENCE: CC BY 4.0
Rule:

key: MEDIA LICENCE
match type: exact
match value: CC BY 4.0
render mode: icon-text
tooltip mode: fixed
title: Creative Commons Attribution 4.0
Result:

the badge uses the specific display rule for this licence
the tooltip is a fixed explanatory label
Example C: icon-only rule
Shared note:

CopyMEDIA STATUS: verified
Rule:

key: MEDIA STATUS
match type: exact
match value: verified
render mode: icon
icon type: class
icon value: bi bi-check-circle
label mode: none
Result:

only the icon is shown
Example D: SVG URL icon
Shared note:

CopyMEDIA RIGHTS: Public Domain
Rule:

key: MEDIA RIGHTS
match type: exact
match value: Public Domain
render mode: icon-text
icon type: url
icon value: https://upload.wikimedia.org/wikipedia/commons/5/52/Cc-zero.svg
label mode: value
Result:

a graphic icon is shown together with the text
11. Current scope
At the current stage, badge rendering is focused on media-related page output, especially the media title area.

Future versions may extend this to additional contexts, such as:

media lists
search results
gallery or thumbnail views
linked media blocks on other record pages
12. Best practices
Use short and stable note keys, for example:

CopyMEDIA LICENCE
MEDIA RIGHTS
MEDIA STATUS
Prefer generic rules where possible, and add specific rules only when you need special styling or special tooltips.

For portability and database safety, CSS class icons or URL-based icons are usually better than decorative Unicode symbols.

Keep rule sets small and intentional at first.
A few well-structured rules are easier to maintain than many overlapping ones.

13. Related documents
For the internal structure of rules and how the matching logic works, see:

docs/rule-model.md
docs/architecture.md
