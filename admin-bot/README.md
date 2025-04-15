# ShellRace Admin Bot

This is an automated headless browser bot for the ShellRace CTF challenge. It simulates an admin user browsing the application, which is essential for triggering the XSS → CSRF → Shell Upload exploit chain.

## Features

- Automatically logs in as the admin user
- Visits the dashboard page to view comments (potentially containing XSS payloads)
- Visits the admin section, enabling CSRF attacks to upload malicious files
- Configurable timing to adjust how long the bot spends on each page
- Comprehensive logging system

## Prerequisites

- Node.js (v14 or later)
- npm (v6 or later)

## Installation

1. Install dependencies:
   ```
   npm install
   ```

## Configuration

Edit the `src/config.js` file to adjust the following settings:

- `baseUrl`: The base URL of the ShellRace application
- `credentials`: Admin username and password
- `timing`: How long to spend on each page
- `logging`: Log level and whether logging is enabled

## Usage

### Running Manually

Run the bot once:
```
npm start
```

### Setting Up as a Cron Job

To have the admin bot run periodically (every minute):
```
npm run cron
```

This will install a cron job that runs the bot every minute, writing logs to `admin-bot.log`.

### Development Mode

For development/testing with auto-restart:
```
npm run dev
```

## Security Note

This bot is designed to be vulnerable to XSS and CSRF attacks as part of a CTF challenge. Do not use this code in a production environment without significant security enhancements.

## Integration with ShellRace CTF

This admin bot is a crucial component of the ShellRace CTF challenge, enabling the following attack chain:

1. Attacker injects JavaScript via XSS vulnerability in comments
2. Admin bot visits the page and executes the JavaScript
3. JavaScript performs CSRF to upload a malicious PHP shell
4. Shell is available for ~10 seconds (race condition)
5. Attacker must quickly access the shell to execute commands