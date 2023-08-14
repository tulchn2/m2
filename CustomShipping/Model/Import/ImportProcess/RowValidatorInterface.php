<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ecommage\CustomShipping\Model\Import\ImportProcess;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
       const ERROR_INVALID_TITLE= 'Invalid Value TITLE';
       const ERROR_TITLE_IS_EMPTY = 'Empty TITLE';
    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}
