# EBANX Payment Gateway for Magento 1.9.x 
[![Build Status](https://img.shields.io/travis/ebanx/magento-gateway-ebanx/master.svg?style=for-the-badge)](https://travis-ci.com/ebanx/magento-gateway-ebanx)
[![Codacy grade](https://img.shields.io/codacy/grade/64dbf4b4e1b941b7bb38f35251af7575.svg?style=for-the-badge)](https://img.shields.io/codacy/grade/64dbf4b4e1b941b7bb38f35251af7575.svg)

This plugin enables you to integrate your Magento 1.x store with the EBANX payment gateway.

## Getting Started with Docker

**To contribute to this repository you may use Docker.**

First run this command **once**:
```
chmod +x $(pwd)/scripts/start.sh && $(pwd)/scripts/start.sh
```

After the installation, you need to install the composer dependencies. Run the command at root folder:
```
composer install
```

This will install and run the project with Docker on port 80.
If you want to change that port or any other configuration you may use an environment variable or the `.env` file. Just run `cp .env.example .env` and change the values the way you want it. The values meaning are descripted below:

```
MAGENTO_EXTERNAL_PORT: Magento port exposed by Docker. 80 is the default port.
MYSQL_EXTERNAL_PORT: MySQL port exposed by Docker. 3306 is the default port.
MAGENTO_DATABASE: Magento Databse name. "magento" is the default database.
MYSQL_PASSWORD: MySQL database root password. It will be used by setup the MySQL and to connect Magento to MySQL. "root" is the default root password.
ADMIN_USER: Magento default admin user. It is the first Magento used created in setup. "ebanx" is the default Magento user.
ADMIN_PASSWORD: Magento default admin password. It is the default password of ${ADMIN_USER} user. "ebanx123" is the default password.
ADMIN_FIRSTNAME: Magento default admin user Firstname. "Ebanx" is the default admin Firstname.
ADMIN_LASTNAME: Magento default admin user Lirstname. "Store" is the default admin Lastname.
ADMIN_EMAIL: Magento default admin user e-mail. "magento@ebanx.com" is the default admin e-mail.
```



After that, you can use the command to start:
```
docker-compose up
```

To access the project, go to http://localhost.

The admin can be acessed by http://localhost/admin using the credentials `ebanx` username and `ebanx123` password.

#### Issues

If you have getting trouble to set up Docker, make sure you've changed the external port docker uses by following the above steps.

## License

Copyright 2017 EBANX Payments

Licensed under the Apache License, Version 2.0 (the "License");
you may not use these files except in compliance with the License.
You may obtain a copy of the License at

   [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
