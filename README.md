# Welcome to the "Make Magic" challenge repository!

This repository was created for Dextra's "Make Magic" challenge.

It's a small Character CRUD application, with some integration with PotterAPI.

## Development Environment

For your convenience, a Vagrant development environment is available under the `vagrant` directory, which can be used by running `vagrant up` on that directory.

By doing so, you should have an accessible environment up and running at the [192.168.9.34](http://192.168.9.34) IP address.

You can also play with `vagrant ssh` by navigating to the `~/app` directory, which maps to the `src` directory.

### Running Tests

To run all test cases, enter your Vagrant instance and go to the app directory, then run `phpunit --testdox` to run all tests.

You can also run HTTP tests manually by using the sample files under the `http-client` directory. They should allow you properly CRUD into your development environment by using the [VS Code REST Client](https://marketplace.visualstudio.com/items?itemName=humao.rest-client).

## Docker Environment

In case you want to run this application with Docker, you can execute the `docker-run.sh` shell script included with this repository, which will properly run all migrations and generate application keys for Laravel.

After doing so, the application should be up and running at your [port 80](http://127.0.0.1).

## Adding your PotterAPI key

For some endpoints to work properly, an PotterAPI key is required.

You can set this up on your Vagrant by going to the `~/app/.env` file and adding your key after `POTTERAPI_KEY=`

Alternatively, you can also set this up for your Docker environment, by editing the `docker-compose.yaml` file and looking for `POTTERAPI_KEY:`