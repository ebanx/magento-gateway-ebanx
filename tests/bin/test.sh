#!/bin/bash

[[ $TRAVIS_COMMIT_MESSAGE =~ ^(\[tests skip\]) ]] && echo "TESTS SKIP" && exit 0;

cd $TRAVIS_BUILD_DIR
chmod +x $(pwd)/scripts/start.sh && $(pwd)/scripts/start.sh

cd $TRAVIS_BUILD_DIR/tests

npm ci
npx cypress run --config video=false --project ./magento -s magento/cypress/integration/$TEST_COUNTRY.js
