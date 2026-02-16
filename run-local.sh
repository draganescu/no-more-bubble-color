#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

echo "Starting Chat for All on http://localhost:8087/"
docker compose up --build
