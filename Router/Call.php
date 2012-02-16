<?php
namespace Neton\DirectBundle\Router;

/**
 * Call encapsule an single ExtDirect call.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Call
{
    /**
     * The ExtDirect action called. With reference to Bundle via underscore '_'.
     * 
     * @var string
     */
    protected $action;

    /**
     * The ExtDirect method called.
     * 
     * @var string
     */
    protected $method;

    /**
     * The ExtDirect request type.
     * 
     * @var string
     */
    protected $type;

    /**
     * The ExtDirect transaction id.
     * 
     * @var integer
     */
    protected $tid;

    /**
     * The ExtDirect call params.
     * 
     * @var array
     */
    protected $data;

    /**
     * The ExtDirect request type. Where values in ('form','single').
     * 
     * @var string
     */
    protected $callType;

    /**
     * The ExtDirect upload reference.
     * 
     * @var boolean
     */
    protected $upload;

    /**
     * Initialize an ExtDirect call.
     * 
     * @param array  $call
     * @param string $type
     */
    public function __construct($call, $type)
    {
        $this->callType = $type;
        
        if ('single' == $type) {
            $this->initializeFromSingle($call);
        } else {
            $this->initializeFromForm($call);
        }
    }

    /**
     * Get the requested action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get the requested method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the request method params.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * Return a result wrapper to ExtDirect method call.
     * 
     * @param  \Closure $cb
     * @return array
     */
    public function getResponse($cb)
    {
        $response = array(
            'tid' => $this->tid
        );

        try {
            $response = array_merge($response, array(
                'result' => $cb($this),
                'action' => $this->action,
                'method' => $this->method,
                'type' => 'rpc',
            ));
        } catch (\Exception $e) {
            $response = array_merge($response, array(
                'status' => 'false',
                'type' => 'exception',
                'message' => $e->getMessage(),
                'where' => $e->getFile().':'.$e->getLine()
            ));
        }

        return $response;
    }
    
    /**
     * Initialize the call properties from a single call.
     * 
     * @param array $call
     */
    private function initializeFromSingle($call)
    {
        $this->action = $call['action'];
        $this->method = $call['method'];
        $this->type   = $call['type'];
        $this->tid    = $call['tid'];
        $this->data   = (array)$call['data'][0];// sli: undefined offset if no params provided in procedure invocation
    }

    /**
     * Initialize the call properties from a form call.
     * 
     * @param array $call
     */
    private function initializeFromForm($call)
    {

        $this->action   = $call['extAction']; unset($call['extAction']);
        $this->method   = $call['extMethod']; unset($call['extMethod']);
        $this->type     = $call['extType']; unset($call['extType']);
        $this->tid      = $call['extTID']; unset($call['extTID']);
        $this->upload = $call['extUpload']; unset($call['extUpload']);

        foreach ($call as $key => $value) {
            $this->data[$key] = $value;
        }
    }
}
