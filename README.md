# GeoServer Client (PHP)

A modern, lightweight PHP client for interacting with the GeoServer REST API (REST & SLD Upload).

## Features

- Manage GeoServer **Workspaces** (`get`, `exists`, `create`, `update`, `delete`)
- Manage GeoServer **Datastores** (`get`, `exists`, `createPostGIS`, `update`, `delete`)
- Manage GeoServer **FeatureTypes** (`get`, `exists`, `create`, `update`, `delete`)
- Manage GeoServer **Layers** (`get`, `exists`, `publish`, `update`, `delete`)
- Manage GeoServer **Styles**:
    - `createWorkspaceStyle()` (SLD upload via Slug)
    - `assignStyleToLayer()`
    - `updateStyle()`, `deleteStyle()`, `styleExists()`
- Robust error handling with `GeoServerException`
- GeoServer availability check via `isAvailable()`
- 100% PHPUnit 12 test coverage
- PSR-4 ready and composer-installable

## Installation

Install via Composer:

```bash
composer require hfelge/geoserver-client
```

## Usage Example
```php
<?php

use Hfelge\GeoServerClient\GeoServerClient;

$client = new GeoServerClient('http://localhost:8080/geoserver', 'admin', 'geoserver');

// === WORKSPACES ===
if (!$client->workspaceManager->workspaceExists('example_ws')) {
    $client->workspaceManager->createWorkspace('example_ws');
}

// === DATASTORES ===
if (!$client->datastoreManager->datastoreExists('example_ws', 'example_ds')) {
    $client->datastoreManager->createPostGISDatastore('example_ws', 'example_ds', [
        'host'     => 'localhost',
        'port'     => '5432',
        'database' => 'gis',
        'user'     => 'geo_user',
        'passwd'   => 'secret',
    ]);
}

// === FEATURETYPES ===
if (!$client->featureTypeManager->featureTypeExists('example_ws', 'example_ds', 'stadtgrenzen')) {
    $client->featureTypeManager->createFeatureType('example_ws', 'example_ds', [
        'name'       => 'stadtgrenzen',
        'nativeName' => 'stadtgrenzen',
        'title'      => 'Stadtgrenzen',
        'srs'        => 'EPSG:4326',
    ]);
}

// === LAYERS ===
if (!$client->layerManager->layerExists('stadtgrenzen')) {
    $client->layerManager->publishLayer('stadtgrenzen');
}

// === STYLES ===
$sld = file_get_contents(__DIR__ . '/style.sld');
$client->styleManager->createWorkspaceStyle('example_ws', 'my_custom_style', $sld);
$client->styleManager->assignStyleToLayer('stadtgrenzen', 'my_custom_style');

```

## Requirements
+ PHP 8.3 or higher
+ GeoServer instance with REST API access
+ Optional: Docker/DDEV setup for local testing

## Project Structure

```sql
src/    → Contains the core library
tests/  → Contains PHPUnit tests
```

## Development
Run PHPUnit tests:
```bash
vendor/bin/phpunit
```

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
