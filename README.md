# EBANX Payment Gateway for Magento 1.9.x [![Build Status](https://api.travis-ci.org/ebanx/magento-gateway-ebanx.svg)](https://travis-ci.org/ebanx/magento-gateway-ebanx) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/64dbf4b4e1b941b7bb38f35251af7575)](https://www.codacy.com/app/pblwlln/magento-gateway-ebanx?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=ebanx/magento-gateway-ebanx&amp;utm_campaign=Badge_Grade)

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
If you want to change that port you may use an environment variable or the `.env` file. Just run `cp .env.example .env` and change the ports the way you want it.

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
