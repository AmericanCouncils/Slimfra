# Slimfra #

Slimfra is a thin extension of the Silex microframework for building small(ish) projects.  It provides a configurable 
base application, a simple base controller, and command, which can access the app and its services and configuration.

For documentation on the excellent Silex framework, and examples of how to use it (all of which applies to Slimfra), 
refer to the [Silex documentation](http://silex.sensiolabs.org/).

Slimfra was built for personal use and rapid app development at work in order to easily convert some simple legacy projects 
into an easier to manage structure.  Why is it named Slimfra?  Because.

If anyone wishes to contribute, please do, but we recommend contributing to Silex for things that would be of benefit to 
the broader community.

## Installation & Use ##

Require `"ac/slimfra":"0.1.0"` in your `composer.json`.  Then run `composer update ac/slimfra`

Set your project up the same as you would any other Silex app.

The base Controller and Command provided both implement ArrayAccess, which lets you refer to the parent app via `$this`

For example:

```php
$service = $this['some.service.name'];
$config = $this['some.value'];
```

### Controllers ###

```php

class MyController extends Slimfra\Controller
{
	public function helloWorldAction()
	{
		$service = $this->app['some.service'];

		//...do whatever

		return 'Hello World!';
	}
}


$app = new Slimfra\Application();
$app->get('/hello-world', 'MyController::helloWorldAction');
$app->run();
```

### Commands ###

```php
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloWorldCommand extends Slimfra\Command
{
	protected function configure()
	{
		$this->setName('hello-world');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$service = $this->app['some.service'];

		//... do whatever

		$output->writeln('Hello World!');
	}
}

$app = new Slimfra\Console(new Slimfra\Application());

$app->add(new HelloWorldCommand());

$app->run();
```