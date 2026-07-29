#!/usr/bin/env bash
set -euo pipefail

# ============================================================
# CADDFE Training Services — Deployment Script
# Usage: bash deploy.sh [environment]
#   environment: "docker" (default) | "prod"
# ============================================================

ENV="${1:-docker}"
COMPOSE_FILE="docker-compose.yml"

if [ "$ENV" = "prod" ]; then
  COMPOSE_FILE="docker-compose.prod.yml"
fi

echo "==> Deploying to environment: $ENV"
echo "==> Using compose file: $COMPOSE_FILE"

# 1. Pull latest code
if [ -d .git ]; then
  echo "==> Pulling latest code..."
  git pull origin main
fi

# 2. Build and start containers
echo "==> Building and starting containers..."
docker compose -f "$COMPOSE_FILE" build --pull
docker compose -f "$COMPOSE_FILE" up -d

# 3. Wait for services
echo "==> Waiting for services..."
sleep 5

# 4. Run database migrations (if any)
# php docker/scripts/migrate.php

# 5. Health check
echo "==> Running health check..."
if [ "$ENV" = "docker" ]; then
  curl -sf http://localhost:8084/health.php && echo " OK"
elif [ "$ENV" = "prod" ]; then
  curl -sf http://localhost:8084/health.php && echo " OK"
fi

echo "==> Deployment complete."
