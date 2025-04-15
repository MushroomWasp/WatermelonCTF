FROM node:18-slim

# Install dependencies for Puppeteer
RUN apt-get update && apt-get install -y \
    ca-certificates \
    fonts-liberation \
    libappindicator3-1 \
    libasound2 \
    libatk-bridge2.0-0 \
    libatk1.0-0 \
    libc6 \
    libcairo2 \
    libcups2 \
    libdbus-1-3 \
    libexpat1 \
    libfontconfig1 \
    libgbm1 \
    libgcc1 \
    libglib2.0-0 \
    libgtk-3-0 \
    libnspr4 \
    libnss3 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libstdc++6 \
    libx11-6 \
    libx11-xcb1 \
    libxcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxext6 \
    libxfixes3 \
    libxi6 \
    libxrandr2 \
    libxrender1 \
    libxss1 \
    libxtst6 \
    lsb-release \
    wget \
    xdg-utils \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Create app directory
WORKDIR /usr/src/app

# Install app dependencies
COPY package*.json ./
RUN npm install

# Bundle app source
COPY . .

# Create log file and logs directory
RUN mkdir -p logs
RUN touch /var/log/cron.log
RUN chmod 777 /var/log/cron.log

# Find where node is installed and create symlink if needed
RUN which node && \
    echo "Node is at: $(which node)" && \
    if [ ! -f "/usr/local/bin/node" ]; then \
      ln -s $(which node) /usr/local/bin/node; \
    fi

# Add crontab file
COPY admin-bot-cron /etc/cron.d/admin-bot-cron
RUN chmod 0644 /etc/cron.d/admin-bot-cron
RUN crontab /etc/cron.d/admin-bot-cron

# Configure PATH for cron
RUN echo "PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin" > /etc/environment

# Test if Node.js is working
RUN node --version

# Default command to run
CMD ["bash", "-c", "service cron start && node src/index.js && tail -f /var/log/cron.log"] 