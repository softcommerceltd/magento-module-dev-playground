<?php
/**
 * Copyright © Soft Commerce Ltd, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magency\DevPlayground\Plugin;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;
use Psr\Log\LoggerInterface;

/**
 * Class Country
 * @package SoftCommerce\Sales\Ui\Component\Listing\Column
 */
class ListAllDispatchEvents
{
    public function beforeDispatch($subject, $eventName, array $data = [])
    {
        return;
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/events.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->debug(print_r([
            '$eventName' => $eventName
        ], true), []);
    }
}
