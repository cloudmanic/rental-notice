#!/usr/bin/env bash

# Litestream restore script
# This script will restore the database from Litestream backup if the database doesn't exist
# or if restoration is explicitly requested

DB_PATH="/data/rental-notice.sqlite"

# Check if database exists and is not empty
if [ ! -f "$DB_PATH" ] || [ ! -s "$DB_PATH" ]; then
    echo "Database not found or empty, attempting restore from Litestream..."
    
    # Try to restore from Litestream
    if /usr/local/bin/litestream restore -if-replica-exists "$DB_PATH"; then
        echo "Database restored successfully from Litestream backup"
        chown www-data:www-data "$DB_PATH"
        chmod 644 "$DB_PATH"
    else
        echo "No backup found or restore failed, will create new database"
    fi
else
    echo "Database exists, skipping restore"
fi
