# GeoServer Client (PHP)

A modern, lightweight PHP client for interacting with the GeoServer REST API.

## Features

- Manage GeoServer Workspaces (create, read, update, delete)
- Manage GeoServer Datastores (create PostGIS stores, read, update, delete)
- Future: Layer management and WFS-T transaction support

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

// Workspace example
$client->workspaceManager->createWorkspace('my_workspace');

// Datastore example
$client->datastoreManager->createPostGISDatastore('my_workspace', 'my_postgis_store', [
    'host' => 'localhost',
    'port' => '5432',
    'database' => 'gisdb',
    'user' => 'geo_user',
    'passwd' => 'secret'
]);
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
