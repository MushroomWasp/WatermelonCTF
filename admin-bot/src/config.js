/**
 * Configuration for the admin bot
 * This bot periodically visits the dashboard page to view user comments (potential XSS)
 * and other pages to simulate admin activity
 */
const config = {
  // Base URL of the application
  baseUrl: "http://localhost",
  
  // URL endpoints to visit
  pages: {
    login: "/index.php",
    dashboard: "/dashboard.php",
    admin: "/admin.php"
  },
  
  // Admin credentials
  credentials: {
    username: "admin",
    password: "adminpass"
  },
  
  // Browser settings
  browser: {
    headless: "new", // Use new headless mode for better performance
    args: [
      "--no-sandbox",
      "--disable-setuid-sandbox",
      "--disable-dev-shm-usage",
      "--disable-accelerated-2d-canvas",
      "--disable-gpu"
    ],
    defaultViewport: {
      width: 1280,
      height: 800
    }
  },
  
  // Timing settings (in milliseconds)
  timing: {
    pageLoadTimeout: 10000,
    viewCommentsDuration: 5000,
    adminSessionDuration: 30000
  },
  
  // Logging settings
  logging: {
    enabled: true,
    level: "info" // error, warn, info, debug
  }
};

module.exports = config;
