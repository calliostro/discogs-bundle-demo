Discogs Bundle Demo
===================

This is a simple demo of the [calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle)
in combination with [hwi/HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle).

You need PHP 7.3.x or PHP 8.x and Symfony 5.


Installation
------------

Make sure Composer is installed globally, as explained in the 
[installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Open a command console, enter your desired parent directory for the demo and execute:

```console
$ git clone https://github.com/calliostro/discogs-bundle-demo
```

Then enter the new directory and run the installation with composer:

```console
$ cd discogs-bundle-demo
$ composer install
```


Configuration
-------------

First, you must register the application at https://www.discogs.com/de/applications/edit to get the `consumer_key` and
`consumer_secret`. You can set the values as environment variables. Then they will be used by both bundles. See also
[calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle#configuration).


Usage
-----

Start the local web server of Symfony in the discogs-bundle-demo directory:

```console
$ symfony serve
```

Open your web browser. Refer to the output in the console for the URL to call.


Documentation
-------------

Further documentation can be found at the [Discogs API v2.0 Documentation](https://www.discogs.com/developers).
