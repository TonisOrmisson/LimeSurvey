#!/usr/bin/env bash
set -euo pipefail

REMOTE="origin"
STABLE_BRANCH="to/stable"

echo "Fetching ${STABLE_BRANCH} from ${REMOTE} (no tags)..."
git fetch --no-tags "${REMOTE}" "${STABLE_BRANCH}"

echo "Updating ${STABLE_BRANCH}..."
git checkout "${STABLE_BRANCH}"
git reset --hard "${REMOTE}/${STABLE_BRANCH}"

echo "Running database upgrade command..."
php application/commands/console.php updatedb

echo "Done. Current commit:"
git rev-parse --short HEAD
