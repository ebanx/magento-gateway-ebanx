#!/bin/bash
#current_branch=$(git branch | grep \* | sed 's/\* //g')
php ./lib/Ebanx/vendor/squizlabs/php_codesniffer/bin/phpcs --extensions=php,phtml -v -n --parallel=20 .
