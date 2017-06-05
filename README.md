# EBANX Payment Gateway for Magento 1.x

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

- [ ] Benjamin Integration
- [ ] Settings Page
- [ ] Refunds
- [ ] Payment by link
- [ ] Checkout Manager and Compliance Fields
- [ ] Debug Log
- [ ] Sandbox mode
- [ ] Save credit card data
- [ ] One Click Payment
- [ ] Instalments
- [ ] Minimum Instalment Values
- [ ] Interest Rates
- [ ] Days to Expiration
- [ ] Checkout for CPF and CNPJ
- [ ] Auto-capture payments
- [ ] Payment Methods
  - [ ] Brazil
    - [ ] Boleto
    - [ ] Credit Card
    - [ ] TEF
    - [ ] EBANX Wallet
  - [ ] Mexico
    - [ ] OXXO
    - [ ] Debit Card
    - [ ] Credit Card
  - [ ] Peru
    - [ ] Pago Efectivo
    - [ ] SafetyPay
  - [ ] Chile
    - [ ] Sencillito
    - [ ] ServiPag
  - [ ] Colombia
    - [ ] Baloto
    - [ ] PSE
