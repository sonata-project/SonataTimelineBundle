# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.7.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.6.0...3.7.0) - 2021-02-15
### Changed
- [[#306](https://github.com/sonata-project/SonataTimelineBundle/pull/306)] Updates dutch translations ([@zghosts](https://github.com/zghosts))
- [[#260](https://github.com/sonata-project/SonataTimelineBundle/pull/260)] SonataEasyExtendsBundle is now optional, using SonataDoctrineBundle is preferred ([@jordisala1991](https://github.com/jordisala1991))

### Deprecated
- [[#260](https://github.com/sonata-project/SonataTimelineBundle/pull/260)] Using SonataEasyExtendsBundle to add Doctrine mapping information ([@jordisala1991](https://github.com/jordisala1991))

### Removed
- [[#264](https://github.com/sonata-project/SonataTimelineBundle/pull/264)] Support for PHP < 7.2 ([@wbloszyk](https://github.com/wbloszyk))
- [[#264](https://github.com/sonata-project/SonataTimelineBundle/pull/264)] Support for Symfony < 4.4 ([@wbloszyk](https://github.com/wbloszyk))

## [3.6.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.5.0...3.6.0) - 2020-06-29
### Added
- [[#227](https://github.com/sonata-project/SonataTimelineBundle/pull/227)]
  Support for Twig 3 ([@franmomu](https://github.com/franmomu))

### Removed
- [[#255](https://github.com/sonata-project/SonataTimelineBundle/pull/255)]
  Remove SonataCoreBundle dependencies
([@wbloszyk](https://github.com/wbloszyk))
- [[#227](https://github.com/sonata-project/SonataTimelineBundle/pull/227)]
  Remove `sonata-project/intl-bundle` dependency
([@franmomu](https://github.com/franmomu))
- [[#247](https://github.com/sonata-project/SonataTimelineBundle/pull/247)]
  Support for Symfony < 4.3 ([@franmomu](https://github.com/franmomu))

## [3.5.0](https://github.com/sonata-project/SonataTimelineBundle/compare/3.4.0...3.5.0) - 2020-01-12
### Added
- Added DoctrineBundle dependency in the `composer.json`

### Changed
- Changed `Sonata\TimelineBundle\Spread\AdminSpread::__construct` signature
  to use `Doctrine\Common\Persistence\ManagerRegistry`
  instead of  `Symfony\Bridge\Doctrine\RegistryInterface`

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
