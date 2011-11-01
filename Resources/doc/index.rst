DirectBundle
============

DirectBundle is an implementation of ExtDirect specification to Symfony2
framework.

Installing
----------

The best way to install DirectBundle into your project is add it as a git submodule.
To do it, in the terminal, go to your main  Symfony2 application directory
(e.g. /home/htdocs/symfony-sandbox or c:\\wamp\\www\\symfony-sandbox) and run:

::

    # add DirectBundle as a git submodule into your project
    $ git submodule add git://github.com/oaugustus/DirectBundle.git src/Neton/DirectBundle

Register the Neton namespace into your autoloader
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    // app/autoload.php
    $loader->registerNamespaces(array(
        // ...,
        'Neton' => __DIR__.'/../src',
        // ...,
    );

Register DirectBundle into your application kernel
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            // ...,
            new Neton\DirectBundle\DirectBundle(),
            // ...,
        );

        //..
        return $bundles;
    }

Register DirectBundle route into your route config
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    // app/config/routing.yml
    # ... your other routes here
    direct:
        resource: "@DirectBundle/Resources/config/routing.yml"


How to use
----------

Add the ExtDirect API into your page
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you is using Twig engine, only add the follow line in your views page at the
script section:

::

    <script type="text/javascript" src="{{ url('api')}}"></script>

Or if you are not using a template engine:

::

    <script type="text/javascript" src="http://localhost/symfony-sandbox/web/app.php/api.js"></script>

Expose your controller methods to ExtDirect Api
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    // ...
    namespace Neton\HelloBundle\Controller;

    // these imports are only needed if you are using symfony-standard
    // ( because of doctrine's annotation-scanner )
    use Neton\DirectBundle\Api\Remote;
    use Neton\DirectBundle\Api\Form;

    class TestController extends Controller
    {
       /*
        * Single exposed method.
        *
        * @Remote    // this annotation expose the method to API
        * @param  array $params
        * @return string
        */
        public function indexAction($params)
        {
            return 'Hello '.$params['name'];
        }

        /*
         * An action to handle forms.
         *
         * @Remote   // this annotation expose the method to API
         * @Form     // this annotation expose the method to API with formHandler option
         * @param array $params Form submited values
         * @param array $files  Uploaded files like $_FILES
         */
        public function testFormAction($params, $files)
        {
            // your proccessing
            return true;
        }
    }

Call the exposed methods from JavaScript
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    // Hello is the Bundle name without 'Bundle'
    // Test is the Controller name without 'Controller'
    // index is the method name without 'Action'
    Actions.Hello_Test.index({name: 'Otavio'}, function(r){
       alert(r);
    });

Finished
~~~~~~~~

Well, this all to DirectBundle work. Suggestions, bug reports and observations
are wellcome.