# GeoServerClient Roadmap

A development plan for future versions of the GeoServerClient PHP library.


## 🚧 v1.9.0 (Planned)

- Full support for GeoServer Layer Groups
  - Create, update, list, and delete layer groups
  - Support for named and anonymous layer groups
  - Configure mode (SINGLE, NAMED, CONTAINER) and styles
- Internal handling via new `LayerGroupManager`
- Test coverage for group publishing and validation

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
