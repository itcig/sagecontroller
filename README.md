# Controller

WordPress package to enable a controller when using Timber/Twig with [Sage 9](https://roots.io/sage/). This also requires heavy modification of Sage 9 to use.

## Installation

#### Composer:

**Please note that Controller is no longer an mu-plugin and is now a Composer theme depedency.**

Browse into the Sage theme directory and run;

```shell
$ composer require itcig/sagecontroller:2.0.2
```

#### Requirements:

* [PHP](http://php.net/manual/en/install.php) >= 7.0

## Setup

By default, create folder `app/Controllers/` within your theme directory.

Alternatively, you can define a custom path using the filter below within your themes `resources/functions.php` file;
```php

add_filter('sober/controller/path', function () {
    return dirname(get_template_directory()) . '/app/Custom-folder';
});
```

The controller will autoload PHP files within the above path and its subdirectories.

## Usage

#### Creating a basic Controller:

* Controller files follow the same hierarchy as WordPress.
    <!--* You can view the controller hierarchy by using the Twig directive `@debug('hierarchy')`.-->
* Extend the Controller Class&mdash; it is recommended that the class name matches the filename.
* Create methods within the Controller Class;
    * Use `public function` to expose the returned values to the Twig views/s.
    * Use `public static function` to use the function within your Twig view/s.
    * Use `protected function` for internal controller methods as only public methods are exposed to the view. You can run them within `__construct`.
* Return a value from the public methods which will be passed onto the Twig view.
    * **Important:** The method name is converted to snake case and becomes the variable name in the Twig view.
    * **Important:** If the same method name is declared twice, the latest instance will override the previous.

#### Examples:

The following example will expose `$images` to `resources/views/single.twig`

**app/controllers/Single.php**

```php
<?php

namespace App;

use Cig\Sage\Controller\Controller;

class Single extends Controller
{
    /**
     * Return images from Advanced Custom Fields
     *
     * @return array
     */
    public function images()
    {
        return get_field('images');
    }
}
```

**resources/views/single.twig**

```php
{% if(images|length) %}
  <ul>
    {% for image in images %}
      <li><img src="{{ image.sizes.thumbnail }}" alt="{{ image.alt }}"></li>
    {% endfor %}
  </ul>
{% endif %}
```

#### Creating Components;

You can also create reusable components and include them in a view using PHP traits.

**app/controllers/partials/Images.php**

```php
<?php

namespace App;

trait Images
{
    public function images()
    {
        return get_field('images');
    }
}
```

You can now include the Images trait into any view to pass on variable $images;

**app/controllers/Single.php**

```php
<?php

namespace App;

use Cig\Sage\Controller\Controller;

class Single extends Controller
{
    use Images;
}
```

#### Using Static Methods;

You can use static methods as a pass-thru method that returns content from your controller.

This is useful if you are within the loop and want to return data for each post item individually by passing in a $post_id.

**app/controllers/Archive.php**

```php
<?php

namespace App;

use Cig\Sage\Controller\Controller;

class Archive extends Controller
{
    public static function callback_method($arg = null)
    {
        return my_callback($arg);
    }
}
```

**resources/views/archive.php**

```php
{% extends "base.twig" %}

{% block content %}
	{{ callback_method() }}
{% endblock %}
```

#### Inheriting the Tree/Heirarchy;

By default, each Controller overrides its template heirarchy depending on the specificity of the Controller (the same way WordPress templates work).

You can inherit the data from less specific Controllers in the heirarchy by implementing the Tree.

For example, the following `app/controllers/Single.php` example will inherit methods from `app/controllers/Singular.php`;

**app/controllers/Single.php**

```php
<?php

namespace App;

use Cig\Sage\Controller\Controller;
use Cig\Sage\Controller\Module\Tree;

class Single extends Controller implements Tree
{

}
```

If you prefer you can also do this;

```php
<?php

namespace App;

use Cig\Sage\Controller\Controller;

class Single extends Controller
{
    protected $tree = true;
}
```

You can override a `app/Controllers/Singular.php` method by declaring the same method name in `app/Controllers/Single.php`;

#### Creating Global Properties;

Methods created in `app/Controllers/App.php` will be inherited by all views and can not be disabled as `resources/views/layouts/app.php` extends all views.

**app/Controllers/App.php**

```php
<?php

namespace App;

use Cig\Sage\Controller\Controller;

class App extends Controller
{
    public function siteName()
    {
        return get_bloginfo('name');
    }
}
```

#### Disable Option;

```php
protected $active = false;
```

#### Twig Debugging;
Coming soon
## Updates

#### Composer:

* Change the composer.json version to ^2.0.2
* Check [CHANGELOG.md](CHANGELOG.md) for any breaking changes before updating.

```shell
$ composer update
```

#### WordPress:

Includes support for [github-updater](https://github.com/afragen/github-updater) to keep track on updates through the WordPress backend.
* Download [github-updater](https://github.com/afragen/github-updater)
* Clone [github-updater](https://github.com/afragen/github-updater) to your sites plugins/ folder
* Activate via WordPress

## Special Thanks

* Most of the leg work here was done by Daren Jacoby so show him some love
* For Controller updates and other WordPress dev, follow [@withjacoby](https://twitter.com/withjacoby)
