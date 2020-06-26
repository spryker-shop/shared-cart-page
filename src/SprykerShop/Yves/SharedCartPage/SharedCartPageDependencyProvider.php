<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\SharedCartPage;

use Spryker\Yves\Kernel\AbstractBundleDependencyProvider;
use Spryker\Yves\Kernel\Container;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCompanyUserClientBridge;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCustomerClientBridge;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToMultiCartClientBridge;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToQuoteClientBridge;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToSharedCartClientBridge;

class SharedCartPageDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_CUSTOMER = 'CLIENT_CUSTOMER';
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_COMPANY_USER = 'CLIENT_COMPANY_USER';
    public const CLIENT_SHARED_CART = 'CLIENT_SHARED_CART';
    public const CLIENT_MULTI_CART = 'CLIENT_MULTI_CART';

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = $this->addCustomerClient($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addCompanyUserClient($container);
        $container = $this->addSharedCartClient($container);
        $container = $this->addMultiCartClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCustomerClient(Container $container): Container
    {
        $container->set(static::CLIENT_CUSTOMER, function (Container $container) {
            return new SharedCartPageToCustomerClientBridge($container->getLocator()->customer()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container->set(static::CLIENT_QUOTE, function (Container $container) {
            return new SharedCartPageToQuoteClientBridge($container->getLocator()->quote()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addCompanyUserClient(Container $container): Container
    {
        $container->set(static::CLIENT_COMPANY_USER, function (Container $container) {
            return new SharedCartPageToCompanyUserClientBridge($container->getLocator()->companyUser()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addSharedCartClient(Container $container): Container
    {
        $container->set(static::CLIENT_SHARED_CART, function (Container $container) {
            return new SharedCartPageToSharedCartClientBridge($container->getLocator()->sharedCart()->client());
        });

        return $container;
    }

    /**
     * @param \Spryker\Yves\Kernel\Container $container
     *
     * @return \Spryker\Yves\Kernel\Container
     */
    protected function addMultiCartClient(Container $container): Container
    {
        $container->set(static::CLIENT_MULTI_CART, function (Container $container) {
            return new SharedCartPageToMultiCartClientBridge(
                $container->getLocator()->multiCart()->client()
            );
        });

        return $container;
    }
}
