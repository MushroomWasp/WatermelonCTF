#!/bin/bash
# Installation script for the ShellRace Admin Bot

# Display banner
echo "============================================"
echo "  ShellRace Admin Bot Installation Script"
echo "============================================"

# Check for Node.js
if ! command -v node &> /dev/null; then
    echo "Node.js is not installed. Please install Node.js v14 or later."
    exit 1
fi

# Check Node.js version
NODE_VERSION=$(node -v | cut -d 'v' -f 2 | cut -d '.' -f 1)
if [ "$NODE_VERSION" -lt 14 ]; then
    echo "Node.js version is too old. Please upgrade to v14 or later."
    exit 1
fi

echo "Node.js version: $(node -v)"

# Check for npm
if ! command -v npm &> /dev/null; then
    echo "npm is not installed. Please install npm."
    exit 1
fi

echo "npm version: $(npm -v)"

# Install dependencies
echo "Installing dependencies..."
npm install

# Create logs directory
echo "Creating logs directory..."
mkdir -p logs
chmod 777 logs

# Prompt for configuration
echo "============================================"
echo "  Configuration"
echo "============================================"

# Get the current configuration values for baseUrl
CURRENT_BASE_URL=$(grep -o "baseUrl: \"[^\"]*\"" src/config.js | cut -d '"' -f 2)

read -p "Enter the base URL of your ShellRace application [$CURRENT_BASE_URL]: " BASE_URL
BASE_URL=${BASE_URL:-$CURRENT_BASE_URL}

# Update config.js with new values
echo "Updating configuration..."
sed -i "s|baseUrl: \"[^\"]*\"|baseUrl: \"$BASE_URL\"|g" src/config.js

echo "Configuration updated."

# Ask about cron job
echo "============================================"
echo "  Cron Job Setup"
echo "============================================"
read -p "Would you like to set up a cron job to run the admin bot every minute? (y/n): " SETUP_CRON
if [[ "$SETUP_CRON" =~ ^[Yy]$ ]]; then
    echo "Setting up cron job..."
    npm run cron
    echo "Cron job set up successfully."
else
    echo "Skipping cron job setup."
    echo "You can manually run the admin bot with 'npm start'."
fi

echo "============================================"
echo "  Testing the Admin Bot"
echo "============================================"
read -p "Would you like to test run the admin bot now? (y/n): " RUN_TEST
if [[ "$RUN_TEST" =~ ^[Yy]$ ]]; then
    echo "Running admin bot..."
    node src/index.js
    echo "Test run completed."
else
    echo "Skipping test run."
fi

echo "============================================"
echo "  Installation Complete"
echo "============================================"
echo "The ShellRace Admin Bot has been installed successfully."
echo "You can run it manually with 'npm start' or it will run via cron if you set that up."
echo "For more information, see the README.md file." 