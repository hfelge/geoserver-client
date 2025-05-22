# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),  
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

# [1.8.0] - 2025-05-22

### Added
- Introduced `UserGroupManager` with support for:
  - Listing all groups (`getGroups()`)
  - Listing groups for a specific user (`getGroupsForUser(username)`)

### Internal
- Codebase prepared for full group CRUD (create, update, delete) in future version
- Added live test coverage for group listing and user-to-group resolution

# [1.7.0] - 2025-05-18

### Added
- Full `RoleManager` with:
    - Role creation, deletion and lookup
    - Role assignment to users and groups
    - Retrieval of roles for users and groups
- Methods: `getRoles()`, `roleExists()`, `getRolesForUser()`, `getRolesForGroup()`,
  `assignRoleToUser()`, `removeRoleFromUser()`, `assignRoleToGroup()`, `removeRoleFromGroup()`

### Changed
- Removed dependency on unstable `/roles/{role}` endpoint
- Improved role existence checks and test behavior for unknown users/groups

### Tests
- 100% test coverage for `RoleManager`

# [1.6.0] - 2025-05-16

### Added
- Introduced `UserManager` with full CRUD operations for GeoServer users via REST security API
- Added `GeoServerClient::publishFeatureLayer()` to simplify feature type and layer creation in one step
- Added `GeoServerClient::isAvailable()` to verify GeoServer instance availability
- New integration and unit tests for user management and feature publishing

### Changed
- Unified `GeoServerException` handling across all components including new modules

### Security
- Improved robustness and error reporting in security-related API endpoints (user operations)

## [1.5.1] - 2025-05-02

### Fixed
- Fixed deletion of datastores with `.` in their name (GeoServer requires `.xml` suffix in DELETE URL)

## [1.5.0] - 2025-05-02

### Added
- `StyleManager::createWorkspaceStyle()` – upload SLD styles using `Slug:` and `Content-Type` header
- `StyleManager::assignStyleToLayer()` – assign a style to a layer via REST
- `StyleManager::updateStyle()`, `deleteStyle()`, `styleExists()`, `getStyle()`
- `GeoServerClient::isAvailable()` – check if GeoServer is reachable (with optional caching)
- Live tests using a local GeoServer instance (DDEV-compatible), auto-skipped when unavailable
- Unified `GeoServerException` used in all managers
- 100% test coverage with PHPUnit 12

### Changed
- `StyleManager` now uses `POST` with raw SLD content only (replaces old JSON+PUT combination)
- Error codes 403, 404, 406 are now properly interpreted (e.g., on style assignment)
- All managers return consistent results: `true`, `false`, `array`, or `GeoServerException`

### Fixed
- `styleExists()` now supports workspace-specific styles (via optional `workspace` context)
- `GeoServerClient::request()` merges headers safely (custom headers take precedence)

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
