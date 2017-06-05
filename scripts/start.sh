#!/bin/bash

source $(pwd)/.env

echo "Shutting down containers..."
docker-compose down

echo "Removing .data folder..."
rm -rf $(pwd)/.data

echo "Starting containers..."
docker-compose up -d
sleep 20

echo "Importing sample data..."
docker exec -it magento install-sampledata

echo "Installing magento..."
docker exec -it magento install-magento

# echo "Creating admin user..."
# docker cp $(pwd)/scripts/create_user.sql magento_db:/create_user.sql
# docker exec -it magento_db chmod +x /create_user.sql
# docker exec -it magento_db /bin/bash -c 'mysql -uroot -proot < /create_user.sql'

echo "Everything is fine... Magento is live in http://localhost:${MAGENTO_EXTERNAL_PORT:-80}"
