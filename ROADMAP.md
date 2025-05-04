# GeoServerClient Roadmap

A development plan for future versions of the GeoServerClient PHP library.

---

## ✅ v1.5.0 (Released)

- Full CRUD support for Workspaces, Datastores, FeatureTypes, Layers, and Styles
- Upload SLD styles via `Slug:` header and proper `Content-Type`
- Assign styles to layers using REST
- Unified `GeoServerException` handling across all managers
- Live tests with GeoServer availability detection (DDEV-ready)
- 100% PHPUnit 12 test coverage

---

## 🚧 v1.6.0 (Planned)

### API Features
- Add `styleExists()` support for workspace-specific styles
- Add `getStyleSLD()` and `updateStyleSLD()` methods for raw SLD handling
- Add support for listing available formats via `/rest/formats`

### Testing & Tooling
- Optional `.env.test.local` config for GeoServer test target
- PHPUnit test coverage integration (CI-ready)

---

## 🧪 v1.7.0 – Experimental

### WFS-T Support
- Create `GeoServerWFSTransactionClient` (Insert/Update/Delete)
- Support for GML and GeoJSON transactions
- Auto-conversion of geometry types (Point, LineString, Polygon)

### Advanced Layer Control
- Get default style for a layer
- Enable/disable layer visibility
- Update layer metadata (title, abstract, keywords)

---

## 🗂 v1.8.0 – Internal Refactor

### Code Organization
- Move managers into namespace: `Hfelge\GeoServerClient\Manager`
- Add abstract `BaseManager` with shared logic
- Extract test helpers to `tests/support/TestCaseWithGeoServerClient.php`

### Documentation
- Create GitHub Pages or GitBook documentation site
- Add auto-generated PHPDocs

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
