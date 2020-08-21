<?php

namespace MageSuite\DeferJs\Observer;


class ScriptDefer implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \MageSuite\DeferJs\Helper\Data
     */
    protected $helper;

    /**
     * @var \MageSuite\DeferJs\Service\ScriptDefer
     */
    protected $scriptDefer;

    public function __construct(
        \MageSuite\DeferJs\Helper\Data $helper
    )
    {
        $this->helper = $helper;
        $this->scriptDefer = new \MageSuite\DeferJs\Service\ScriptDefer();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getData('request');

        if (!$this->helper->isEnabled($request)) {
            return;
        }

        $response = $observer->getEvent()->getData('response');

        if (!$response) {
            return;
        }

        $html = $response->getBody();

        if ($html == '') {
            return;
        }

        $html = $this->scriptDefer->execute($html, $this->helper->getIgnoredStrings());

        $response->setBody($html);
    }
}
