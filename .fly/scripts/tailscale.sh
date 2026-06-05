#!/usr/bin/env bash
#
# Copyright (c) 2026 Cloudmanic Labs, LLC. All rights reserved.
# Date: 2026-06-05
#
# Start the Tailscale daemon and join our tailnet. This gives the app a
# private network path to the print server, so the SSH/SCP print jobs no
# longer need to traverse the public internet.
#

# Create persistent Tailscale state directory on the data volume so the
# node identity survives deploys and machine suspends.
mkdir -p /data/tailscale

# Start the Tailscale daemon with persistent state
tailscaled --state=/data/tailscale/tailscaled.state --socket=/var/run/tailscale/tailscaled.sock &

# Wait for the daemon to be ready
sleep 2

# Connect to the tailnet using the auth key. We pass --accept-dns=false so
# Tailscale does not take over /etc/resolv.conf — Fly's resolver keeps
# handling S3/Stripe/SES lookups. Use the print server's Tailscale IP
# (100.x.y.z) in PRINT_SERVER_HOST rather than a MagicDNS name.
if [ -n "$TAILSCALE_AUTHKEY" ]; then
    tailscale up --authkey="$TAILSCALE_AUTHKEY" --hostname="rental-notice" --accept-dns=false
    echo "Tailscale connected successfully"
else
    echo "Warning: TAILSCALE_AUTHKEY not set, skipping Tailscale setup"
fi
