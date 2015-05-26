<?php

namespace DC\Bundler\NGTemplate;

class NGTemplateTransformer implements \DC\Bundler\IMultiFileTransformer {
    /**
     * @var string
     */
    private $module;

    /**
     * @param $module string Which module should we put the templates in?
     */
    function __construct($module)
    {
        $this->module = $module;
    }

    /**
     * @inheritdoc
     */
    function getName()
    {
        return "ngtpl";
    }

    /**
     * @inheritdoc
     */
    function transform(\DC\Bundler\Content $content, $file = null)
    {
        throw new \Exception("Not supported");
    }

    /**
     * @param \DC\Bundler\Content[] $contents The content to optimize
     * @return \DC\Bundler\Content
     */
    function transformMultiple(array $contents)
    {
        $wrapperTemplate = <<<EOTEMPLATE
(function(angular){
    angular
        .module('%s')
        .run(['\$templateCache', function(\$templateCache) {%s
        }]);
})(angular);
EOTEMPLATE;
        $itemTemplate = <<<EOTEMPLATE
\r\n            \$templateCache.put('%s', '%s');
EOTEMPLATE;

        $items = [];
        foreach ($contents as $content) {
            $html = $content->getContent();
            $html = preg_replace('/\n/', '\\n', $html);
            $items[] = sprintf($itemTemplate, $content->getPath(), $html);
        }

        return new \DC\Bundler\Content(
            $this->getOutputContentType(),
            sprintf($wrapperTemplate, $this->module, implode('\n', $items)));
    }

    /**
     * @inheritdoc
     */
    function getOutputContentType()
    {
        return "application/javascript";
    }

    /**
     * @inheritdoc
     */
    function runInDebugMode()
    {
        return false;
    }

    public static function registerWithContainer(\DC\IoC\Container $container, $module) {
        $container
            ->register(function() use ($module) {
                return new NGTemplateTransformer($module);
            })
            ->to('\DC\Bundler\ITransformer');
    }
}