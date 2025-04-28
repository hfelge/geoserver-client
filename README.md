# GeoServer Client (PHP)

A modern, lightweight PHP client for interacting with the GeoServer REST API.

## Features

- Retrieve workspaces
- Create new workspaces
- (Planned) Manage datastores, layers, and WFS-T transactions

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

// Create a new workspace
$client->createWorkspace('my_workspace');

// Get a list of all workspaces
$workspaces = $client->getWorkspaces();
print_r($workspaces);
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
