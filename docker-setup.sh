#!/bin/bash
# Docker setup script for ShellRace CTF Challenge

echo "===== ShellRace CTF Challenge Docker Setup ====="

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Installing Docker..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
else
    echo "Docker is already installed."
fi

# Check if Docker Compose is installed
if ! command -v docker-compose &> /dev/null; then
    echo "Docker Compose is not installed. Installing Docker Compose..."
    curl -L "https://github.com/docker/compose/releases/download/v2.18.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
else
    echo "Docker Compose is already installed."
fi

# Make sure uploads directory exists
echo "Creating uploads directory..."
mkdir -p uploads
chmod 777 uploads

# Ensure admin-bot logs directory exists
echo "Creating admin-bot logs directory..."
mkdir -p admin-bot/logs
chmod 777 admin-bot/logs

# Start the Docker containers
echo "Starting Docker containers..."
docker-compose up -d

# Check if containers are running
if [ $? -eq 0 ]; then
    echo "===== Setup Complete ====="
    echo "ShellRace CTF Challenge is now running."
    echo "Access the challenge at: http://localhost/"
    echo ""
    echo "Credentials:"
    echo "User: user / password123"
    echo "Admin: admin / adminpass"
    echo ""
    echo "To view logs: docker-compose logs"
    echo "To stop: docker-compose down"
else
    echo "===== Setup Failed ====="
    echo "There was an error starting the Docker containers."
    echo "Please check the error messages above."
fi 