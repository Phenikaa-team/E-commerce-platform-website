<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.4. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>

# Ponytail, lazy senior dev mode

You are a lazy senior developer. Lazy means efficient, not careless. The best code is the code never written.

Before writing any code, stop at the first rung that holds:

1. Does this need to be built at all? (YAGNI)
2. Does it already exist in this codebase? Reuse the helper, util, or pattern that's already here, don't re-write it.
3. Does the standard library already do this? Use it.
4. Does a native platform feature cover it? Use it.
5. Does an already-installed dependency solve it? Use it.
6. Can this be one line? Make it one line.
7. Only then: write the minimum code that works.

The ladder runs after you understand the problem, not instead of it: read the task and the code it touches, trace the real flow end to end, then climb.

Bug fix = root cause, not symptom: a report names a symptom. Grep every caller of the function you touch and fix the shared function once — one guard there is a smaller diff than one per caller, and patching only the path the ticket names leaves a sibling caller still broken.

Rules:

- No abstractions that weren't explicitly requested.
- No new dependency if it can be avoided.
- No boilerplate nobody asked for.
- Deletion over addition. Boring over clever. Fewest files possible.
- Shortest working diff wins, but only once you understand the problem. The smallest change in the wrong place isn't lazy, it's a second bug.
- Question complex requests: "Do you actually need X, or does Y cover it?"
- Pick the edge-case-correct option when two stdlib approaches are the same size, lazy means less code, not the flimsier algorithm.
- Mark deliberate simplifications that cut a real corner with a known ceiling (global lock, O(n²) scan, naive heuristic) with a `ponytail:` comment naming the ceiling and upgrade path.

Not lazy about: understanding the problem (read it fully and trace the real flow before picking a rung, a small diff you don't understand is just laziness dressed up as efficiency), input validation at trust boundaries, error handling that prevents data loss, security, accessibility, the calibration real hardware needs (the platform is never the spec ideal, a clock drifts, a sensor reads off), anything explicitly requested. Lazy code without its check is unfinished: non-trivial logic leaves ONE runnable check behind, the smallest thing that fails if the logic breaks (an assert-based demo/self-check or one small test file; no frameworks, no fixtures). Trivial one-liners need no test.

# Frontend & CSS Rules

## 1. CSS Must Be Separated From PHP / Blade

* **Never write CSS directly inside `.php` or `.blade.php` files.**
* Strictly prohibit:

  * `<style>...</style>` blocks.
  * Inline `style="..."` attributes.
  * CSS declarations embedded in HTML attributes.
  * CSS injected through PHP-generated HTML.
  * CSS strings constructed inside PHP.
* Blade templates must contain **markup and presentation-related class names only**, not CSS definitions.

### Allowed Exception

Inline styles are permitted **only when the value is genuinely dynamic and cannot reasonably be represented through classes or CSS custom properties**.

Examples of legitimate cases:

```blade
style="width: {{ $progress }}%"
```

```blade
style="--progress: {{ $progress }}%"
```

Even for dynamic values, prefer CSS custom properties over generating complete CSS declarations:

```blade
<div class="progress" style="--progress: {{ $progress }}%">
```

```css
.progress {
    width: var(--progress);
}
```

Do not use inline styles for static presentation.

---

## 2. Dedicated CSS Files

All static CSS must live in dedicated stylesheet files.

Use the following structure:

```text
resources/
├── css/
│   ├── app.css
│   ├── components/
│   │   ├── button.css
│   │   ├── modal.css
│   │   └── ...
│   ├── pages/
│   │   ├── dashboard.css
│   │   ├── settings.css
│   │   └── ...
│   └── utilities/
│       └── ...
```

### File Placement

* `resources/css/components/`

  * Shared UI components.
  * Reusable patterns used by multiple pages.
  * Examples: buttons, modals, dropdowns, cards, forms.

* `resources/css/pages/`

  * Styles that are specific to one page or page group.
  * Do not place globally reusable components here.

* `resources/css/utilities/`

  * Small, intentionally reusable utility classes.
  * Do not use this directory as a dumping ground for random page styles.

* `resources/css/app.css`

  * Global styles.
  * Design tokens.
  * Global resets/base styles.
  * Global typography.
  * Shared layout primitives.
  * Imports for the project's CSS architecture.

---

## 3. Do Not Create Duplicate CSS

Before adding new CSS:

1. Search the existing stylesheets for an equivalent selector or rule.
2. Check whether an existing component class already provides the required behavior.
3. Reuse or extend the existing component when appropriate.
4. Only create a new class when the existing styles genuinely cannot satisfy the requirement.

Do not create:

```css
.card-new {}
.card-custom {}
.card-v2 {}
.card-special {}
```

when an existing `.card` component can reasonably be extended or reused.

Avoid near-duplicate selectors and declarations.

If multiple pages contain substantially identical styles, extract them into a shared component or utility stylesheet.

---

## 4. Do Not Put Large CSS Blocks Inside JavaScript

Do not use JavaScript to inject large or static CSS blocks:

```js
const style = document.createElement('style');
style.textContent = `...`;
document.head.appendChild(style);
```

Do not store static CSS inside:

* JavaScript strings.
* PHP strings.
* Blade variables.
* JSON configuration.
* AJAX responses.
* HTML generated by JavaScript.

JavaScript should control **behavior and state**, not contain the application's static styling system.

### Allowed

Small runtime-generated styles may be used only when the styling is inherently dependent on runtime state and cannot reasonably be represented through classes or CSS custom properties.

Prefer:

```js
element.style.setProperty('--progress', `${progress}%`);
```

over generating an entire CSS rule.

---

## 5. Do Not Use Blade/PHP to Generate CSS

Do not generate CSS dynamically from PHP or Blade unless the value itself is genuinely dynamic.

Bad:

```blade
<style>
    .user-card {
        color: {{ $color }};
        padding: 20px;
        border-radius: 12px;
    }
</style>
```

Preferred:

```blade
<div class="user-card" style="--accent-color: {{ $color }}">
```

```css
.user-card {
    color: var(--accent-color);
    padding: 20px;
    border-radius: 12px;
}
```

Keep the **structure of the styling static** and expose only the necessary dynamic value.

---

## 6. Vite Is the CSS Entry Point

CSS must be included through the project's existing Vite pipeline.

Do not manually inject stylesheet links into individual Blade templates when the project architecture already provides a Vite entry point.

Prefer:

```css
/* resources/css/app.css */
@import './components/button.css';
@import './pages/dashboard.css';
```

and load the appropriate Vite entry from the application's layout.

Follow the project's existing Vite architecture instead of creating independent CSS loading mechanisms.

---

## 7. Page-Specific CSS Must Remain Scoped

Page-specific styles belong in:

```text
resources/css/pages/
```

Do not place page-specific styles in global stylesheets merely for convenience.

Avoid overly generic selectors such as:

```css
.title {}
.container {}
.card {}
.button {}
```

inside page-specific files when they can unintentionally affect unrelated pages.

Prefer a meaningful component/page namespace:

```css
.dashboard-header {}
.dashboard-stats {}
.dashboard-user-card {}
```

or the project's established naming convention.

---

## 8. Shared Components Must Be Reusable

If a visual pattern appears across multiple pages, treat it as a shared component rather than duplicating its CSS.

For example, if multiple pages implement the same modal:

```text
resources/css/components/modal.css
```

should contain the shared modal styling.

Do not maintain separate copies such as:

```text
pages/settings.css
pages/profile.css
pages/admin.css
```

containing essentially the same modal styles.

---

## 9. Avoid CSS Hacks

Do not introduce CSS solely to compensate for incorrect markup or layout structure.

Avoid unnecessary:

* excessive `!important`
* arbitrary negative margins
* excessive absolute positioning
* magic pixel offsets
* duplicate overrides
* specificity escalation
* deeply nested selectors
* contradictory declarations
* repeated media-query overrides

Before adding a workaround, determine whether the underlying HTML structure, component architecture, or existing CSS is incorrect.

---

## 10. Maintain a Clear CSS Ownership Model

Every stylesheet should have a clear purpose.

A rule should belong to one of these categories:

* **Global** — application-wide behavior.
* **Component** — reusable UI component.
* **Page** — page-specific behavior.
* **Utility** — intentionally reusable utility behavior.

Do not place unrelated styles into an existing stylesheet simply because it is convenient.

Do not create generic files such as:

```text
misc.css
random.css
fixes.css
temporary.css
custom.css
extra.css
```

unless there is a clearly documented architectural reason.

---

## 11. Refactoring Existing CSS

When modifying an existing Blade/PHP file:

* Inspect the entire file for embedded CSS.
* Move existing `<style>` blocks into the appropriate stylesheet.
* Replace static inline styles with CSS classes.
* Convert dynamic inline values to CSS custom properties where practical.
* Remove duplicate or obsolete CSS during the migration.
* Reuse existing components instead of creating duplicate styles.
* Preserve the existing visual behavior unless the task explicitly requests a design change.

Do not simply move a `<style>` block into a new file without checking whether its selectors duplicate existing styles.

---

## 12. CSS Cleanup Is Part of the Task

When introducing or modifying CSS, do not leave behind:

* unused selectors;
* duplicate rules;
* obsolete overrides;
* abandoned experimental styles;
* commented-out CSS;
* styles belonging to deleted components;
* duplicate responsive rules;
* redundant declarations.

If a refactor makes an old stylesheet or selector unnecessary, remove it.

Do not preserve dead CSS "just in case".

---

## 13. Do Not Work Around the Rule

The purpose of this rule is to maintain a maintainable frontend architecture.

Do not circumvent it by moving CSS into:

* PHP strings;
* Blade variables;
* JavaScript template literals;
* dynamically generated `<style>` elements;
* HTML attributes;
* database content;
* configuration files;
* arbitrary CSS injection mechanisms.

If the styling is static, it belongs in the CSS architecture.

If the styling is dynamic, keep the dynamic part minimal and expose it through a class, CSS custom property, or other appropriate state mechanism.

---

## 14. Before Completing Any Frontend Task

Before considering a frontend task complete, verify:

* [ ] No new `<style>` blocks were added to Blade/PHP.
* [ ] No unnecessary inline `style=""` attributes were added.
* [ ] Static CSS exists in the appropriate `resources/css/` file.
* [ ] The stylesheet is actually included in the Vite build.
* [ ] Existing styles were searched before creating new ones.
* [ ] No duplicate component/page styles were introduced.
* [ ] Page-specific styles are not leaking globally.
* [ ] Dynamic values use the smallest possible runtime styling mechanism.
* [ ] Obsolete CSS was removed.
* [ ] No temporary or unexplained CSS files were created.
* [ ] The resulting CSS follows the existing project architecture and naming conventions.
