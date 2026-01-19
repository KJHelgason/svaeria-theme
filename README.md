# Svaeria - Kadence Child Theme

Custom WordPress child theme for Svaeria Nordic Jewelry (https://svaeria.is).

## Stack

- WordPress
- WooCommerce 10.4.3
- Kadence Theme (parent)
- PHP, jQuery, CSS

## Development Environment

**Local Development:** Local by Flywheel
- No npm/webpack build process required
- Changes to CSS/JS/PHP are instant

## Theme Structure

```
kadence-child/
├── functions.php                    # Bootstrap loader (~46 lines)
├── style.css                        # All custom CSS with design tokens
├── inc/
│   ├── class-currency-converter.php # Multi-currency system (ISK base)
│   ├── enqueues.php                 # Scripts & styles enqueuing
│   ├── helpers.php                  # Utility functions & AJAX handlers
│   ├── header-functions.php         # Header & footer logic
│   ├── widgets.php                  # Widget areas
│   ├── customizer.php               # Customizer settings
│   ├── woocommerce.php              # WooCommerce customizations
│   └── shortcodes/
│       ├── product-shortcodes.php   # [new_arrivals], [our_favorites]
│       ├── layout-shortcodes.php    # [collection_banner], [split_content], [hero_image]
│       ├── content-shortcodes.php   # [trust_badges], [newsletter_section], [section_header], [love_from_north]
│       └── social-shortcodes.php    # [social_block], [shop_categories], gallery lightbox
├── assets/
│   ├── css/                         # (Reserved for future CSS modules)
│   └── js/
│       └── main.js                  # Header interactions, search, currency
├── template-parts/
│   └── header/
│       └── header-main.php          # Custom header with mega menu
├── footer-custom.php                # Custom footer
└── page-home.php                    # Homepage template
```

## Deployment (GitHub + Hostinger)

### Initial Setup

1. **Create GitHub repository:**
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/svaeria-theme.git
   git branch -M main
   git push -u origin main
   ```

2. **Connect Hostinger:**
   - Log into Hostinger hPanel
   - Go to **Website > Git**
   - Connect your GitHub account
   - Select repository and branch (main)
   - Set deployment path: `/public_html/wp-content/themes/kadence-child/`

### Deploy Workflow

1. Make changes locally
2. Test on Local by Flywheel
3. Commit and push:
   ```bash
   git add -A
   git commit -m "Description of changes"
   git push
   ```
4. Deploy via Hostinger hPanel (or enable auto-deploy)
5. Clear LiteSpeed cache after deployment

## Available Shortcodes

### Product Shortcodes
- `[new_arrivals title="NEW ARRIVALS" count="8" columns="4" link_text="SEE MORE"]`
- `[our_favorites title="OUR FAVORITES" count="8" link_text="See more" orderby="popularity"]`

### Layout Shortcodes
- `[collection_banner subtitle="COLLECTION" title="Valkyria" image="URL" bg_color="grey|blue"]Content[/collection_banner]`
- `[split_content subtitle="" title="" text="" button_text="" image="URL" reverse="false" bg_color="grey|blue"]`
- `[hero_image image="URL" height="600" title="" subtitle="" button_text="" overlay="true"]`

### Content Shortcodes
- `[trust_badges]`
- `[newsletter_section title="Our newsletter" description="..."]`
- `[section_header subtitle="" title="" text=""]`
- `[love_from_north subtitle="" title="" text="" stamp_image="URL"]`

### Social Shortcodes
- `[social_block title="" text="" button_text="" image="URL" reverse="false"]`
- `[shop_categories columns="4" title="SHOP BY CATEGORY"]`

## Currency System

Base currency: ISK (Icelandic Króna)

Supported currencies: ISK, USD, EUR, GBP, NOK, DKK, SEK

Exchange rates are configured in `inc/class-currency-converter.php`. Update rates periodically or integrate with an API.

## Naming Conventions

- PHP functions: `jj_` prefix (e.g., `jj_convert_price()`)
- CSS classes: `jj-` prefix (e.g., `.jj-mega-menu`)
- JavaScript events: `.nordic` namespace (e.g., `click.nordic`)

## Key Design Tokens

CSS variables (in `:root`):
- Colors: `--jj-black`, `--jj-cream`, `--jj-gold-accent`, `--jj-svaeria-blue`, `--jj-section-grey`
- Fonts: `--jj-font-heading` (Spectral), `--jj-font-body` (Inter)
- Spacing: `--jj-spacing-xs` through `--jj-spacing-xxl`

## Cache Clearing

After deployment, clear LiteSpeed Cache via WordPress admin or Hostinger hPanel.
