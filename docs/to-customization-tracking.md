# TO Customization Tracking

## Purpose
Track all customizations carried in the `to/stable` branch on top of upstream LimeSurvey, so upgrades do not lose local functionality.
Versioning/tagging rules are documented in `docs/to-versioning.md`.

## Release Model
- Upstream base: latest stable upstream release tag (example: `6.16.10+260223`)
- Custom branch: `to/stable`
- Custom tags: `vX.Y.Z-to.N`
  - Example: `v6.16.10-to.1`, `v6.16.10-to.2`

## Current Base
- Branch: `to/stable`
- Base upstream tag: `6.16.10+260223`
- Base commit: `571bab902e`

## Active Customizations
| ID | Area | Description | Local Commit(s) | Upstream PR/Issue | Keep Until |
|---|---|---|---|---|---|
| TO-0001 | RemoteControl API | Add `list_response_exports` discovery endpoint | `da3614f98a` | PR #4731 | Merged upstream and released |
| TO-0002 | RemoteControl API | Make `list_response_exports` global (remove survey param) | `1fb5bca3c9` | PR #4731 | Merged upstream and released |

## Upgrade Checklist (Upstream -> TO)
1. Fetch upstream and identify latest stable tag.
2. Create/update integration branch from that upstream tag.
3. Cherry-pick all `TO-*` commits still marked active.
4. Resolve conflicts and run tests.
5. Update this file:
   - Base tag/commit
   - Active customization rows
   - Local commit references
6. Tag release as `vX.Y.Z-to.N`.

## Decommission Rule
When a TO customization is included in an official upstream release used as base:
- mark its row as merged upstream
- remove it from future cherry-pick lists
- keep historical reference in this file.
