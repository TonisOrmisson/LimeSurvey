# TO Fork Versioning

## Purpose
Define one clear versioning/release model for this fork so upgrades are predictable and custom builds are always identifiable.

## Rules
1. Release branch is always `to/stable`.
2. Base each TO release on the latest upstream stable tag.
3. Tag format is always `v<upstream-version>-to.<n>`.
4. Never create plain upstream-looking release tags in this fork.
5. Version shown in UI must always contain the TO suffix (`-to.<n>`), never a plain upstream version string.

## Tag Examples
- `v6.16.10-to.1`
- `v6.16.10-to.2`
- `v6.16.11-to.1`

## Version File Update
For each TO release, update:
- `application/config/version.php`

Use:
- `$config['versionnumber'] = '<upstream-version>-to.<n>';`
- Keep `dbversionnumber` aligned with upstream base unless a DB migration is added in fork.
- Keep `assetsversionnumber` behavior unchanged unless asset-cache invalidation is needed.

## Release Flow
1. Checkout `to/stable`.
2. Rebase/refresh from latest upstream stable tag.
3. Apply/cherry-pick active TO customizations.
4. Run required checks/tests.
5. Update `docs/to-customization-tracking.md`.
6. Update `application/config/version.php` with TO suffix.
7. Create TO tag using the format above.

## Upgrade Script Usage
- `upgrade-example.sh` is a template for pulling `to/stable` and checking out the latest TO tag.
- Copy to `upgrade.sh` for server-local usage (`upgrade.sh` is gitignored).
