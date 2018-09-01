<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\SharedCartPage;

use ArrayObject;
use Generated\Shared\Transfer\CustomerTransfer;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Yves\Kernel\AbstractFactory;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCompanyUserClientInterface;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCustomerClientInterface;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToMultiCartClientInterface;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToSharedCartClientInterface;
use SprykerShop\Yves\SharedCartPage\Form\DataProvider\ShareCartFormDataProvider;
use SprykerShop\Yves\SharedCartPage\Form\DataProvider\ShareCartFormDataProviderInterface;
use SprykerShop\Yves\SharedCartPage\Form\ShareCartForm;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class SharedCartPageFactory extends AbstractFactory
{
    /**
     * @param int $idQuote
     * @param \ArrayObject|\Generated\Shared\Transfer\ShareDetailTransfer[]|null $quoteShareDetails
     * @param \Generated\Shared\Transfer\CustomerTransfer|null $customerTransfer
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function getShareCartForm(
        $idQuote,
        ?ArrayObject $quoteShareDetails = null,
        ?CustomerTransfer $customerTransfer = null
    ): FormInterface {
        $dataProvider = $this->createShareCartFormDataProvider();

        return $this->getFormFactory()->create(
            ShareCartForm::class,
            $dataProvider->getData($idQuote, $quoteShareDetails),
            $dataProvider->getOptions($customerTransfer)
        );
    }

    /**
     * @return \SprykerShop\Yves\SharedCartPage\Form\DataProvider\ShareCartFormDataProviderInterface
     */
    public function createShareCartFormDataProvider(): ShareCartFormDataProviderInterface
    {
        return new ShareCartFormDataProvider(
            $this->getCustomerClient(),
            $this->getCompanyUserClient(),
            $this->getSharedCartClient()
        );
    }

    /**
     * @return \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToMultiCartClientInterface
     */
    public function getMultiCartClient(): SharedCartPageToMultiCartClientInterface
    {
        return $this->getProvidedDependency(SharedCartPageDependencyProvider::CLIENT_MULTI_CART);
    }

    /**
     * @return \Symfony\Component\Form\FormFactoryInterface
     */
    public function getFormFactory(): FormFactoryInterface
    {
        return $this->getProvidedDependency(ApplicationConstants::FORM_FACTORY);
    }

    /**
     * @return \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCustomerClientInterface
     */
    public function getCustomerClient(): SharedCartPageToCustomerClientInterface
    {
        return $this->getProvidedDependency(SharedCartPageDependencyProvider::CLIENT_CUSTOMER);
    }

    /**
     * @return \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCompanyUserClientInterface
     */
    public function getCompanyUserClient(): SharedCartPageToCompanyUserClientInterface
    {
        return $this->getProvidedDependency(SharedCartPageDependencyProvider::CLIENT_COMPANY_USER);
    }

    /**
     * @return \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToSharedCartClientInterface
     */
    public function getSharedCartClient(): SharedCartPageToSharedCartClientInterface
    {
        return $this->getProvidedDependency(SharedCartPageDependencyProvider::CLIENT_SHARED_CART);
    }
}
