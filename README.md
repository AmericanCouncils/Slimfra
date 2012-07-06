# Slimfra #

Slimfra is a thin extension of the Silex microframework for building small(ish) projects.  It assumes a default project structure to help keep the project organized as it grows.  It provides a configurable base application, a simple base controller, and default configuration for commonly used services.

For documentation on the excellent Silex framework, and examples of how to use it (all of which applies to Slimfra), refer to the [Silex documentation](http://silex.sensiolabs.org/).

Slimfra was built for personal use and rapid app development at work in order to easily convert some simple legacy projects into an easier to manage structure.  Why is it named Slimfra?  Because.

If anyone wishes to contribute, please do, but we recommend contributing to Silex for things that would be of benefit to the broader community.

## Installation ##

There are several ways you can get started with a new Slimfra project.  Here they're listed from simplest to most complex, pick whichever suits you best!

### Online installer ###
	
The easiest way to install a new project and start working is to use the online installer available at `documentation.americancouncils.org/slimfra`.  In order for it to work, though, you may need to enable php to execute `phar` files.  See [this]() for more on that.

	mkdir <project_name>
	cd <project_name>
	curl -s http://documentation.americancouncils.org/slimfra/installer | php

### Github clone ###

An almost as simple way would be to clone the empty-project repository from `www.github.com`.

	mkdir <project_name>
	cd <project_name>
	git clone http://github.com/evillemez/slimfra-new-project .

### Download Archive ###

The next easiest way to start a Slimfra project would be to download the `.zip` or `.tgz` archive of an empty project from `documentation.americancouncils.org/slimfra/downloads`.  Follow the instructions on that page for opening.

### Composer ###

Or, you can manually start a new project and include Slimfra as a dependency.  Here's what you should do:

	mkdir <project_name>
	cd <project_name>
	touch composer.json
	
Now, in `composer.json`, copy/paste the next few lines of JSON:

	{
		"requires": {
			"ac/slimfra": "master",
		}
	}
	
Now use `composer.phar` to have it download Slimfra + its dependencies.  You'll see that this automatically creates a `vendor/` directory to store all of that code.

	//if you don't have composer.phar anywhere on your system, you can get it by running the following command:
	curl -s http://getcomposer.org/installer | php
	
	//now run composer to have it install dependencies
	php composer.phar install
	
	//now run the slimfra command to initialize an empty project structure
	php bin/slimfra init
	
You should now have a full project ready to use, complete with a `README.md`, and directories for `app/`, `templates/`, etc.  Read through the `README.md` for examples of how to get started with your new project!

## Running Tests ##

To run the tests you need `phpunit` installed, and must have dependencies installed.  You can install the dependencies by running `composer`.  Once you have those, you can execute the tests included with Slimfra by simply running the `phpunit` command from this directory.
