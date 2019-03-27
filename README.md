### Install
This will install Carvago Coding Challenge into your computer and completely emulate dev environment.
##### Requirements
- Docker
- Docker Compose
- Git

There is no need for any web server, php, mysql, composer, yarn or npm to be installed on your machine.

##### 1. Initiate git repository in your directory and pull actual code from remote repository:
```bash

git init
git remote add origin https://github.com/petrzivny/coding-challenge.git
git pull origin master

```

##### 2. Build docker images and create containers (and networks and volumes) to simulate develop environment:
```
docker-compose build && docker-compose up -d
```
It will take about 15 min to build proper php image for a first time. Every other build will be matter of seconds.
Php dockerfile is intentionally structured and repeats some installations. The reason for this is to easy add or remove libraries and extensions.

##### 3. Login into docker container as root, install all dependencies:
```
docker exec -ituroot carvago_php sh -l
composer install
```

##### 4. Create database from migration
```
docker exec -ituroot carvago_php sh -l      // if you are not loged already
php bin/console doctrine:migration:diff
php bin/console doctrine:migration:migrate
```
For better experience add some tags in PhpMyAdmin at http://192.168.99.100:8080 (localhost:8080)

##### 5. Enjoy the app in your browser at either http://192.168.99.100 or localhost
To access admin section, enter /admin with 
- login: **admin** 
- password: **admin** 

##### To simulate production environment on your local machine:
  ```
  docker-compose -f docker-compose-prod.yml build && docker-compose -f docker-compose-prod.yml up -d
  ```
Do not remove dev containers, MySQL and PhpMyAdmin are needed.

### In real world
- I need to know complete background of project before first row of code to proper choose design (consider e.g. DDD, microservices, monolith, classic MVC etc…).
- I prefer to have only really needed dependencies, I would probably install Symfony from symfony/skeleton and not from symfony/websiteskeleton. Or at least delete unused dependencies.
- I have no idea about global frame of this app, so exceptions are not handled in controller. There would be probably some log and maybe warning page or flash messages in a real world.
- According to company philosophy, authorization may be demanded in controller level strictly or left to view.
- I would probably do custom security logic, but for this I need to know exact expectation and future plans. For almost all cases I recommend using Guard Authenticator.
- According to an expected traffic I would consider to implement Second Level Cache for Doctrine.

### My philosophy
- I use PSR-12.
- I prefer thin controllers and fat models (The code here is so simple I omit BlogService).
- I try to stick to SOLID, DRY and I strongly believe in KISS.
- I prefer not to misuse inheritance.
- I design Repositories differently from official Symfony docs to be easily interchangeable. For the same reason I don’t use Paramconvertor.
- I use PHPDocs in a minimalistic version. Less is sometime more. I do add PHPDoc row only when needed. For example, there is no need to add `@param FormBuilderInterface $builder`. On the other hand `@param Tag[] $tags` has added information value.
- By default, I store and work with UTC time and convert it into user’s time zone in a view layer.
- I use DateTime only when really needed, in the rest cases I use DateTimeImmutable. The same is for other immutables.

### Comments to specification
- I know there is only date in specification, but I added time to twig to better demonstrate conversion from UTC to local time, just for this coding challenge.
- The two simple REST returns could be easily done without any other additional libraries, you can see this solution in SimpleRestApiController. But since there is “at least” in the specification and no other info (e.g. performance) I choose to implement FOSRest bundle also as a demonstration. But as I stated before I prefer add dependencies only when really needed.
- For page count is used the simplest solution (as a like it that way), the SQL. If the blog is expected to experience heavy traffic, I would use APCu cache for single instance and Redis for cloud/multiple instances.

