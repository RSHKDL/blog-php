<?php
namespace Tests\Framework;

use Framework\Renderer;
use PHPUnit\Framework\TestCase;


class RendererTest extends TestCase
{

    private $renderer;

    public function setUp() {
        $this->renderer = new Renderer\PHPRenderer(__DIR__ . '/views');
    }


    public function testRenderTheRightPath() {
        $this->renderer->addPath('blog', __DIR__ . '/views');
        $content = $this->renderer->render('@blog/demo');
        $this->assertEquals('Hello world !', $content);
    }


    public function testRenderTheDefaultPath() {
        $content = $this->renderer->render('demo');
        $this->assertEquals('Hello world !', $content);
    }


    public function testRenderWithParams() {
        $content = $this->renderer->render('demoparams', ['name' => 'Julien']);
        $this->assertEquals('Hello Julien', $content);
    }


    public function testGlobalParams() {
        $this->renderer->addGlobal('name', 'Julien');
        $content = $this->renderer->render('demoparams');
        $this->assertEquals('Hello Julien', $content);
    }
}
