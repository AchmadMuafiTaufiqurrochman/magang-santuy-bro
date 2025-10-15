#!/bin/bash

echo "Setting up completion photo feature..."

# Run migration
echo "Running migration..."
php artisan migrate

# Create storage link if not exists
echo "Creating storage link..."
php artisan storage:link

# Create completion-photos directory
echo "Creating completion-photos directory..."
mkdir -p storage/app/public/completion-photos

# Set permissions
echo "Setting permissions..."
chmod 755 storage/app/public/completion-photos

echo "✅ Setup complete! You can now:"
echo "1. Technician can complete orders with photo evidence"
echo "2. Customer can view completed orders with photos"
echo "3. Photos are stored in storage/app/public/completion-photos"