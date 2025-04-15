#!/bin/bash
# Setup script for ShellRace CTF Challenge

echo "Setting up ShellRace CTF Challenge..."

# Check if running as root
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 
   exit 1
fi

# Ensure required packages are installed
echo "Checking required packages..."
apt-get update
apt-get install -y apache2 php libapache2-mod-php php-curl

# Set up the web directory
WEBDIR="/var/www/html/shellrace"
echo "Creating web directory at $WEBDIR..."
mkdir -p $WEBDIR
mkdir -p $WEBDIR/uploads

# Copy all files to the web directory
echo "Copying challenge files..."
cp -r *.php $WEBDIR/
cp -r README.md $WEBDIR/
chmod 777 $WEBDIR/uploads

# Create the flag
FLAG="flag{sh3ll_r4c3_c0nd1t10n_pwn3d}"
FLAGFILE="/flag_0xf8a2b3c4.txt"
echo "Creating flag file at $FLAGFILE..."
echo $FLAG > $FLAGFILE
chmod 644 $FLAGFILE

# Set up cron job for the admin bot
echo "Setting up admin bot cron job..."
CRONFILE="/etc/cron.d/shellrace_admin"
echo "*/1 * * * * www-data php $WEBDIR/admin_bot.php" > $CRONFILE
chmod 644 $CRONFILE

# Update the admin bot URL
echo "Updating admin bot configuration..."
sed -i "s|http://localhost/|http://localhost/shellrace/|g" $WEBDIR/admin_bot.php

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data $WEBDIR
chmod -R 755 $WEBDIR

echo "ShellRace CTF Challenge has been set up!"
echo "Access the challenge at: http://your-server-ip/shellrace/"
echo ""
echo "Credentials:"
echo "User: user / password123"
echo "Admin: admin / adminpass"
echo ""
echo "The flag is: $FLAG" 