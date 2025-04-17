# OneHouse Base

OneHouse Base is a foundational custom Drupal module created for the One House website project. It provides essential configurations, utility services, and helper components to support a scalable and consistent site architecture.

This module serves as the backbone for other OneHouse-specific features and themes.

## Key Features

- Base configurations for a custom Drupal site
- Helper services and reusable logic
- Structured scaffolding for further custom module development
- Site-wide settings and integrations
- Clean separation of concerns with Domain-Driven Design principles

## Directory Structure

    onehouse_base/ 
        ├── config/ # Default configuration exports 
        ├── src/ # PHP classes (services, plugins, etc.) 
        │ └── Service/ 
        ├── templates/ # Twig template overrides (if applicable) 
        ├── onehouse_base.info.yml # Module info 
        ├── onehouse_base.module # Hook implementations 
        └── README.md # You're here!

## Installation

1. Place the module in your project:
   git clone https://github.com/ausgandalf/drupal_snippets.git
   cp -r drupal_snippets/one-house/modules/onehouse_base /web/modules/custom/

2. Enable the module:

    ```bash
   drush en onehouse_base

Or use the admin UI at /admin/modules.

## Requirements

- Drupal 10.x+
- Recommended:
  - Config Split
  - Layout Builder
  - Token
  - Pathauto

## Development Guidelines

- Follow Drupal coding standards (PSR-4, annotations, etc.)
- All configuration is exportable using `drush config-export`
- Use service classes to encapsulate business logic
- Designed to pair with OneHouse custom theme and additional feature modules

## Contributing

Have suggestions or improvements? Feel free to fork the repo or submit a pull request. Contributions are welcome.

## License

MIT License. See LICENSE for details.