<?php

namespace DC\Tests\Bundler\NGTemplateTransformer;

class NGTemplateTransformerTest extends \PHPUnit_Framework_TestCase {

    private static $Templates = [
        "a" => "<h1>A</h1>",
        "b" => "<h2>B</h2>\n<p>Foo</p>"
    ];

    public function testTransformMultiple_simpleTemplate_correctlyRendered()
    {
        $transformer = new \DC\Bundler\NGTemplate\NGTemplateTransformer("foo");
        $result = $transformer->transformMultiple(
            [
                new \DC\Bundler\Content("text/html", self::$Templates["a"], "a")
            ]);
        $this->assertEquals("application/javascript", $result->getContentType());
        $desired = <<<EOJS
(function(angular){
    angular
        .module('foo')
        .run(['\$templateCache', function(\$templateCache) {
            \$templateCache.put("\/a", "<h1>A<\/h1>");
        }]);
})(angular);
EOJS;
        $this->assertEquals(trim($desired), trim($result->getContent()));
    }

    public function testTransformMultiple_multilineTemplate_correctlyRendered()
    {
        $transformer = new \DC\Bundler\NGTemplate\NGTemplateTransformer("foo");
        $result = $transformer->transformMultiple(
            [
                new \DC\Bundler\Content("text/html", self::$Templates["b"], "b")
            ]);
        $this->assertEquals("application/javascript", $result->getContentType());
        $desired = <<<EOJS
(function(angular){
    angular
        .module('foo')
        .run(['\$templateCache', function(\$templateCache) {
            \$templateCache.put("\/b", "<h2>B<\/h2>\\n<p>Foo<\/p>");
        }]);
})(angular);
EOJS;
        $this->assertEquals($desired, $result->getContent());
    }

    public function testGetOutputContentType_javascript() {
        $transformer = new \DC\Bundler\NGTemplate\NGTemplateTransformer("foo");
        $this->assertEquals("application/javascript", $transformer->getOutputContentType());
    }

    public function testGetName() {
        $transformer = new \DC\Bundler\NGTemplate\NGTemplateTransformer("foo");
        $this->assertEquals("ngtpl", $transformer->getName());
    }

    /**
     * @expectedException \Exception
     */
    public function testTransform_throws() {
        $transformer = new \DC\Bundler\NGTemplate\NGTemplateTransformer("foo");
        $transformer->transform(new \DC\Bundler\Content("text/html", "foo"));
    }
}