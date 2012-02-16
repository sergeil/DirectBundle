<?php

namespace Neton\DirectBundle\Tests\Router;

use Neton\DirectBundle\Router\Call;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class CallTest extends \PHPUnit_Framework_TestCase
{
    public function testCall()
    {
        $tc = $this;

        $request = array(
            'action' => 'FooAction',
            'method' => 'barMethod',
            'tid' => 1,
            'data' => array('baz-data'),
            'type' => 'rpc'
        );
        $call = new Call($request, 'single');
        $response = $call->getResponse(function($me) use($tc, $call) {
            $tc->assertSame($call, $me);

            return 'ololo-result';
        });

        foreach (array('action', 'method', 'tid', 'type') as $key) {
            $this->assertArrayHasKey($key, $response);
            $this->assertSame($request[$key], $response[$key]);
        }
        $this->assertSame('ololo-result', $response['result']);

        $response = $call->getResponse(function($me) use ($tc, $call) {
            $tc->assertSame($call, $me);

            throw new \Exception('ololo-exception');
        });
        $expectedResponse = array(
            'tid' => 1,
            'type' => 'exception',
            'status' => 'false',
            'message' => 'ololo-exception'
        );
        unset($response['where']);
        $this->assertEquals($expectedResponse, $response);

    }
}
