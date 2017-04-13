# vdm_scraper

VDM Scraper to success iAdvize backend test.
It contains :
* A database to store vdm posts
* The API to access to this posts
* The service to fill database

## Prerequisite
A great part of this project run inside containers, so you need to install docker and docker-compose
You need also composer to download packages

## Installation

#### Get the source code
Clone this project somewhere in your home directory

#### Install the dependancies
API project use PHP Composer to install dependancies, run `composer install` in the `api` folders.

If you're using docker-compose, skip the dependancies check using this command : `composer install --ignore-platform-reqs`.

#### Grab the images and build the containers
Inside `vdm-scraper`, run `docker-compose build` to download images and build the container that use a Dockerfile... time to drink a coffee !

## Run and Try !
#### Start / Stop 
Run `docker-compose up` inside the `vdm-scraper` folder to start the system.
`CTRL-C` to gracefully stop it.

#### Populate the databases
Postgres databases are empty, you need to perform some SQL queries to initialize them.
Ready to use SQL script is provided in the `vdm-scraper/sql` folder.
Then to get some datas from <http://viedemerde.fr/>, you have to go in `vdm-scraper` and run 
`docker-compose run api php download.php -n <post_limit>` By default post_limit = 200

#### Check if it works
Using your favorite browser go to  : <http://127.0.0.1:8082>
The api routes are:
* <http://127.0.0.2:8082/posts?author=baptiste&city=angers&country=france&from=2017-04-10&to=2017-04-15>
> This route return all posts optionnaly filtered by `author`, `city`, `country`, `from` and `to` dates
* <http://127.0.0.2:8082/post/{post_id}>
> This route return a unique post identify by his id

## Tests

Like the Starks said:
> Winter is comming.
>
> -- <cite>Eddard Stark</cite>

And tests are also coming!
