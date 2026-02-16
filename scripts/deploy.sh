#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

if [ ! -f .env ]; then
  echo "Missing .env. Copy .env.example to .env and set SERVER_NAME + Mercure keys." >&2
  exit 1
fi

echo "Pulling latest code..."
if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  git pull --ff-only origin main
fi

echo "Building + starting containers..."
docker compose -f compose.yaml -f compose.prod.yaml up -d --build

echo "Deploy complete."
