# Changelog

All notable changes to this project will be documented in this file.

## [2.1.0] - 2025-10-17
### Added
- Database seeding and `DummyDataSeeder` with 350+ realistic records for development
- Three super-admin users seeded: daniel@quty.co.id, idol@quty.co.id, ridwan@quty.co.id
- New `docs/` with Admin guide, API summary, and Deployment guide

### Fixed
- Multiple Blade view null-reference errors in `resources/views/assets/index.blade.php`
- Menu permission creation and assignment scripts; super-admin role now has full permissions
- Routes: added `/assets/{asset}/history` and `/assets/{asset}/move` aliases and fixed view route names
- Fixed warranty accessor usage in asset history view (`warranty_status` property)

### Notes
- After permission changes, users must logout and login again to refresh session permissions.

## [2.0.0] - 2024-xx-xx
- Initial release notes (legacy)
