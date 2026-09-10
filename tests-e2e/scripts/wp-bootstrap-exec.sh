#!/usr/bin/env bash
# Executes a PHP file inside this site's real WordPress context (correct DB socket,
# correct PHP version) using Local's bundled PHP 8.0.30 runtime. Used by test setup
# scripts to seed/inspect data via WP's own APIs instead of raw SQL.
set -euo pipefail

SITE_ID="0t-gcrPDm"
PHP_BIN="/home/shivam/.config/Local/lightning-services/php-8.0.30+0/bin/linux/bin/php"
PHP_INI="/home/shivam/.config/Local/run/${SITE_ID}/conf/php/php.ini"
LIBDIR="/home/shivam/.config/Local/lightning-services/php-8.0.30+0/bin/linux/shared-libs"

if [ $# -lt 1 ]; then
  echo "Usage: $0 <path-to-php-file> [args...]" >&2
  exit 1
fi

PHP_FILE="$1"
shift

LD_LIBRARY_PATH="$LIBDIR" "$PHP_BIN" -c "$PHP_INI" "$PHP_FILE" "$@"
