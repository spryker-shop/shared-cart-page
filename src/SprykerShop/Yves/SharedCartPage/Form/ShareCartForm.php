<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerShop\Yves\SharedCartPage\Form;

use Generated\Shared\Transfer\ShareCartRequestTransfer;
use Spryker\Yves\Kernel\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShareCartForm extends AbstractType
{
    public const FORM_NAME = 'shareCartForm';
    public const FIELD_ID_QUOTE = 'idQuote';
    /**
     * @deprecated
     */
    public const FIELD_COMPANY_USER_ID = 'idCompanyUser';
    /**
     * @deprecated
     */
    public const FIELD_QUOTE_PERMISSION_GROUP_ID = 'idQuotePermissionGroup';
    /**
     * @deprecated
     */
    public const OPTION_CUSTOMERS = 'OPTION_CUSTOMERS';
    public const FIELD_SHARE_DETAILS = 'shareDetails';
    public const OPTION_PERMISSION_GROUPS = 'OPTION_PERMISSION_GROUPS';

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return static::FORM_NAME;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(static::OPTION_PERMISSION_GROUPS);
        $resolver->setDefaults([
            'data_class' => ShareCartRequestTransfer::class,
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addQuoteIdField($builder)
            ->addShareDetailsField($builder, $options);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addQuoteIdField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_QUOTE, HiddenType::class, []);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addShareDetailsField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_SHARE_DETAILS, CollectionType::class, [
            'entry_type' => ShareCartCompanyUserShareEditForm::class,
            'entry_options' => [
                static::OPTION_PERMISSION_GROUPS => $options[static::OPTION_PERMISSION_GROUPS],
            ],
            'label' => 'shared_cart.form.select_permissions',
        ]);

        return $this;
    }
}
