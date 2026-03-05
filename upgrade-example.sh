#!/usr/bin/env bash
set -euo pipefail

REMOTE="origin"
STABLE_BRANCH="to/stable"
TO_TAG_PATTERN="v*-to.*"

echo "Fetching ${STABLE_BRANCH} from ${REMOTE}..."
git fetch --no-tags "${REMOTE}" "${STABLE_BRANCH}"
echo "Fetching TO tags (${TO_TAG_PATTERN}) from ${REMOTE}..."
mapfile -t TO_TAGS < <(
  git ls-remote --tags --refs "${REMOTE}" "${TO_TAG_PATTERN}" \
    | awk '{print $2}' \
    | sed 's#^refs/tags/##'
)
if [[ "${#TO_TAGS[@]}" -eq 0 ]]; then
  echo "No TO tags found on ${REMOTE} with pattern '${TO_TAG_PATTERN}'"
  exit 1
fi
for TAG in "${TO_TAGS[@]}"; do
  git fetch --no-tags --force "${REMOTE}" "refs/tags/${TAG}:refs/tags/${TAG}"
done

echo "Updating ${STABLE_BRANCH}..."
git checkout "${STABLE_BRANCH}"
git reset --hard "${REMOTE}/${STABLE_BRANCH}"

LATEST_TO_TAG="$(git tag -l "${TO_TAG_PATTERN}" --sort=-v:refname | head -n 1)"
if [[ -z "${LATEST_TO_TAG}" ]]; then
  echo "No TO tags found with pattern '${TO_TAG_PATTERN}'"
  exit 1
fi

echo "Checking out latest TO tag: ${LATEST_TO_TAG}"
git checkout "${LATEST_TO_TAG}"

echo "Running database upgrade command..."
php application/commands/console.php updatedb

echo "Done. Current version tag:"
git describe --tags --exact-match

git status
