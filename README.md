# EBANX Payment Gateway for Magento 1.0.0

This plugin enables you to integrate your Magento 1.x store with the EBANX payment gateway.

## Getting Started with Docker

**To contribute to this repository you may use Docker.**

First run this command **once**:
```
chmod +x $(pwd)/scripts/start.sh && $(pwd)/scripts/start.sh
```

This will install and run the project with Docker on port 80.
If you want to change that port you may use an environment variable or the `.env` file. Just run `cp .env.example .env` and change the ports the way you want it.

After that, you can use the command to start:
```
docker-compose up
```

To access the project, go to http://localhost.

The admin can be acessed by http://localhost/admin using the credentials `ebanx` usernamae and `ebanx123` password.

#### Issues

If you have getting trouble to set up Docker, make sure you've changed the external port docker uses by following the above steps.

## Tasks

- [X] Benjamin Integration
- [X] Settings Page
- [ ] Refunds
- [ ] Payment by link
- [X] Checkout Manager and Compliance Fields
- [X] Debug Log
- [X] Sandbox mode
- [ ] Save credit card data
- [ ] One Click Payment
- [X] Instalments
- [X] Minimum Instalment Values
- [X] Interest Rates
- [X] Days to Expiration
- [X] Checkout for CPF and CNPJ
- [X] Auto-capture payments
- [X] Payment Methods
  - [X] Brazil
    - [X] Boleto
    - [X] Credit Card
    - [X] TEF
    - [X] EBANX Wallet
  - [X] Mexico
    - [X] OXXO
    - [X] Debit Card
    - [X] Credit Card
  - [X] Peru
    - [X] Pago Efectivo
    - [X] SafetyPay
  - [X] Chile
    - [X] Sencillito
    - [X] ServiPag
  - [X] Colombia
    - [X] Baloto
    - [X] PSE
