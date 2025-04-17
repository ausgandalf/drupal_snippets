# One House Theme

The One House theme is a custom Drupal theme designed as the frontend layer for the One House website. Built using Twig, Tailwind CSS, and Drupal best practices, it provides a clean, scalable foundation for theming across the site.

## Features

- Tailwind CSS integration for utility-first styling
- Custom templates and layout overrides
- Component-based structure using partials
- Responsive and accessibility-ready out of the box
- Designed to work seamlessly with the `onehouse_base` module

## Directory Structure

    one_house/ 
        ├── config/ # Theme configuration 
        ├── css/ # Custom styles (if applicable) 
        ├── templates/ # Twig templates 
        │ ├── layout/ 
        │ ├── node/ 
        │ └── includes/ 
        ├── tailwind.config.js # Tailwind config 
        ├── theme.info.yml # Theme definition file 
        ├── theme.settings.yml # Default theme settings 
        └── README.md # This file


## Installation

1. Copy the theme into your custom themes folder:

    ```bash
   git clone https://github.com/ausgandalf/drupal_snippets.git
   cp -r drupal_snippets/one-house/themes/one_house /web/themes/custom/

2. Enable the theme:

    ```bash
   drush theme:enable one_house

3. Set it as the default:

    ```bash
   drush config-set system.theme default one_house

4. Rebuild cache:

    ```bash
   drush cr

## Requirements

- Drupal 10.x+
- Tailwind CLI or Laravel Mix (based on your build setup)
- Node.js (for Tailwind build)

## Development

This theme uses Tailwind CSS for styling. To watch and build assets:

```bash
npm install
npm run dev
