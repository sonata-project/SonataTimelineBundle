# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
