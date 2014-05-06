Movie tickets booking REST API
==============================

Installation
------------
Create an empty MySQL database and specify the connection settings:

    app/config/database.php

Then run:
```
composer install
```
- command will download dependences, launch migration database and generates the sample data.

Using
-----
Endpoints

    /api/film/list
	/api/film/<film>/schedule
	/api/cinema/list
	/api/cinema/<cinema>/schedule[?hall=<hall>]
	/api/session/<session>/places
	/api/tickets/buy?session=<session>&places=<places comma separated>
	/api/tickets/reject/<code>
	