# MetaDeck

MetaDeck is a Symfony-based web application designed to provide a solid foundation for scalable and secure projects.  

## Features
- Symfony 7 framework
- MySQL/MariaDB database support
- Environment-based configuration
- Production-ready deployment setup

## Requirements
- PHP >= 8.3
- Composer
- MySQL or MariaDB
- Apache2 with `mod_rewrite` enabled
- OpenSSL for HTTPS support

## Installation
Clone the repository and install dependencies:
```bash
git clone https://github.com/StariaStudios/MetaDeck.git
cd MetaDeck
composer install
```

Copy and configure the environment variables:
```bash
cp .env .env.local
```

Edit `.env.local` with your database and application settings:
```
DATABASE_URL="mysql://DB_USER:DB_PASSWORD@DB_HOST:DB_PORT/DB_NAME?serverVersion=8.0.32&charset=utf8mb4"

APP_ENV=prod
APP_DEBUG=0
APP_SECRET=your_secret_key
```

Run database migrations:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

Start the local server if you are running it locally:
```bash
symfony server:start
```

## Deployment
On production servers, ensure you set:
```
APP_ENV=prod
APP_DEBUG=0
```

Set the correct file permissions:
```bash
sudo chown -R www-data:www-data /var/www/metadeck
sudo chmod -R 775 /var/www/metadeck/var
```

Configure Apache VirtualHost:
```apache
<VirtualHost *:80>
    ServerName yourUrl.com
    DocumentRoot /var/www/metadeck/public

    <Directory /var/www/metadeck/public>
        AllowOverride All
        Order Allow,Deny
        Allow from All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/metadeck_error.log
    CustomLog ${APACHE_LOG_DIR}/metadeck_access.log combined
</VirtualHost>
```

Enable the site and reload Apache:
```bash
sudo a2ensite metadeck.conf
sudo systemctl reload apache2
```

## Debugging
To debug locally, install the Symfony Debug Bundle:
```bash
composer require symfony/debug-bundle --dev
```

To check logs:
```bash
tail -f var/log/prod.log
```

## License
This project is licensed under the Apache 2.0 license.
