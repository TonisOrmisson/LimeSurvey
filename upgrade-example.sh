#!/usr/bin/env bash
set -euo pipefail

REMOTE="origin"
STABLE_BRANCH="to/stable"
TO_TAG_PATTERN="*-to.*"

echo "Fetching branch and tags from ${REMOTE}..."
git fetch "${REMOTE}" --tags
git fetch "${REMOTE}" "${STABLE_BRANCH}"

echo "Updating ${STABLE_BRANCH}..."
git checkout "${STABLE_BRANCH}"
git pull --ff-only "${REMOTE}" "${STABLE_BRANCH}"

LATEST_TO_TAG="$(git tag -l "${TO_TAG_PATTERN}" --sort=-v:refname | head -n 1)"
if [[ -z "${LATEST_TO_TAG}" ]]; then
  echo "No TO tags found with pattern '${TO_TAG_PATTERN}'"
  exit 1
fi

echo "Checking out latest TO tag: ${LATEST_TO_TAG}"
git checkout "${LATEST_TO_TAG}"

echo "Running database upgrade command..."
php application/commands/console.php updatedb

echo "Done. Current tag:"
git describe --tags --exact-match
