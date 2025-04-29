# GeoServerClient – Roadmap

This document outlines the planned features and major milestones for the GeoServerClient project.

---

## 🚀 Short-Term Goals (v1.4.0)

- **Style Management Enhancements**
    - Upload new SLD styles (`createStyle()`)
    - Assign styles to layers
    - Full PHPUnit 12 tests for styles
- **Improved Error Handling**
    - Introduce `GeoServerException` class for structured error responses
- **Client Improvements**
    - Add support for HTTP timeouts and future token-based authentication
- **Documentation**
    - Expand README and Wiki with detailed examples for style management

---

## 🌟 Mid-Term Goals (v1.5.0 / v1.6.0)

- **WFS-T Transaction Support**
    - Insert, update, delete features via WFS-T (OGC standards)
- **GeoJSON Upload**
    - Allow uploading GeoJSON files to create FeatureTypes and layers
- **Advanced Layer Configuration**
    - Manage default styles
    - Manage LayerGroups

---

## 🚀 Long-Term Goals (v2.0.0)

- **OpenAPI Integration**
    - Parse GeoServer OpenAPI (Swagger) specification dynamically
- **Plugin System**
    - Support for extended GeoServer APIs (e.g., Security, Monitoring modules)
- **Admin Client**
    - Lightweight web-based admin UI using this client

---

## 📅 Release Plan

| Version | Goal |
|:--------|:-----|
| v1.4.0 | Full Style Management + better error handling |
| v1.5.0 | WFS-T basic support |
| v1.6.0 | GeoJSON import |
| v2.0.0 | Plugins, OpenAPI parsing, Admin-Client (optional)

---

## 📜 Notes

- All versions follow [Semantic Versioning 2.0.0](https://semver.org/).
- The roadmap may be updated based on community feedback and project priorities.

---

*Let's build the best open-source GeoServer client together! 🚀*
