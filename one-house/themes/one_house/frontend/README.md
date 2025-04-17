# Pattern lab component library (starter version)

The component library is a front-end framework/starter, focusing on functionality over opinionated styles. It features purposeful and semantic HTML, minimal JavaScript, and SASS variables for easy customization.

It is designed to be modified once downloaded, and all CSS/JS/etc to be modified per specific project. It acts as a starter, with common, accessible components present in a vanilla way, that can be used or not, and styles as needed.

## Installation

### Scaffolding

Depending on the project, this can be downloaded/cloned in several different places, but best practice is to keep the folder named "pattern-lab-starter", wherever you choose to utilize it.

- Drupal: "/themes/custom/client_theme/pattern-lab-starter"

### Remove .git tracking

Remove the repo git, as the desire is to commit all to your new client theme.

- `rm -R .git`

### Dependencies

Dependencies need to be installed with [node/npm](https://docs.npmjs.com/getting-started/installing-node), and is best pinned to stable versions via [nvm](https://github.com/nvm-sh/nvm).

- `cd pattern-lab-starter`
- `nvm use`
- `npm install`

## Development

The component library includes [Pattern Lab](https://patternlab.io/) for component based development. Your own components can be added to the `source/_patterns/components` folder. Static assets such as JavaScript, CSS and images will be served out of the `build` folder.

### To start the development server:

- `cd pattern-lab-starter` (if not there)
- `nvm use` (if you haven't previously done so, this uses version 19.0.0, so you may need to install)
- `npm run develop`

### Building for production

To build your code for production, run the following:

- `npm run develop` (this will need to be run to build the pattern lab files)
- `build:all` (this will minify the css and js files)

The `public` folder contains all of your compiled assets (CSS, JavaScript etc.) and contains a static generated version of your Fractal component library, which can be used for previews and an online reference to your component library

## Moving From Fractal

There are some differences to note as the library has moved from [Fractal](https://fractal.build/) to [Pattern Lab](https://patternlab.io/).

- To access the page templates head and foot sections see `/source/_meta/_head.twig` to access and update the head and `source/_meta/_foot.twig` to access the closing tags of the document. Instead of placing wrappers and site-header and site-footer in here (similar to the page-preview files in Fractal), these elements go into a template called `page-structure.twig` at `source/_patterns/pages/page-structure.twig`. This template is extended in all pages, for example in `kitchen-sink.twig` the document starts with `{% extends "@pages/page-structure.twig" %}` and then `{% block content %}` contains all of the custom content to be inserted and ends with `{% endblock %}`
- Global data can be added to `source/_data/data.json`, this is useful for common data, such as the data associated with a site-header, site-footer or other global elements that you don't want to repeat the data on a page-by-page basis
- For components, the associated json files are not suffixed with `.config.json`, just `.json` is needed (not `.config`)
- For json files - the "context" object is not used in pattern lab, instead wrap your components in an object with the same (or similar) name as the associated component. See `source/_patterns/components/card/card.json` for an example, you'll notice that the data is wrapped in an object called "card". This will help with adding multiple instances of a component to a page, as well as nested components
- Page templates are located in `source/_patterns/pages`. Here is an example of the syntax of adding a component to a page: `{% include "@components/card-set/card-set.twig" with kitchen_sink_card_set %}`, where `kitchen_sink_card_set` is an object in the associated page json file with the data for the component in this particular context
- The image `| path` filter in fractal for images is not in use in Pattern Lab. Images are linked relatively to the source folder. For exmaple if you have a twig template with `<img src="{{ image.src }}">`, in the associated json use `"src": "../../images/image.png"`, or if the image is not coming from the json file, `{{ "../../images/image.png" }}` for example
- Pattern Lab does not use variants in the same way that fractal does. To accomplish something similar, place a new variant of the component within the respective component folder ("see buttons example")
  – When linking to a npm package in the index.scss file - link relatively to the node_modules directory and specific sass file, for example: `@import "../../node_modules/gridlex/src/gridlex.scss";`
