#!/usr/bin/env bash

set -euo pipefail

# PHPMD 2.15/PDepend 2.16 emit PHP deprecation notices from vendor code.
# Keep the analyzer's result and errors visible while removing that noise.
error_reporting=$(php -r 'echo E_ALL & ~E_DEPRECATED;')
exec php -d "error_reporting=${error_reporting}" vendor/bin/phpmd "$@"
