<?php

namespace MageSuite\DeferJs\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_IGNORED_STRINGS_PATH = 'deferjs/general/ignored_strings';
    const IS_ACTIVE_PATH = 'deferjs/general/active';
    const EXCLUDE_HOMEPAGE_PATH = 'deferjs/general/home_page';
    const EXCLUDED_CONTROLLERS_PATH = 'deferjs/general/controller';
    const EXCLUDED_PATHS_PATH = 'deferjs/general/path';

    const HOMEPAGE_CONTROLLER_PATH = 'cms_index_index';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * @return array
     */
    public function getIgnoredStrings()
    {
        $ignoredStrings = $this->scopeConfig->getValue(self::XML_IGNORED_STRINGS_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($ignoredStrings == null) {
            return [];
        }

        $unserializedIgnoredStrings = $this->serializer->unserialize($ignoredStrings);

        if (empty($unserializedIgnoredStrings)) {
            return [];
        }

        $ignoredStrings = [];

        foreach ($unserializedIgnoredStrings as $string) {
            $ignoredStrings[] = $string['defer'];
        }

        return $ignoredStrings;
    }


    public function isEnabled($request)
    {
        $active = $this->scopeConfig->getValue(self::IS_ACTIVE_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($active != 1) {
            return false;
        }

        $active = $this->scopeConfig->getValue(self::EXCLUDE_HOMEPAGE_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($active == 1 && $request->getFullActionName() == self::HOMEPAGE_CONTROLLER_PATH) {
            return false;
        }

        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($this->regexMatchSimple($this->scopeConfig->getValue(self::EXCLUDED_CONTROLLERS_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), "{$module}_{$controller}_{$action}", 1)) {
            return false;
        }

        if ($this->regexMatchSimple($this->scopeConfig->getValue(self::EXCLUDED_PATHS_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE), $request->getRequestUri(), 2)) {
            return false;
        }

        return true;
    }

    public function regexMatchSimple($regex, $matchTerm, $type)
    {
        if (!$regex) {
            return false;
        }

        $rules = @unserialize($regex);

        if (empty($rules)) {
            return false;
        }

        foreach ($rules as $rule) {
            $regex = trim($rule['defer'], '#');
            if ($regex == '') {
                continue;
            }
            if ($type == 1) {
                $regexs = explode('_', $regex);
                switch (count($regexs)) {
                    case 1:
                        $regex = $regex . '_index_index';
                        break;
                    case 2:
                        $regex = $regex . '_index';
                        break;
                    default:
                        break;
                }
            }

            $regexp = '#' . $regex . '#';
            if (@preg_match($regexp, $matchTerm)) {
                return true;
            }
        }

        return false;
    }

}
