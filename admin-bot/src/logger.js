/**
 * Logger module for admin bot
 * Provides consistent logging with timestamps and log levels
 */

const config = require('./config');

// Log levels
const LOG_LEVELS = {
  error: 0,
  warn: 1,
  info: 2,
  debug: 3
};

// Get numeric value for configured log level
const configuredLevel = LOG_LEVELS[config.logging.level] || LOG_LEVELS.info;

/**
 * Format timestamp for log entries
 */
function getTimestamp() {
  const now = new Date();
  return now.toISOString();
}

/**
 * Log a message if the log level is enabled
 */
function log(level, message) {
  if (!config.logging.enabled) return;
  
  const levelValue = LOG_LEVELS[level];
  if (levelValue <= configuredLevel) {
    const timestamp = getTimestamp();
    const formattedLevel = level.toUpperCase().padEnd(5);
    console.log(`${timestamp} [${formattedLevel}] ${message}`);
  }
}

// Logger interface
const logger = {
  error: (message) => log('error', message),
  warn: (message) => log('warn', message),
  info: (message) => log('info', message),
  debug: (message) => log('debug', message)
};

module.exports = logger; 