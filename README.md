 Transactional email microservice

# Transactional email microservice

This application send transactional email as a stand-alone microservice. It sends email through two method:

    -  (JSON) API and Command Line Interface

The service uses externlal email services, it uses multiple services, using one as defaul and others as fallback.

The two service used in this app are:

* MailJet - Requires two keys, entered in `.env` file  `MJ_APIKEY_PUBLIC` and `MJ_APIKEY_PRIVATE`
* SendGrid - Requires a single key entered in the `.env `. `SENDGRID_API_KEY`

The default email service is stored in the env file as

More can be added as fallback in the future. Refer to [How to add more mail services as fallback](#adding-fallback-email-services)

This application has a single endpoint, which is:

- Post request to send an email: ``[base_url]/api/v1/send-email``

**Note: The app has the flexibility to accept more than one fallback service.**

## Architechture of the microservice

The microservice follows standard coding methods. Concerns were separated properly and accordingly.

The endpoint accepts a json request payload which calls the `index` function of the `EmailController` class.

The paylooad is validated through a request class `SendEmailRequest`. This class takes care of the request validation and returns descriptive error messages accordingly. An action class ` SendEmailAction` is injected into the  `index` function to perform the action of dispatching the job.

The action class saves the request data in `sent_emails` table and dispatches the job class `SendEmailJob`.

The job class uses the default service to send the mail and uses the fallbacks randomly if any failure occurs. The values are saved in the `mail.php` file

```php
    /*
     * Default email service provider
     */
    'service' => "App\Interfaces\EmailInterfaces\MailJetService",
    /*
     * Other fallback email service providers
     */
    'fallbacks' => [
        'App\Interfaces\EmailInterfaces\SendGridService',
        'App\Interfaces\EmailInterfaces\MailJetService',
    ],
```

The full class path of the mail services are entered.

The Email interface defines the methods which all service class must implement.

Sending a mail through the CLI reuses the `SendEmailAction` class after the inputs anad arguments must have been validated.

#### Logging

An `helper.php` file was created which is recognised globally through the application. It houses the functions used for logging details through the app. The functions are:

1. `log_activity()`
2. `log_error()`

These functions logs to a separate log file email.log

## Installation Instructions without Docker

- Run following commands:

```shell
git clone https://https://github.com/ghost-lolade/transactional-email-service.git
composer install
cp .env.example .env
npm install
php artisan migrate
```

## Installation Instructions with Docker

Run the following command:

```shell
git clone https://https://github.com/ghost-lolade/transactional-email-service.git
docker-compose up --build
```

As a final step, visit http://your_server_ip in the browser

**Note:** Create a user for MySQL

## Adding fallback email services

Adding a fallback service can be done in the following steps:

1. `composer requires --[service-package]`
2. Create a service file in `app/Interfaces/EmailInterfaces/`
3. Make sure the service class implements the `app/Interfaces/EmailInterfaces/Email` interface
4. Enter the full path of the service created in the `fallbacks` array in the `mail.php` file like so:
   ```php
   /*
        * Other fallback email service providers
        */
       'fallbacks' => [
           'App\Interfaces\EmailInterfaces\SendGridService',
           'App\Interfaces\EmailInterfaces\MailJetService',
       ],
   ```
5. Override the following methods:
   1. `send()` - This function does the sending of the mail as required by each external service
   2. `updateSentEmailTable()` - This method update the `service` column of the `sent_emails` table. So we can have a record of thee seervcie that was used to ssend what
   3. `logActivity()` - This logs actions immediately before and after the mail is sent.
   4. `logError()` - This method logs error in the catch block if there is an exception.

## API endpoint

**endpoint:** ``[base_url]/api/v1/send-email``

**Headers:** ``Content-Type => application/json``

**Request Type:** ``Post``

**Request Body:**

```json
{
	"to": "wahlolade@gmail.com",
    	"subject": "Test",
    	"message":
		{
           		"text": "Lorem Ipsum",
        		"html": "<htm></html>",
	    		"markdown": ""
            	}
}
```

**Response**:

```json
{
    "status": true,
    "message": "This email has been queued successfully",
    "data": {
        "to": "test@gmail.com",
        "subject": "Test",
        "service": null,
        "html": "<html>",
        "text": "Lorem ipsum",
        "markdown": "##This is a markdown "
    }
}
```

## Send an email through CLI

```shell
docker exec -it transactional-email-service-php-1 php artisan send:mail
```

## Tests

```shell
docker exec <container-name> php artisan test --filter SendEmailApiTest
docker exec <container-name> php artisan test --filter SendEmailCliTest
```

## Other things that could be added

* Make Redis handle the queue
* Create an API gateway  for proper microservice communication
