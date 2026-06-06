#!/usr/bin/env bash
#
# Copyright (c) 2026 Cloudmanic Labs, LLC. All rights reserved.
# Date: 2026-06-05
#
# Write the print server SSH private key from the PRINT_SERVER_SSH_KEY_B64
# Fly secret to disk for both root and www-data (the queue worker user).
# Keeping the key in a runtime secret means it never lives in the git repo,
# the GitHub Actions workflow, or the Docker image layers.
#

if [ -z "$PRINT_SERVER_SSH_KEY_B64" ]; then
    echo "Warning: PRINT_SERVER_SSH_KEY_B64 not set, skipping SSH key setup"
    exit 0
fi

# Install the key for both root (manual debugging) and www-data (queue worker)
for dir in /root/.ssh /var/www/.ssh; do
    mkdir -p "$dir"
    echo "$PRINT_SERVER_SSH_KEY_B64" | base64 -d > "$dir/id_ed25519"
    chmod 700 "$dir"
    chmod 600 "$dir/id_ed25519"
done

chown -R www-data:www-data /var/www/.ssh

echo "Print server SSH key installed from secret"
