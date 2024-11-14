# Site Vitals for WordPress®

**Site Vitals Checker** is a comprehensive WordPress plugin designed to monitor and evaluate various aspects of your website's health. From security and performance to SEO and accessibility, this plugin provides detailed insights and actionable recommendations to ensure your site runs smoothly and effectively.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Frequently Asked Questions](#frequently-asked-questions)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Features

**Site Vitals Checker** offers a wide range of checks to help you maintain and improve your website:

### Security Checks

- **SSL Certificate Check**: Verifies if SSL is enabled on your site.
- **Plugin Update Check**: Identifies outdated plugins that need updates.
- **Theme Update Check**: Detects outdated themes requiring updates.
- **WordPress Core Update Check**: Ensures your WordPress core is up to date.
- **Login Security Check**: Checks if two-factor authentication is enabled.
- **Security Headers Check**: Validates the presence of essential security headers.
- **File Permissions Check**: Reviews file permissions for critical files.

### SEO Checks

- **SEO Meta Tags Check**: Ensures title and meta description tags are set.
- **SEO Plugin Detection**: Detects active SEO plugins installed on your site.
- **Image Alt Text Check**: Identifies images missing alt text.
- **Sitemap Check**: Verifies the existence of sitemaps for better indexing.

### Performance Checks

- **Caching Status Check**: Detects active caching plugins to improve site speed.
- **PHP Version Check**: Ensures your server is running a recommended PHP version.
- **Database Optimization Check**: Identifies transients and revisions that may require cleanup.
- **Max Upload Size Check**: Checks if the maximum upload size is sufficient.
- **Memory Limit Check**: Verifies if the PHP memory limit meets the recommended threshold.
- **Gzip Compression Check**: Confirms if Gzip compression is enabled for faster load times.

### Accessibility Checks

- **Alt Text for Images Check**: Ensures all images have descriptive alt text.
- **Color Contrast Check**: Placeholder for verifying color contrast ratios.
- **Keyboard Navigation Check**: Placeholder for ensuring keyboard accessibility.
- **ARIA Roles and Landmarks Check**: Placeholder for validating ARIA roles.
- **Form Labels Check**: Identifies forms missing labels for accessibility.
- **Heading Structure Check**: Placeholder for verifying logical heading structures.
- **Link Descriptions Check**: Detects ambiguous link texts that need improvement.

### Content Management

- **Content Freshness Check**: Identifies stale posts not updated in over a year.
- **Broken Links Check**: Scans for broken links within your content.
- **Content Length Check**: Detects posts with insufficient content length.
- **Media Usage Check**: Identifies posts missing featured images.
- **Duplicate Content Check**: Finds posts with duplicate titles.
- **Revision Count Check**: Detects posts with excessive revisions.
- **Taxonomy Usage Check**: Identifies posts missing categories or tags.

## Installation

1. **Download the Plugin:**

    - Clone the repository:
        ```
        git clone https://github.com/yourusername/site-vitals-checker.git
        ```

    - Or download the ZIP file from GitHub and extract it.
2. **Upload to WordPress:**

    - Via FTP:
        - Upload the `site-vitals-checker` folder to the `/wp-content/plugins/` directory.
    - Or via WordPress Dashboard:
        - Navigate to `Plugins > Add New > Upload Plugin`.
        - Choose the ZIP file and click `Install Now`.
3. **Activate the Plugin:**

    - Go to `Plugins > Installed Plugins`.
    - Find **Site Vitals Checker** and click `Activate`.

## Usage

1. **Accessing the Plugin:**

    - After activation, navigate to `Dashboard > Site Vitals Checker`.
2. **Running Checks:**

    - The plugin automatically runs all checks upon activation.
    - To manually run a check, go to the respective section and click `Run Check`.
3. **Viewing Results:**

    - Results are displayed in categorized summaries.
    - Each check shows a status (`Good`, `Needs Attention`, etc.) and provides recommendations.
4. **Interpreting Recommendations:**

    - Follow the actionable recommendations to improve your site's health.
    - For placeholder checks (e.g., Color Contrast), consider integrating third-party services or custom implementations.

## Frequently Asked Questions

**Q1: Does this plugin affect site performance?**  
A1: The plugin is optimized to run efficiently. However, some checks, like broken link scans, may be resource-intensive. It's recommended to run such checks during off-peak hours or implement caching mechanisms.

**Q2: How often should I run these checks?**  
A2: For optimal site health, run security and performance checks weekly, and content-related checks monthly. Automated scheduling can be implemented for regular monitoring.

**Q3: Can I disable specific checks?**  
A3: Currently, all checks are enabled by default. Future updates may include options to enable or disable specific checks based on your requirements.

**Q4: Is this plugin compatible with all WordPress themes and plugins?**  
A4: The plugin is designed to be compatible with most WordPress themes and plugins. However, conflicts may arise with custom or poorly coded themes/plugins. It's recommended to test the plugin in a staging environment before deploying it on a live site.

## Contributing

Contributions are welcome! If you'd like to improve **Site Vitals Checker**, please follow these steps:

1. **Fork the Repository:**

    - Click the `Fork` button at the top right of this page.
2. **Clone Your Fork:**
    ```
    git clone https://github.com/yourusername/site-vitals-checker.git
    ```

3. **Create a New Branch:**
    ```
    git checkout -b feature/improve-security-check
    ```

4. **Make Your Changes:**

    - Implement your feature or fix a bug.
5. **Commit Your Changes:**
    ```
    git commit -m "Improve security check with additional validations"
    ```

6. **Push to Your Fork:**
    ```
    git push origin feature/improve-security-check
    ```

7. **Create a Pull Request:**

    - Navigate to the original repository and click `Compare & pull request`.

## License