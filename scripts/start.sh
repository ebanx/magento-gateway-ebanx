#!/bin/bash

echo "Shutting down containers..."
docker-compose down

echo "Removing .data folder..."
rm -rf $(pwd)/.data

echo "Starting containers..."
docker-compose up -d

echo "Installing magento..."
sleep 20
docker exec -it magento install-magento

# echo "Creating admin user..."
# docker cp $(pwd)/scripts/create_user.sql magento_db:/create_user.sql
# docker exec -it magento_db chmod +x /create_user.sql
# docker exec -it magento_db /bin/bash -c 'mysql -uroot -proot < /create_user.sql'

echo "Everything is fine... Magento is live in http://localhost"