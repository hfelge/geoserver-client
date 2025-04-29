# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),  
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.0] - 2025-04-28
### Added
- Added getWorkspace(string $name) to WorkspaceManager
- Added getDatastore(string $workspace, string $datastore) to DatastoreManager
- Added getFeatureType(string $workspace, string $datastore, string $featureType) to FeatureTypeManager
- Added getLayer(string $layerName) to LayerManager
- Added getStyle(string $styleName) to StyleManager
- Added corresponding PHPUnit 12 tests for all new methods

### Notes
- Now fully supports both list and single item retrieval for Workspaces, Datastores, FeatureTypes, Layers, and Styles.

## [1.2.0] - 2025-04-28
### Added
- `FeatureTypeManager` with full CRUD support and `featureTypeExists()`
- `LayerManager` with full CRUD support and `layerExists()`
- PHPUnit 12 tests for both FeatureTypeManager and LayerManager

### Changed
- GeoServerClient now initializes `featureTypeManager` and `layerManager` automatically

### Notes
- This completes full CRUD support for Workspaces, Datastores, FeatureTypes, and Layers
- WFS-T and StyleManager support is planned for a future release

## [1.0.0] - 2025-04-28
### Added
- Initial stable release of the GeoServerClient PHP library.
- Retrieve all workspaces from GeoServer.
- Create new workspaces.
- Composer-ready package with PSR-4 autoloading.
- PHPUnit test structure prepared.

### Notes
- Future functionality for PostGIS datastore management and WFS-T transactions is planned.
