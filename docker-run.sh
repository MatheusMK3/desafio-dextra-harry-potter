#!/bin/bash

# Stops any previously running containers
docker-compose stop

# Starts the Docker environment using docker-compose and runs the application
docker-compose up -d

# Generates application key
docker-compose exec app php artisan key:generate

# Runs database migrations
docker-compose exec app php artisan migrate