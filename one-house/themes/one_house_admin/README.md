# One House Admin Theme

The One House Admin theme is a custom Drupal admin theme tailored for the One House website. It enhances the default admin interface with improved usability, cleaner styling, and a consistent look and feel that aligns with the front-end branding.

## Features

- Custom styling for Drupal's admin UI
- Tailwind CSS integration (optional)
- Clean and modern interface optimized for content editors
- Layout tweaks and overrides for better usability
- Built to work alongside the One House frontend theme (`one_house`)

## Directory Structure

    one_house_admin/ 
        ├── css/ # Custom admin styles 
        ├── templates/ # Admin template overrides 
        ├── theme.info.yml # Theme definition 
        ├── theme.settings.yml # Default settings 
        ├── tailwind.config.js # Tailwind configuration (optional) 
        └── README.md # This file


## Installation

1. Copy the theme into your custom themes folder:
   git clone https://github.com/ausgandalf/drupal_snippets.git
   cp -r drupal_snippets/one-house/themes/one_house_admin /web/themes/custom/

2. Enable the admin theme:
   drush theme:enable one_house_admin

3. Set it as the default admin theme:
   drush config-set system.theme admin one_house_admin

4. Rebuild cache:
   drush cr

## Requirements

- Drupal 10.x+
- Optional: Tailwind CSS setup for compiling styles
- Node.js (if using Tailwind or other build tools)

## Development

If Tailwind CSS is used:

```bash
npm install
npm run dev
