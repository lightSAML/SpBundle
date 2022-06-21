# Changelog

[unreleased]
## [2.0.0] - 2022-06-21
### Added
- Symfony 5.0/6.0 support

### Changed
- src/LightSaml/SpBundle/DependencyInjection/Configuration.php (fixed node declaration fo sf5+)
- src/LightSaml/SpBundle/Controller/DefaultController.php & src/LightSaml/SpBundle/Resources/config/services.yml (inject services instead of using container)
- Php minimum version to 7.2.5
- lightsaml/lightsaml to litesaml/lightsaml

### Removed
- Support for symfony <= 4.x

### @todo before merge
release lightsaml/symfony-bridge version 2.x