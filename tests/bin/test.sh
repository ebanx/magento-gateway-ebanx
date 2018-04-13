#!/bin/bash

[[ $TRAVIS_COMMIT_MESSAGE =~ ^(\[tests skip\]) ]] && echo "TESTS SKIP" && exit 0;

TEST_COUNTRY=mexico
TRAVIS_BUILD_DIR=$(pwd)

# cd $TRAVIS_BUILD_DIR
# chmod +x $(pwd)/scripts/start.sh && $(pwd)/scripts/start.sh
# composer install

cd $TRAVIS_BUILD_DIR/tests

npm install
node ./node_modules/.bin/cypress run --config videoRecording=false --project ./magento -s cypress/integration/$TEST_COUNTRY.js
