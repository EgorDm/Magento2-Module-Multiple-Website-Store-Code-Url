<?php
/**
 * A Magento 2 module named Experius MultipleWebsiteStoreCodeUrl
 * Copyright (C) 2017 Experius
 *
 * This file is part of Experius MultipleWebsiteStoreCodeUrl.
 *
 * Experius MultipleWebsiteStoreCodeUrl is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace Experius\MultipleWebsiteStoreCodeUrl\Plugin\Store\App\Request;

use Experius\MultipleWebsiteStoreCodeUrl\Helper\Settings;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class PathInfoProcessor
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Experius\MultipleWebsiteStoreCodeUrl\Helper\Settings
     */
    private $settings;

    /**
     * PathInfoProcessor constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Experius\MultipleWebsiteStoreCodeUrl\Helper\Settings $settings
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Settings $settings
    ) {
        $this->storeManager = $storeManager;
        $this->settings = $settings;
    }

    public function aroundProcess(
        /** @noinspection PhpUnusedParameterInspection */
        \Magento\Store\App\Request\PathInfoProcessor $subject,
        callable $proceed,
        RequestInterface $request,
        $pathInfo
    ) {
        if (!$this->settings->shouldRemoveWebsiteCodeFromStoreUrl()) {
            return $proceed($request, $pathInfo);
        }
        $website = $this->storeManager->getWebsite();
        if (!$website) {
            return $proceed($request, $pathInfo);
        }
        $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
        $websiteCode = $website->getCode();
        $storeCode = "{$websiteCode}_{$pathParts[0]}";

        try {
            /** @var \Magento\Store\Api\Data\StoreInterface $store */
            $this->storeManager->getStore($storeCode);
        } catch (NoSuchEntityException $e) {
            return $proceed($request, $pathInfo);
        }

        $pathParts[0] = $storeCode;
        return $proceed($request, implode('/', $pathParts));
    }
}
