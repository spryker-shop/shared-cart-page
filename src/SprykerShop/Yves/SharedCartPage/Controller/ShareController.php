<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\SharedCartPage\Controller;

use SprykerShop\Yves\CartPage\Plugin\Provider\CartControllerProvider;
use SprykerShop\Yves\ShopApplication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \SprykerShop\Yves\SharedCartPage\SharedCartPageFactory getFactory()
 */
class ShareController extends AbstractController
{
    public const KEY_GLOSSARY_SHARED_CART_PAGE_SHARE_SUCCESS = 'shared_cart_page.share.success';
    public const URL_REDIRECT_MULTI_CART_PAGE = 'multi-cart';

    /**
     * @param int $idQuote
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Spryker\Yves\Kernel\View\View|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(int $idQuote, Request $request)
    {
        $response = $this->executeIndexAction($idQuote, $request);

        if (!is_array($response)) {
            return $response;
        }

        return $this->view($response, [], '@SharedCartPage/views/cart-share/cart-share.twig');
    }

    /**
     * @param int $idQuote
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function executeIndexAction(int $idQuote, Request $request)
    {
        $quoteTransfer = $this->getFactory()
            ->getMultiCartClient()
            ->findQuoteById($idQuote);

        $customerTransfer = $this->getFactory()
            ->getCustomerClient()
            ->getCustomer();

        if ($quoteTransfer === null || $customerTransfer === null) {
            return $this->redirectResponseInternal(CartControllerProvider::ROUTE_CART);
        }

        $sharedCartForm = $this->getFactory()
            ->getShareCartForm($idQuote, $quoteTransfer->getShareDetails(), $customerTransfer)
            ->handleRequest($request);

        if ($sharedCartForm->isSubmitted() && $sharedCartForm->isValid()) {
            $shareCartRequestTransfer = $sharedCartForm->getData();
            $quoteResponseTransfer = $this->getFactory()->getSharedCartClient()
                ->updateQuotePermissions($shareCartRequestTransfer);
            if ($quoteResponseTransfer->getIsSuccessful()) {
                $this->addSuccessMessage(static::KEY_GLOSSARY_SHARED_CART_PAGE_SHARE_SUCCESS);

                return $this->redirectResponseInternal(static::URL_REDIRECT_MULTI_CART_PAGE);
            }
        }

        $companyUserNames = $this->getFactory()
            ->createShareCartFormDataProvider()
            ->getCompanyUserNames($customerTransfer);

        return [
            'cart' => $quoteTransfer,
            'sharedCartForm' => $sharedCartForm->createView(),
            'companyUserNames' => $companyUserNames,
        ];
    }
}
