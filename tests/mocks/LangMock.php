<?php

use Ovide\Libs\Mvc\Rest;

class LangMock extends Rest\Controller
{
    
    protected $_availableLanguages = ['es', 'ca'];
    
    public function get()
    {
        return [$this->_locale];
    }
    
    public function getOne()
    {
        $this->disallowLanguage('ca');
        $this->getBestLang();
        return $this->get();
    }
}
