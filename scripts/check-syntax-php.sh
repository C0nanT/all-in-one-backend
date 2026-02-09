#!/usr/bin/env bash
set -e
for file in "$@"; do
    php -l "$file" >/dev/null
done
