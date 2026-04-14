<?php

namespace Config;

use CodeIgniter\Config\View as BaseView;
use CodeIgniter\View\ViewDecoratorInterface;

/**
 * @phpstan-type parser_callable (callable(mixed): mixed)
 * @phpstan-type parser_callable_string (callable(mixed): mixed)&string
 */
class View extends BaseView
{
    public $saveData = true;

    public $filters = [];

    public $plugins = [];

    public array $decorators = [];

    public $viewsPaths = [
        APPPATH . 'Views' . DIRECTORY_SEPARATOR,
        ROOTPATH . 'application' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR,
    ];
    
    public string $rendererClass = 'App\View\View';
    
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
}
