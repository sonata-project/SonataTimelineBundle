# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.4.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.3.1...3.4.0) - 2019-06-16
### Added
- Add missing package `twig/twig` with versions `^1.35 || ^2.4`

### Fixed
- deprecation notice about using namespaced classes from `\Twig\`
- Fix deprecation for symfony/config 4.2+

### Removed
- support for php 5 and php 7.0

## [3.3.1](https://github.com/sonata-project/SonataTimelineBundle/compare/3.3.0...3.3.1) - 2018-05-08

### Fixed

- Fix the subject generated link.
- Fix the `subject_text` when data is empty.

### Added
- Added support for `timeline-bundle` 3.0

## [3.3.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.2.0...3.3.0) - 2018-02-08
### Added
- added block title translation domain option
- added block class option

### Changed
- Switch all templates references to Twig namespaced syntax
- Switch from templating service to sonata.templating
- changed block icon configuration to icon class instead of html code
- replaced box with bootstrap panel layout in TimelineBlock

### Removed
- Removed default title from blocks
- remove some unused divs from `timeline.html.twig` block

## [3.2.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.1.1...3.2.0) - 2017-11-30
### Changed
- Removed usage of old form type aliases

### Added
- added Russian translations

### Fixed
- It is now allowed to install Symfony 4

### Removed
- support for old versions of php and Symfony

## [3.1.1](https://github.com/sonata-project/SonataTimelineBundle/compare/3.1.0...3.1.1) - 2017-06-14
### Fixed
- Deprecated `security.context` usage
- Deprecated block service usage
- Fixed wrong argument replacement. There is no 5th parameter for `sonata.timeline.block.timeline`
- Fixed hardcoded paths to classes in `.xml.skeleton` files of config

## [3.1.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.0.0...3.1.0) - 2016-11-25
### Fixed
- Removed deprecation warning for `AdminExtension` usage

### Removed
- Internal test classes are now excluded from the autoloader
