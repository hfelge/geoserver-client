# GeoServer Client (PHP)

A modern, lightweight PHP client for interacting with the GeoServer REST API.

## Features

- Manage GeoServer **Workspaces** (get all, get one, exists, create, update, delete)
- Manage GeoServer **Datastores** (get all, get one, exists, createPostGIS, update, delete)
- Manage GeoServer **FeatureTypes** (get all, get one, exists, create, update, delete)
- Manage GeoServer **Layers** (get all, get one, exists, publish, update, delete)
- Manage GeoServer **Styles** (get all, get one, exists, create, update, delete)
- Full PHPUnit 12 test coverage
- PSR-4 ready and composer-installable
- Future: WFS-T transaction support

## Installation

Install via Composer:

```bash
composer require hfelge/geoserver-client
```

## Usage Example
```php
<?php
require 'vendor/autoload.php';

use Hfelge\GeoServerClient\GeoServerClient;

$client = new GeoServerClient('https://your-geoserver-url/geoserver', 'admin', 'geoserver');

// === WORKSPACES ===
$workspaces = $client->workspaceManager->getWorkspaces();
$workspace = $client->workspaceManager->getWorkspace('example_ws');
if (!$client->workspaceManager->workspaceExists('example_ws')) {
    $client->workspaceManager->createWorkspace('example_ws');
}

// === DATASTORES ===
$datastores = $client->datastoreManager->getDatastores('example_ws');
$datastore = $client->datastoreManager->getDatastore('example_ws', 'example_ds');
if (!$client->datastoreManager->datastoreExists('example_ws', 'example_ds')) {
    $client->datastoreManager->createPostGISDatastore('example_ws', 'example_ds', [
        'host' => 'localhost',
        'port' => '5432',
        'database' => 'gisdb',
        'user' => 'geo_user',
        'passwd' => 'secret'
    ]);
}

// === FEATURETYPES ===
$fts = $client->featureTypeManager->getFeatureTypes('example_ws', 'example_ds');
$featureType = $client->featureTypeManager->getFeatureType('example_ws', 'example_ds', 'stadtgrenzen');
if (!$client->featureTypeManager->featureTypeExists('example_ws', 'example_ds', 'stadtgrenzen')) {
    $client->featureTypeManager->createFeatureType('example_ws', 'example_ds', [
        'name' => 'stadtgrenzen',
        'nativeName' => 'stadtgrenzen',
        'title' => 'Stadtgrenzen',
        'srs' => 'EPSG:4326'
    ]);
}

// === LAYERS ===
$layers = $client->layerManager->getLayers();
$layer = $client->layerManager->getLayer('stadtgrenzen');
if (!$client->layerManager->layerExists('stadtgrenzen')) {
    $client->layerManager->publishLayer('example_ws', 'example_ds', 'stadtgrenzen');
}

// === STYLES ===
$styles = $client->styleManager->getStyles();
$style = $client->styleManager->getStyle('default_point');
if (!$client->styleManager->styleExists('default_point')) {
    $client->styleManager->createStyle('default_point', file_get_contents('path/to/your.sld'));
}
```

## Requirements
+ PHP 8.3 or higher
+ GeoServer instance with REST API access

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
