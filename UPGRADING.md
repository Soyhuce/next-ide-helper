# Upgrading

## From v1 to v2

Many internal changes were made but there should be easy upgrade should be easy.

- Configuration file changed, you need to fetch new version : ` php artisan vendor:publish --tag=next-ide-helper-config --force`
- Delete previously generated files before running next-ide-helper commands
