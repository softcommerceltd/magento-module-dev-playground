<?php
/**
 * Created by PhpStorm.
 * User: theexten
 * Date: 2019-01-06
 * Time: 18:06
 */

declare(strict_types=1);

namespace Magency\DevPlayground\Controller\Adminhtml\Debugging;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use Magento\Sales\Api\OrderRepositoryInterface;


class Index extends Action
{
    /**
     * Index constructor.
     * @param Action\Context $context
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Action\Context $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $time_start = microtime(true);

        // $this->moduleListInterface();

        // Poison Pill
        // $this->poisonPillPutInterface();
        // $this->viewLogJsonDecoded();

        // Send order email
        // $this->sendOrderEmail();

        // $this->viewLogJsonDecoded();

        // DB Setup scripts
        // $this->dbSetup();

        // $this->generateProductUrlKeyByAttribute();
        // $this->productUrlRewrite();
        $this->assignProductToCategoryByAttributeSet();

        $time_end = microtime(true);
        $time = $time_end - $time_start;

        var_dump('$time ::: ' . $time);

        // Uwe ---
        \Magento\InventoryProductAlert\Plugin\AdaptProductSalabilityPlugin::class;
        \Magento\InventoryProductAlert\Plugin\AdaptProductSalabilityPlugin::class;
        \Magento\InventorySalesApi\Api\IsProductSalableInterface::class;
        \Magento\InventorySalesApi\Api\AreProductsSalableInterface::class;
    }

    private function moduleListInterface()
    {
        // $module = $this->_objectManager->get(\Magento\Framework\Module\ModuleListInterface::class);
        // var_dump($module->getAll());

        $module = $this->_objectManager->get(\SoftCommerce\Core\Model\ModuleListProviderInterface::class);
        var_dump('PackageInfo', $module->getList());
    }

    private function poisonPillPutInterface()
    {
        \Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_ERROR;
        var_dump(__METHOD__);
        $pillModel = $this->_objectManager->get(\Magento\Framework\MessageQueue\PoisonPill\PoisonPillPutInterface::class);
        $pillModel->put();
    }

    private function sendOrderEmail()
    {
        $orderRepository = $this->_objectManager->get(OrderRepositoryInterface::class);
        $order = $orderRepository->get(90);
        $orderSender = $this->_objectManager->get(\Magento\Sales\Model\Order\Email\Sender\OrderSender::class);
        var_dump('sending order ::: ' . $order->getIncrementId());
        $orderSender->send($order);
    }

    private function getProductSalableQtyInterface()
    {
        /** @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getSaleableQty */
        $getSaleableQty = $this->_objectManager->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
        $test = $getSaleableQty->execute('WS03-XS-Red', 2);
        var_dump($test);
    }

    private function appendReservations()
    {
        // append reservation example
        /** @var \Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin */
        /** @var \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface */
        /** @var \Magento\InventorySales\Model\PlaceReservationsForSalesEvent */
        // ------
        /** @var \Magento\InventoryReservationsApi\Model\AppendReservationsInterface */
        /** @var \Magento\InventorySalesApi\Api\GetStockBySalesChannelInterface */
        /** @var \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface */
        // Magento\InventorySales\Test\Integration\StockManagement\ReservationPlacingDuringRegisterProductsSaleTest
        // https://devdocs.magento.com/guides/v2.4/inventory/reservations.html

        /** @var \Magento\InventoryReservationsApi\Model\AppendReservationsInterface $appendReservations */
        $appendReservations = $this->_objectManager->create(\Magento\InventoryReservationsApi\Model\AppendReservationsInterface::class);
        // $test = $appendReservations->execute('');

        // Create reservation
        /** @var \Magento\InventorySales\Model\PlaceReservationsForSalesEvent */
        /** @var \Magento\InventoryReservations\Model\AppendReservations */
        /** @var \Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin */
    }

    private function getStockBySalesChannelInterface()
    {
        $salesChannelInterfaceFactory = $this->_objectManager->get(SalesChannelInterfaceFactory::class);
        $salesChannel = $salesChannelInterfaceFactory->create([
            'data' => [
                SalesChannelInterface::TYPE => SalesChannelInterface::TYPE_WEBSITE,
                SalesChannelInterface::CODE => 'w1'
            ]
        ]);

        /** @var \Magento\InventorySalesApi\Api\GetStockBySalesChannelInterface $getStockBySalesChannel */
        $getStockBySalesChannel = $this->_objectManager->create(\Magento\InventorySalesApi\Api\GetStockBySalesChannelInterface::class);
        $test = $getStockBySalesChannel->execute($salesChannel);

        var_dump('$test', $test->getStockId());

        // Get stock ID by channel
        // \Magento\InventorySales\Plugin\CatalogInventory\StockManagement\ProcessRegisterProductsSalePlugin;
        // \Magento\CatalogInventory\Model\StockManagement
        // \Magento\CatalogInventory\Api\RegisterProductSaleInterface
        /** @var \Magento\InventorySales\Model\IsProductSalableCondition\IsSalableWithReservationsCondition */
        /** @var \Magento\InventorySales\Model\IsProductSalableForRequestedQtyCondition\IsSalableWithReservationsCondition */
    }

    private function placeReservationsForSalesEvent()
    {
        /** @var \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationForSalesEvent */
        $placeReservationForSalesEvent = $this->_objectManager
            ->create(\Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface::class);

        /** @var \Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin $appendReservationsAfterOrderPlacementPlugin */
        $appendReservationsAfterOrderPlacementPlugin = $this->_objectManager
            ->get(\Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin::class);
        $appendReservationsAfterOrderPlacementPlugin->aroundPlace();
    }

    private function cleanupReservations()
    {
        /** @var \Magento\InventoryReservationsApi\Model\CleanupReservationsInterface $object */
        $object = $this->_objectManager->create(\Magento\InventoryReservationsApi\Model\CleanupReservationsInterface::class);
        // $object->execute();
        // $object->_execute();
    }

    private function salesOrderReservationRepository()
    {
        $salesOrder = $this->_objectManager->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $order = $salesOrder->get(40);
        var_dump('$order', $order->getEntityId(), 'increment', $order->getIncrementId());
        $object = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\SalesOrderReservationRepositoryInterface::class);
        $list = $object->getList($order->getEntityId(), (string) $order->getIncrementId());
        var_dump($list);
    }

    private function salesOrderReservationSourceAssignment()
    {
        $processor = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\SalesOrderReservationSourceAssignmentInterface::class);
        $processor->execute(['000000093']);
    }

    private function _catalogProductLinks()
    {
        /** @var \Magento\Catalog\Api\ProductLinkTypeListInterface $productLinkTypeList */
        $productLinkTypeList = $this->_objectManager->create(\Magento\Catalog\Api\ProductLinkTypeListInterface::class);
        foreach ($productLinkTypeList->getItems() as $item) {
            var_dump($item->getName(), $item->getCode());
        }

        // var_dump($productLinkTypeList->getItems());
        // \Magento\Catalog\Api\ProductLinkTypeListInterface
    }

    private function bulkOperations()
    {
        // Magento\Catalog\Controller\Adminhtml\Product\Action\Attribute\Save
        // Magento\Catalog\Model\Attribute\Backend\Consumer
        // use Magento\Framework\MessageQueue\PublisherInterface;
        // Magento\InventorySales\Plugin\Catalog\Model\ResourceModel\Product\UpdateReservationsPlugin

        // 18 inventory.reservation.sourceAssignment
        $bulkUuid = '31c39be6bb30fbb7896652c359394f08';
        /** @var \Magento\AsynchronousOperations\Api\BulkStatusInterface $bulkStatusInterface */
        $bulkStatusInterface = $this->_objectManager->create(\Magento\AsynchronousOperations\Api\BulkStatusInterface::class);
        var_dump($bulkStatusInterface->getBulkDetailedStatus($bulkUuid));
        var_dump($bulkStatusInterface->getBulkStatus($bulkUuid));

        return;
        $consumer = $this->consumerFactory->get($consumerName, $batchSize);
        $consumer->process($numberOfMessages);

        return;
        // PUBLISHER ::::
        /** @var \Magento\Framework\DataObject\IdentityGeneratorInterface $identityService */
        $identityService = $this->_objectManager->create(\Magento\Framework\DataObject\IdentityGeneratorInterface::class);
        /** @var \Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory $operationFactory */
        $operationFactory = $this->_objectManager->create(\Magento\AsynchronousOperations\Api\Data\OperationInterfaceFactory::class);
        /** @var \Magento\Framework\Serialize\SerializerInterface $serializer */
        $serializer = $this->_objectManager->create(\Magento\Framework\Serialize\SerializerInterface::class);
        /** @var \Magento\Framework\Bulk\BulkManagementInterface $bulkManagement */
        $bulkManagement = $this->_objectManager->create(\Magento\Framework\Bulk\BulkManagementInterface::class);
        $productIds = [1,2,3,4,5,6,7,8,9,10];
        $productIdsChunks = array_chunk($productIds, 5);
        $bulkUuid = $identityService->generateId();
        $bulkDescription = __('Plenty item product import has been processed.');
        $attributesData = ['country_of_manufacture' => 'UK', 'description' => 'Test Desc'];
        $operations = [];
        foreach ($productIdsChunks as $productIdsChunk) {
            $dataToEncode = [
                'meta_information' => 'Plenty Item ::: Import Product',
                'product_ids' => $productIdsChunk,
                'store_id' => 0,
                'website_id' => 0,
                'attributes' => $attributesData
            ];
            $data = [
                'data' => [
                    'bulk_uuid' => $bulkUuid,
                    'topic_name' => 'plenty_item_product_import.async',
                    'serialized_data' => $serializer->serialize($dataToEncode),
                    'status' => \Magento\Framework\Bulk\OperationInterface::STATUS_TYPE_OPEN,
                ]
            ];

            $operations[] = $operationFactory->create($data);
        }

        var_dump('$operations', $operations);

        if (!empty($operations)) {
            $result = $bulkManagement->scheduleBulk(
                $bulkUuid,
                $operations,
                $bulkDescription,
                1
            );
            var_dump($result);
            if (!$result) {
                throw new LocalizedException(
                    __('Something went wrong while processing the request.')
                );
            }
        }

        // e885dd94-9aa6-4a1a-9c00-4f8f653af87f
        /** @var \Magento\AsynchronousOperations\Api\BulkStatusInterface $bulkStatusInterface */
        $bulkStatusInterface = $this->_objectManager->create(\Magento\AsynchronousOperations\Api\BulkStatusInterface::class);
        var_dump($bulkStatusInterface->getBulkDetailedStatus($bulkUuid));
        var_dump($bulkStatusInterface->getBulkStatus($bulkUuid));
    }

    private function monologLogToMail()
    {
        /** @var \Plenty\Stock\Logger\Logger $object */
        $object = $this->_objectManager->create(\Plenty\Stock\Logger\Logger::class);
        $object->alert('Test message', ['error' => [['Could not xxx'], ['Could not xxx']]]);
        var_dump('logged to email.');
    }

    private function viewLogJsonDecoded()
    {
        $requestOrder = '{"address":{"shipping":{"gender":"","title":"","name1":"GA Weissenstein GmbH","name2":"Andreas","name3":"Altstätter (GA Weissenstein)","name4":"","address1":"Weissensteinstrasse 1","address2":"","address3":"","address4":"","postalCode":"4503","town":"Solothurn","stateId":null,"countryId":4,"options":[{"typeId":2,"value":"93321"},{"typeId":4,"value":"0793968866"},{"typeId":5,"value":"rx0mail@icloud.com"}],"contactRelations":[{"contactId":26442,"typeId":2}]}},"order":{"typeId":1,"ownerId":6,"plentyId":47806,"referrerId":9,"statusId":"5","orderItems":[{"typeId":4,"referrerId":9,"quantity":1,"countryVatId":4,"vatField":0,"vatRate":7.7,"orderItemName":"8750 Prämienpunkte verwendet","amounts":[{"isSystemCurrency":true,"currency":"CHF","exchangeRate":1,"priceOriginalGross":"-8.7500","surcharge":0,"discount":0,"isPercentage":false}]},{"typeId":1,"itemVariationId":31039,"referrerId":9,"shippingProfileId":21,"quantity":15,"orderItemName":"Elfa Pod Kartuschen von Elf Bar","countryVatId":4,"vatField":0,"vatRate":"8","amounts":[{"isSystemCurrency":true,"currency":"CHF","exchangeRate":1,"priceOriginalGross":"7.7000","priceOriginalNet":"7.1500","priceNet":"7.1500","surcharge":0,"discount":0,"isPercentage":false}],"warehouseId":109,"properties":[{"typeId":1,"value":"109"},{"typeId":2,"value":"21"}]},{"typeId":1,"itemVariationId":31033,"referrerId":9,"shippingProfileId":21,"quantity":20,"orderItemName":"Elfa Pod Kartuschen von Elf Bar","countryVatId":4,"vatField":0,"vatRate":"8","amounts":[{"isSystemCurrency":true,"currency":"CHF","exchangeRate":1,"priceOriginalGross":"7.5000","priceOriginalNet":"6.9500","priceNet":"6.9500","surcharge":0,"discount":0,"isPercentage":false}],"warehouseId":109,"properties":[{"typeId":1,"value":"109"},{"typeId":2,"value":"21"}]},{"typeId":1,"itemVariationId":31032,"referrerId":9,"shippingProfileId":21,"quantity":10,"orderItemName":"Elfa Pod Kartuschen von Elf Bar","countryVatId":4,"vatField":0,"vatRate":"8","amounts":[{"isSystemCurrency":true,"currency":"CHF","exchangeRate":1,"priceOriginalGross":"7.7000","priceOriginalNet":"7.1500","priceNet":"7.1500","surcharge":0,"discount":0,"isPercentage":false}],"warehouseId":109,"properties":[{"typeId":1,"value":"109"},{"typeId":2,"value":"21"}]},{"typeId":1,"itemVariationId":31035,"referrerId":9,"shippingProfileId":21,"quantity":10,"orderItemName":"Elfa Pod Kartuschen von Elf Bar","countryVatId":4,"vatField":0,"vatRate":"8","amounts":[{"isSystemCurrency":true,"currency":"CHF","exchangeRate":1,"priceOriginalGross":"7.7000","priceOriginalNet":"7.1500","priceNet":"7.1500","surcharge":0,"discount":0,"isPercentage":false}],"warehouseId":109,"properties":[{"typeId":1,"value":"109"},{"typeId":2,"value":"21"}]},{"typeId":6,"referrerId":9,"quantity":1,"countryVatId":4,"vatField":0,"vatRate":7.7,"orderItemName":"Schweizer Post - Priority - Kostenlos ab CHF 20.","amounts":[{"isSystemCurrency":true,"currency":"CHF","exchangeRate":1,"priceOriginalGross":"0.0000","priceOriginalNet":"0.0000","priceNet":"0.0000","surcharge":0,"discount":0,"isPercentage":false}],"shippingProfileId":21,"properties":[{"typeId":2,"value":"21"}]}],"properties":[{"typeId":3,"value":"6007"},{"typeId":7,"value":"4000033889"},{"typeId":8,"value":"65560"},{"typeId":2,"value":"21"},{"typeId":6,"value":"de"}],"addressRelations":[{"typeId":1,"addressId":48578},{"typeId":2,"addressId":99255}],"relations":[{"referenceType":"contact","referenceId":26442,"relation":"receiver"}]},"payment":{"amount":"410.7500","exchangeRatio":0,"mopId":6007,"currency":"CHF","type":"credit","status":2,"transactionType":2,"unaccountable":0,"properties":[{"typeId":10,"value":"4000033889"},{"typeId":11,"value":"Andreas Altstätter"},{"typeId":12,"value":"rx0mail@icloud.com"},{"typeId":1,"value":"345196251"},{"typeId":3,"value":"345196251"}],"order":{"orderId":210595},"updateOrderPaymentStatus":true,"contact":{"contactId":26442}},"payment_relation":[{"resource":"/rest/payment/215001/contact/26442","method":"POST"}]}';
        $requestOrder = json_decode($requestOrder, true);
        var_dump('$requestOrder', $requestOrder);

    }

    private function dbSetup()
    {
        $service = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfile\Setup\Patch\Data\ChangeOrderReferrerConfigPath::class);
        $service->apply();
    }

    private function assignAttributeSetByCategory()
    {
        $module = $this->_objectManager->get(\SoftCommerce\Utils\Console\Command\AssignAttributeSetByCategory::class);
        $module->testDebugging();
    }

    private function generateProductUrlKeyByAttribute()
    {
        $module = $this->_objectManager->get(\SoftCommerce\UrlRewriteGenerator\Console\Command\GenerateProductUrlKeyByAttribute::class);
        $module->testDebugging('name', [41]);
    }

    private function productUrlRewrite()
    {
        $module = $this->_objectManager->get(\SoftCommerce\UrlRewriteGenerator\Model\ProductUrlRewrite::class);
        $module->execute([41]);
    }

    private function assignProductToCategoryByAttributeSet()
    {
        $module = $this->_objectManager->get(\SoftCommerce\Utils\Console\Command\AssignProductToCategoryByAttributeSet::class);
        $module->testDebugging(13, [50]);
    }
}
