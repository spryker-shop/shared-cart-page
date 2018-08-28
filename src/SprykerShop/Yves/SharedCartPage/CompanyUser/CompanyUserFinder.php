<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\SharedCartPage\CompanyUser;

use ArrayObject;
use Generated\Shared\Transfer\CompanyBusinessUnitTransfer;
use Generated\Shared\Transfer\CompanyUserCriteriaFilterTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\ShareDetailTransfer;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCompanyUserClientInterface;
use SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCustomerClientInterface;

class CompanyUserFinder implements CompanyUserFinderInterface
{
    /**
     * @var \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCompanyUserClientInterface
     */
    protected $companyUserClient;

    /**
     * @param \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCustomerClientInterface $customerClient
     * @param \SprykerShop\Yves\SharedCartPage\Dependency\Client\SharedCartPageToCompanyUserClientInterface $companyUserClient
     */
    public function __construct(
        SharedCartPageToCustomerClientInterface $customerClient,
        SharedCartPageToCompanyUserClientInterface $companyUserClient
    ) {
        $this->customerClient = $customerClient;
        $this->companyUserClient = $companyUserClient;
    }

    /**
     * @param int $idQuote
     *
     * @return \ArrayObject
     */
    public function getCompanyUsersShareDetails(int $idQuote): ArrayObject
    {
        $customerTransfer = $this->customerClient->getCustomer();

        $companyBusinessUnitTransfer = $this->getCompanyBusinessUnit();
        $businessUnitCompanyUserList = $this->getBusinessUnitCustomers($companyBusinessUnitTransfer);

        $customerListData = new ArrayObject();
        foreach ($businessUnitCompanyUserList as $companyUserTransfer) {
            if ($customerTransfer->getIdCustomer() === $companyUserTransfer->getFkCustomer()) {
                continue;
            }

            $customerListData[$companyUserTransfer->getIdCompanyUser()] = $this->createCompanyUserShareDetailTransfer(
                $companyUserTransfer,
                $idQuote
            );
        }

        return $customerListData;
    }

    /**
     * @return string[]
     */
    public function getCompanyUserNames(): array
    {
        $customerTransfer = $this->customerClient->getCustomer();

        $companyBusinessUnitTransfer = $this->getCompanyBusinessUnit();
        $businessUnitCompanyUserList = $this->getBusinessUnitCustomers($companyBusinessUnitTransfer);

        $companyUserNames = [];
        foreach ($businessUnitCompanyUserList as $companyUserTransfer) {
            if ($customerTransfer->getIdCustomer() === $companyUserTransfer->getFkCustomer()) {
                continue;
            }

            $companyUserCustomerTransfer = $companyUserTransfer->getCustomer();

            $companyUserCustomerTransfer->requireFirstName();
            $companyUserCustomerTransfer->requireLastName();

            $companyUserNames[$companyUserTransfer->getIdCompanyUser()] = $companyUserCustomerTransfer->getFirstName() . ' ' . $companyUserCustomerTransfer->getLastName();
        }

        return $companyUserNames;
    }

    /**
     * @return \Generated\Shared\Transfer\CompanyBusinessUnitTransfer
     */
    protected function getCompanyBusinessUnit(): CompanyBusinessUnitTransfer
    {
        $customerTransfer = $this->customerClient->getCustomer();
        $customerTransfer->requireCompanyUserTransfer();

        return $customerTransfer->getCompanyUserTransfer()
            ->getCompanyBusinessUnit();
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyBusinessUnitTransfer $companyBusinessUnitTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserTransfer[]
     */
    protected function getBusinessUnitCustomers(CompanyBusinessUnitTransfer $companyBusinessUnitTransfer): array
    {
        $companyUserCriteriaFilterTransfer = new CompanyUserCriteriaFilterTransfer();
        $companyUserCriteriaFilterTransfer->setIdCompany($companyBusinessUnitTransfer->getFkCompany());
        $companyUserCollectionTransfer = $this->companyUserClient->getCompanyUserCollection($companyUserCriteriaFilterTransfer);

        $businessUnitCompanyUserList = [];
        foreach ($companyUserCollectionTransfer->getCompanyUsers() as $companyUserTransfer) {
            if ($companyBusinessUnitTransfer->getIdCompanyBusinessUnit() === $companyUserTransfer->getFkCompanyBusinessUnit()) {
                $businessUnitCompanyUserList[] = $companyUserTransfer;
            }
        }

        return $businessUnitCompanyUserList;
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     * @param int $idQuote
     *
     * @return \Generated\Shared\Transfer\ShareDetailTransfer
     */
    protected function createCompanyUserShareDetailTransfer(
        CompanyUserTransfer $companyUserTransfer,
        int $idQuote
    ): ShareDetailTransfer {
        $companyUserTransfer->requireIdCompanyUser();
        $companyUserTransfer->requireCustomer();

        $customerTransfer = $companyUserTransfer->getCustomer();

        $customerTransfer->requireFirstName();
        $customerTransfer->requireLastName();

        return (new ShareDetailTransfer())
            ->setIdCompanyUser($companyUserTransfer->getIdCompanyUser())
            ->setIdQuoteCompanyUser($idQuote)
            ->setCustomerName($customerTransfer->getLastName() . ' ' . $customerTransfer->getFirstName());
    }
}