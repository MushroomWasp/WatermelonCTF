/**
 * Admin Bot for ShellRace CTF Challenge
 * 
 * This bot simulates an admin user browsing the application.
 * It logs in as admin, visits the dashboard to view comments (which may contain XSS),
 * and navigates to the admin page to potentially trigger CSRF attacks.
 */

const puppeteer = require("puppeteer");
const config = require("./config");
const logger = require("./logger");

/**
 * Main function to run the admin bot
 */
async function runAdminBot() {
  let browser = null;

  try {
    logger.info("Starting admin bot session");
    
    // Launch browser with configured options
    browser = await puppeteer.launch(config.browser);
    
    // Create a new page and set viewport
    const page = await browser.newPage();
    await page.setViewport(config.browser.defaultViewport);
    
    // Set user agent to a realistic browser
    await page.setUserAgent("Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36");

    // Configure request timeout
    page.setDefaultNavigationTimeout(config.timing.pageLoadTimeout);
    
    // Enable JavaScript execution (needed for XSS)
    await page.setJavaScriptEnabled(true);
    
    // Log all console messages from the page
    page.on('console', message => {
      const type = message.type().substr(0, 3).toUpperCase();
      const text = message.text();
      logger.debug(`PAGE CONSOLE [${type}]: ${text}`);
    });

    // Log in as admin
    await loginAsAdmin(page);
    
    // Visit dashboard page to view comments (potential XSS trigger)
    await visitDashboard(page);
    
    // Visit admin page
    await visitAdminPage(page);
    
    logger.info("Admin bot session completed successfully");
  } catch (error) {
    logger.error(`Admin bot error: ${error.message}`);
  } finally {
    // Always close the browser to clean up resources
    if (browser) {
      await browser.close();
      logger.info("Browser closed");
    }
  }
}

/**
 * Login as admin user
 */
async function loginAsAdmin(page) {
  const loginUrl = `${config.baseUrl}${config.pages.login}`;
  
  try {
    logger.info(`Navigating to login page: ${loginUrl}`);
    await page.goto(loginUrl, { waitUntil: "networkidle2" });
    
    // Check if already logged in by looking for dashboard redirect
    const currentUrl = page.url();
    if (currentUrl.includes(config.pages.dashboard)) {
      logger.info("Already logged in, redirected to dashboard");
      return;
    }
    
    // Fill in login form
    logger.info("Submitting login credentials");
    await page.type('input[name="username"]', config.credentials.username);
    await page.type('input[name="password"]', config.credentials.password);
    
    // Submit form and wait for navigation
    await Promise.all([
      page.click('input[type="submit"]'),
      page.waitForNavigation({ waitUntil: "networkidle2" })
    ]);
    
    // Verify successful login
    const postLoginUrl = page.url();
    if (postLoginUrl.includes(config.pages.dashboard)) {
      logger.info("Login successful");
    } else {
      throw new Error("Login failed - not redirected to dashboard");
    }
  } catch (error) {
    logger.error(`Login error: ${error.message}`);
    throw error; // Re-throw to stop execution
  }
}

/**
 * Visit dashboard to view comments (triggers XSS)
 */
async function visitDashboard(page) {
  const dashboardUrl = `${config.baseUrl}${config.pages.dashboard}`;
  
  try {
    logger.info(`Visiting dashboard: ${dashboardUrl}`);
    await page.goto(dashboardUrl, { waitUntil: "networkidle2" });
    
    // Look for comments container
    const hasCommentsSection = await page.evaluate(() => {
      return document.querySelector('.comments') !== null;
    });
    
    if (hasCommentsSection) {
      logger.info("Found comments section, viewing comments");
      
      // Take a screenshot for debugging if needed
      if (config.logging.level === "debug") {
        await page.screenshot({ path: 'dashboard-visit.png' });
      }
      
      // Wait to ensure any XSS has time to execute
      logger.info(`Waiting ${config.timing.viewCommentsDuration}ms on dashboard`);
      await new Promise(resolve => setTimeout(resolve, config.timing.viewCommentsDuration));
    } else {
      logger.warn("Could not find comments section on dashboard");
    }
  } catch (error) {
    logger.error(`Dashboard visit error: ${error.message}`);
    throw error;
  }
}

/**
 * Visit admin page (for CSRF attack vector)
 */
async function visitAdminPage(page) {
  const adminUrl = `${config.baseUrl}${config.pages.admin}`;
  
  try {
    logger.info(`Visiting admin page: ${adminUrl}`);
    await page.goto(adminUrl, { waitUntil: "networkidle2" });
    
    // Verify we're on the admin page
    const isAdminPage = await page.evaluate(() => {
      return document.body.textContent.includes('Admin Panel');
    });
    
    if (isAdminPage) {
      logger.info("Successfully accessed admin panel");
      
      // Wait on admin page (allowing time for any CSRF to occur)
      logger.info(`Waiting ${config.timing.adminSessionDuration}ms on admin page`);
      await new Promise(resolve => setTimeout(resolve, config.timing.adminSessionDuration));
    } else {
      logger.error("Failed to access admin panel or admin panel not found");
    }
  } catch (error) {
    logger.error(`Admin page visit error: ${error.message}`);
    // Don't throw error here, allow session to continue
  }
}

// Run the admin bot
if (require.main === module) {
  runAdminBot();
}

// Export for potential use in other modules
module.exports = { runAdminBot };
