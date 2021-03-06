<?php namespace Orchestra\Asset\TestCase;

use Mockery as m;
use Orchestra\Asset\Dispatcher;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Teardown the test environment.
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * Test Orchesta\Asset\Dispatcher::run() metho.
     *
     * @test
     */
    public function testRunMethod()
    {
        $files = m::mock('\Illuminate\Filesystem\Filesystem');
        $html = m::mock('\Illuminate\Html\HtmlBuilder');
        $resolver = m::mock('\Orchestra\Asset\DependencyResolver');
        $path = '/var/public';

        $script = array(
            'foo' => array(
                'source'       => 'foo.js',
                'dependencies' => array(),
                'attributes'   => array(),
            ),
            'foobar' => null,
        );

        $assets = array(
            'script' => $script,
            'style'  => array(),
        );

        $files->shouldReceive('lastModified')->once()->andReturn('');
        $html->shouldReceive('script')->twice()->with('foo.js', m::any())->andReturn('foo');
        $resolver->shouldReceive('arrange')->twice()->with($script)->andReturn($script);

        $stub = new Dispatcher($files, $html, $resolver, $path);

        $stub->addVersioning();

        $this->assertEquals('foo', $stub->run('script', $assets));
        $this->assertEquals('', $stub->run('style', $assets));

        $stub->removeVersioning();

        $this->assertEquals('foo', $stub->run('script', $assets));
    }
}
