# Site Vitals for WordPress®

**Site Vitals for WordPress®** is a comprehensive WordPress plugin designed to monitor, evaluate, and improve various aspects of your website's health. 

From performance and security to SEO, user experience, and content management, this free plugin offers actionable insights and recommendations. 

With asynchronous loading, cached results, and a user-friendly interface, Site Vitals for WordPress® ensures that you have a clear understanding of your site’s strengths and areas for improvement.

Note: Accessibility (Compliance) checks are currently in development and will be introduced in a future release.

## Table of Contents

- [Features](#features)
- [Usage](#usage)
- [Filters and Extensibility](#filters-and-extensibility)
- [Frequently Asked Questions](#frequently-asked-questions)
- [Contributing](#contributing)
- [License](#license)

## Features

**Site Vitals for WordPress®** performs a wide range of checks, grouped into categories. The plugin runs these checks automatically on activation and caches the results for a faster, smoother experience.

### Security Checks

- **SSL Certificate Check**: Ensures SSL is enabled for secure browsing.
- **Plugin/Theme/Core Update Checks**: Alerts you to outdated components needing updates.
- **Login Security Check**: Checks for two-factor authentication usage.
- **Security Headers Check**: Verifies recommended security headers are present.
- **File Permissions Check**: Confirms critical files have secure permissions.

### SEO Checks

- **SEO Meta Tags Check**: Validates the presence of title and description meta tags.
- **SEO Plugin Detection**: Identifies if an SEO plugin is active.
- **Image Alt Text Check**: Detects images lacking alt text.
- **Sitemap Check**: Confirms the presence of a sitemap for better indexing.

### Performance Checks

- **Page Load Speed & Server Response Time**: Measures homepage load time and server response.
- **Image Optimization Check**: Finds oversized images that could slow down the site.
- **Code Optimization Check**: Flags excessive CSS/JS files.
- **Third-Party Scripts Check**: Identifies slow external scripts.
- **Database Optimization Check**: Checks for excessive transients and revisions.
- **Caching Status Check**: Determines if a caching plugin is active.
- **Gzip Compression & PHP Version Checks**: Ensures your environment is configured for optimal performance.

### User Experience (UX) Checks

- **Mobile Responsiveness Check**: Verifies theme responsiveness for mobile devices.
- **Navigation Clarity Check**: Detects orphaned pages.
- **404 Error Check**: Identifies broken links resulting in 404 pages.
- **Page Load Time (Key Pages) & Font Readability**: Reviews load times and typography standards.

### Content Management Checks

- **Content Freshness & Broken Links**: Finds stale posts and broken links.
- **Content Length & Media Usage**: Flags short posts and posts missing featured images.
- **Duplicate Content & Revision Count Checks**: Identifies duplicate titles and excessive revisions.
- **Taxonomy Usage Check**: Ensures posts are categorized or tagged.

_(Accessibility/Compliance checks are planned for a future update.)_

1. **Download the Plugin:**

    - Download the plugin ZIP from the [GitHub repository](https://github.com/robertdevore/site-vitals-for-wordpress/).
2. **Upload to WordPress:**

    - **Via WordPress Dashboard:**
        - Go to `Plugins > Add New`.
        - Click `Upload Plugin`.
        - Select the downloaded ZIP file and click `Install Now`.
    - **Via FTP:**
        - Unzip the downloaded file.
        - Upload the `site-vitals-for-wordpress` folder to `/wp-content/plugins/`.
3. **Activate the Plugin:**

    - Go to `Plugins > Installed Plugins`.
    - Find **Site Vitals for WordPress®** and click `Activate`.

Upon activation, the plugin immediately runs checks and caches the results for quick subsequent loads.

## Usage

1. **Accessing the Dashboard:**

    - Navigate to `Dashboard > Site Vitals`.
    - You'll see a grid of categories (Performance, Security, SEO, UX, Content, Technical).
    - Each category initially shows a loading indicator and then fetches results asynchronously to prevent slow page loads.
2. **Reviewing Checks:**

    - Once results are loaded, you'll see color-coded summaries:
        - Green ("Good")
        - Yellow ("Needs Attention")
        - Red ("Needs Improvement")
    - Click any category's submenu item (e.g., `Site Vitals > Performance`) to view detailed checks and recommendations.
3. **Follow Recommendations:**

    - For each check, you'll find actionable advice--e.g., optimize images, update plugins, or add missing alt text.
    - Implementing these suggestions helps maintain and improve site health over time.
4. **Caching and Re-checks:**

    - Results are cached for about 12 hours. After making improvements, return later to see updated results.
    - The asynchronous loading and caching ensure minimal impact on your site's performance.

## Filters and Extensibility

Developers can adjust certain checks using filters:

- **`sv_common_sitemap_urls`**: Modify the array of sitemap URLs checked.
```
add_filter( 'sv_common_sitemap_urls', function( $urls ) {
    $urls[] = home_url( '/custom-sitemap.xml' );
    return $urls;
} );
```

- **`sv_404_pages_to_check`**: Change the set of pages checked for 404 errors.
```
add_filter( 'sv_404_pages_to_check', function( $pages ) {
    $pages[] = home_url( '/another-test-page' );
    return $pages;
} );
```

These filters allow advanced users to customize checks for their specific site setup.

## Frequently Asked Questions

**Q1: Will these checks slow down my site?**  
A1: The plugin caches results and uses asynchronous loading to minimize performance impact. Most checks run efficiently, and the initial comprehensive run is done at activation.

**Q2: How often should I check my site's vitals?**  
A2: Since results are cached, checking once or twice a week is sufficient for most sites. After making improvements, wait for the cache to refresh (12 hours) before reviewing updates.

**Q3: Can I disable specific checks?**  
A3: Not currently. All checks run as a set. Future updates may provide more granular control.

**Q4: Are Accessibility (Compliance) checks available?**  
A4: They are planned for a future release. The current version focuses on Performance, Security, SEO, UX, and Content.

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

This plugin is open-source software licensed under the GPL-2.0+ license.