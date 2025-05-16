# GeoServerClient Roadmap

A development plan for future versions of the GeoServerClient PHP library.

---

## ✅ v1.6.0 (Released)

- New `UserManager` for full CRUD operations on GeoServer users via the REST security API
- Added `GeoServerClient::publishFeatureLayer()` for simplified feature + layer setup
- Added `GeoServerClient::isAvailable()` to check server reachability
- Improved internal exception handling with consistent `GeoServerException` usage
- Additional live tests and extended PHPUnit coverage for new functionality

---

## 🚧 v1.7.0 (Planned)

- Full CRUD support for GeoServer Roles
- Role listing, creation, updating, and deletion via REST security API

---
## 🚧 v1.8.0 (Planned)

- Full CRUD support for GeoServer Groups
- Group assignment to users and roles
- Group listing, creation, updating, and deletion

---

## 🧪 v1.10.0 – Experimental

### WFS-T Support
- Create `GeoServerWFSTransactionClient` (Insert/Update/Delete)
- Support for GML and GeoJSON transactions
- Auto-conversion of geometry types (Point, LineString, Polygon)

### Advanced Layer Control
- Get default style for a layer
- Enable/disable layer visibility
- Update layer metadata (title, abstract, keywords)

---

## 🔒 v2.0.0 – Major Release

### Breaking Changes
- Switch from associative arrays to typed DTOs
- Require PHP 8.3+ and strict types
- Rename all `*_Exists()` methods to `exists*()` for consistency
- Throw exceptions instead of returning false for unexpected states

---

## 💡 Contributions

We welcome feature requests, bug reports, and pull requests.
👉 [https://github.com/hfelge/geoserver-client](https://github.com/hfelge/geoserver-client)
