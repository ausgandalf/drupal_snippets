# These templates are designed to be included in field overrides:

For example, a link field template override may look like:

  `{% include directory ~ '/templates/_includes/fields/links/link--with-options.html.twig' with {'url': item.content['#url'], 'title': item.content['#title'], 'class': 'btn', 'target': '_self'} %}`

For comma seperated entities, it may look like:

  `{% include directory ~ '/templates/_includes/fields/entity-reference/entity-reference--comma-seperated.html.twig' %}`
