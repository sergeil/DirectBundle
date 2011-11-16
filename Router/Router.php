<?php
namespace Neton\DirectBundle\Router;

use Symfony\Component\DependencyInjection\ContainerAware;

/**
 * Router is the ExtDirect Router class.
 *
 * It provide the ExtDirect Router mechanism.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Router
{
    /**
     * The ExtDirect Request object.
     * 
     * @var Neton\DirectBundle\Request
     */
    protected $request;
    
    /**
     * The ExtDirect Response object.
     * 
     * @var Neton\DirectBundle\Response
     */
    protected $response;
    
    /**
     * The application container.
     * 
     * @var Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    
    /**
     * Initialize the router object.
     * 
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->request = new Request($container->get('request'));
        $this->response = new Response($this->request->getCallType());
    }

    /**
     * Do the ExtDirect routing processing.
     *
     * @return JSON
     */
    public function route()
    {
        $batch = array();
        
        foreach ($this->request->getCalls() as $call) {
            $batch[] = $this->dispatch($call);
        }

        return $this->response->encode($batch);
    }

    /**
     * Dispatch a remote method call.
     * 
     * @param  Neton\DirectBundle\Router\Call $call
     * @return Mixed
     */
    private function dispatch($call)
    {
        $controller = $this->resolveController($call->getAction());
        $method = $call->getMethod()."Action";

        if (!is_callable(array($controller, $method))) {
            //todo: throw an execption method not callable
        }

        if ('form' == $this->request->getCallType()) {
            $result = $call->getResponse($controller->$method($call->getData(), $this->request->getFiles()));
        } else {
            $result = $call->getResponse($controller->$method($call->getData()));
        }

        return $result;
    }

    /**
     * Resolve the called controller from action.
     * 
     * @param  string $action
     * @return <type>
     */
    private function resolveController($action)
    {
        $exp = explode('_', $action);
        $bundleName = $exp[0]; // TODO

        $bundle = $this->container->get('kernel')->getBundle($bundleName.'Bundle');
        $class = $bundle->getNamespace().'\Controller\\'.implode('\\', array_slice($exp, 1)).'Controller';

        try {
            $controller = new $class();

            if ($controller instanceof ContainerAware) {
                $controller->setContainer($this->container);
            }

            return $controller;
        } catch(Exception $e) {
            // todo: handle exception
        }
    }
}
