# TemplateStylesUnlimited

This MediaWiki extension is meant to work in tandem with [TemplateStyles](https://www.mediawiki.org/wiki/Extension:TemplateStyles). TemplateStyles introduces reusable styles that load when certain templates are transcluded, and allows users to edit these styles instead of resorting to inline CSS.

Because users are allowed to edit these styles, TemplateStyles performs CSS sanitization in order to restrict what users can do. Unfortunately, it has a terrible track record of being overly restrictive due to lacking newer CSS features, which in turn led to extensions such as [TemplateStylesExtender](https://www.mediawiki.org/wiki/Extension:TemplateStylesExtender). While TemplateStylesExtender goes a long way, the support is still lacking, and users still have to resort to site styles or Gadgets.

TemplateStylesUnlimited solves this problem by removing CSS sanitization from TemplateStyles entirely. In turn, it restricts template styles to the MediaWiki namespace, such that only users that can otherwise edit sitewide CSS can touch template styles. This means two things:

- You **should not** use TemplateStylesUnlimited if the ability of regular users to edit template CSS is important to you.
- You **should** use TemplateStylesUnlimited if the ability to load certain styles together with certain templates, without fragmenting the browser cache like category-loaded gadgets do, is important to you.

## Documentation

User documentation can be found on [mediawiki.org](https://www.mediawiki.org/wiki/Extension:TemplateStylesUnlimited).
