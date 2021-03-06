<?php

namespace InnStudio\Prober\PhpExtensionInfo;

use InnStudio\Prober\Events\Api as Events;
use InnStudio\Prober\Helper\Api as Helper;
use InnStudio\Prober\I18n\Api as I18n;

class PhpExtensionInfo
{
    private $ID = 'phpExtensionInfo';

    public function __construct()
    {
        Events::patch('mods', array($this, 'filter'), 400);
    }

    public function filter($mods)
    {
        $mods[$this->ID] = array(
        'title'     => I18n::_('PHP extensions'),
        'tinyTitle' => I18n::_('Ext'),
        'display'   => array($this, 'display'),
        );

        return $mods;
    }

    public function display()
    {
        ?>
<div class="row">
    <?php echo $this->getContent(); ?>
</div>
        <?php
    }

    private function getContent()
    {
        $items = array(
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Memcache'),
                'content' => Helper::getIni(0, \extension_loaded('memcache') && \class_exists('\Memcache')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Memcached'),
                'content' => Helper::getIni(0, \extension_loaded('memcached') && \class_exists('\Memcached')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Redis'),
                'content' => Helper::getIni(0, \extension_loaded('redis') && \class_exists('\Redis')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s extension'), 'Opcache'),
                'content' => Helper::getIni(0, \function_exists('\opcache_get_configuration')),
            ),
            array(
                'label'   => \sprintf(I18n::_('%s enabled'), 'Opcache'),
                'content' => Helper::getIni(0, $this->isOpcEnabled()),
            ),
            array(
                'label'   => I18n::_('Zend Optimizer'),
                'content' => Helper::getIni(0, \function_exists('zend_optimizer_version')),
            ),
        );

        $content = '';

        foreach ($items as $item) {
            $title = isset($item['title']) ? "title=\"{$item['title']}\"" : '';
            $col   = isset($item['col']) ? $item['col'] : '1-3';
            $id    = isset($item['id']) ? "id=\"{$item['id']}\"" : '';
            $content .= <<<EOT
<div class="poi-g-lg-{$col}">
    <div class="form-group">
        <div class="group-label" {$title}>{$item['label']}</div>
        <div class="group-content" {$title} {$id}>{$item['content']}</div>
    </div> 
</div>
EOT;
        }

        return $content;
    }

    private function isOpcEnabled()
    {
        $isOpcEnabled = \function_exists('\opcache_get_configuration');

        if ($isOpcEnabled) {
            $isOpcEnabled = \opcache_get_configuration();
            $isOpcEnabled = isset($isOpcEnabled['directives']['opcache.enable']) && $isOpcEnabled['directives']['opcache.enable'] === true;
        }

        return $isOpcEnabled;
    }
}
