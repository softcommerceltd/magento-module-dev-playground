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
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\OrderRepository;
use SoftCommerce\PlentyCustomer\Api\CustomerRepositoryInterface;
use SoftCommerce\PlentyOrder\Model\GetSalesOrderIncrementIdByEntityIdInterface;
use SoftCommerce\PlentyOrderProfile\Model\Config\ApiConfigInterfaceFactory;
use SoftCommerce\PlentyOrderProfile\Model\OrderCollectServiceInterface;
use SoftCommerce\PlentyOrderProfile\Model\OrderExportServiceInterfaceFactory;
use SoftCommerce\PlentyOrderProfile\Model\OrderImportServiceInterface;
use SoftCommerce\PlentyOrderProfile\Model\OrderImportServiceInterfaceFactory;
use SoftCommerce\PlentyOrderRestApi\Model\Request\Order\DocumentSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyOrderRestApi\Model\Request\Order\DocumentSearchCriteriaInterface;
use SoftCommerce\PlentyOrderRestApi\Model\Request\OrderSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyOrderRestApi\Model\Request\OrderSearchCriteriaInterface;
use SoftCommerce\PlentyOrderRestApi\Model\Request\PaymentSearchCriteriaBuilderInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;

class Order extends Action
{
    /**
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
        var_dump(__METHOD__);

        // PROFILE CONFIG
        // $this->orderApiConfig();

        // SERVICES
        // $this->orderCollectService();
        $this->orderExportService();
        // $this->orderImportService();

        // CRON SCHEDULE SERVICES
        // $this->cronScheduleOrderCollect();
        // $this->cronScheduleOrderExport();
        // $this->cronScheduleOrderImport();

        // MODEL TESTING
        // $this->plentyAddressModel();
        // $this->plentyCustomerAddressModel();
        // $this->plentyOrderAddressModel();
        // $this->plentyOrderModel();
        // $this->salesOrderReservationRepository();
        // $this->OrderDeleteManagementInterface();
        // $this->getSalesOrderIncrementIdByEntityId();

        // REST API
        // $this->restOrderInterface();
        // $this->restOrderDocumentInterface();
        // $this->restOrderAddressInterface();
        // $this->restOrderItemInterface();
        // $this->restPaymentInterface();
        // $this->deleteOrdersExternally();

        // SCHEDULER

        // GENERAL MODELS
        // $this->getOrderTaxRate();

        // Sales Order Grid Refresh
        // $this->salesOrderGridRefresh();

        // FAKE ORDER CREATOR
        // $this->fakeOrderCronSchedule();
        // $this->fakeOrderCreator();

        // UNLOCK ORDERS
        // $this->unlockClientOrders();
        // $this->unlockSalesOrders();
    }

    private function orderExportService()
    {
        $ids = [344]; // 1604, 1606, 1607, 1609
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();
        $serviceFactory = $this->_objectManager->get(OrderExportServiceInterfaceFactory::class);
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_order_export', 'entity_id');
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        $service->execute($searchCriteria);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function orderImportService()
    {
        $ids = [7300,7301,7302]; // 7149,7148,7146
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            // ->addFilter('updated_at_externally', '2022-08-07T14:00:00+00:00', 'gteq')
            // ->addFilter('status', 'pending')
            ->create();
        $serviceFactory = $this->_objectManager->get(OrderImportServiceInterfaceFactory::class);
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute(OrderImportServiceInterface::TYPE_ID, 'entity_id');
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        $service->execute($searchCriteria);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
        // check bellow
        \Magento\InventoryConfiguration\Model\GetStockItemConfiguration::class;
        \Magento\InventorySales\Plugin\InventoryReservationsApi\PreventAppendReservationOnNotManageItemsInStockPlugin::class;
        \Magento\InventorySourceDeductionApi\Model\SourceDeductionService::class; // !!!
        \Magento\InventoryCatalog\Plugin\InventoryApi\SynchronizeLegacyStockAfterDecrementStockPlugin::class;
        \Magento\Inventory\Model\SourceItem\Command\DecrementSourceItemQty::class;
        \Magento\InventorySales\Model\ReturnProcessor\DeductSourceItemQuantityOnRefund::class;
        \Magento\InventorySales\Model\ReturnProcessor\ProcessRefundItems::class;
        \Magento\Framework\Stdlib\StringUtils::class;
        \Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_ERROR;

        /** @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getSaleableQty */
        $getSaleableQty = $this->_objectManager->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
        $saleableQty = $getSaleableQty->execute('24-WG01', 2);
        var_dump('$saleableQty stock 2 >>>', $saleableQty);
    }

    private function orderCollectService()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_order_import', 'entity_id');

        $apiConfigFactory = $this->_objectManager->get(ApiConfigInterfaceFactory::class);
        $apiConfig = $apiConfigFactory->create(['profileId' => (int) $profileId]);
        $lastCollectedAtModel = $this->_objectManager->get(\SoftCommerce\PlentyProfile\Model\GetLastCollectedAtInterface::class);
        $lastCollectedAt = $lastCollectedAtModel->execute('plenty_order_entity');
        var_dump('$apiConfig->getOrderSearchFilters()', $apiConfig->getOrderSearchFilters());
        var_dump('$lastCollectedAt', $lastCollectedAt);

        $searchCriteriaBuilder = $this->_objectManager->create(OrderSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setIds([600]) // 859
            ->setOrderIds([7134]) // 6661, 6662
            // ->setUpdatedAtFrom('2022-08-16T11:16:19+00:00')
            // ->setReferrerId(13)
            ->setWithParameters($apiConfig->getOrderSearchFilters())

            ->setWithParameters([
                OrderSearchCriteriaInterface::WITH_FILTER_ADDRESS,
                OrderSearchCriteriaInterface::WITH_FILTER_RELATIONS,
                OrderSearchCriteriaInterface::WITH_FILTER_COMMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_LOCATIONS,
                OrderSearchCriteriaInterface::WITH_FILTER_PAYMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_DOCUMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_CONTACT_SENDER,
                OrderSearchCriteriaInterface::WITH_FILTER_CONTACT_RECEIVER,
                OrderSearchCriteriaInterface::WITH_FILTER_WAREHOUSE_SENDER,
                OrderSearchCriteriaInterface::WITH_FILTER_WAREHOUSE_RECEIVER,
                // OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_VARIATION,
                // OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_GIFT_CARD_CODES,
                // OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_TRANSACTIONS,
                OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_SERIAL_NUMBERS,
                OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_VARIATION_BARCODES,
                OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_COMMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_ORIGIN_ORDER_REFERENCES,
                OrderSearchCriteriaInterface::WITH_FILTER_SHIPPING_PACKAGES,
                OrderSearchCriteriaInterface::WITH_FILTER_SHIPPING_PACKAGES_ITEMS,
                OrderSearchCriteriaInterface::WITH_FILTER_SHIPPING_INFORMATION,
                OrderSearchCriteriaInterface::WITH_FILTER_PAYMENT_TERMS
            ])
            ->create();

        $service = $this->_objectManager->get(OrderCollectServiceInterface::class);
        $service->execute($searchCriteria);
        // $arrayOutput = $this->_objectManager->get(OutputArrayInterface::class);
        // $htmlOutput = $this->_objectManager->get(OutputHtmlInterface::class);
        var_dump('response', $service->getResponseStorage()->getData());
        var_dump('message', $service->getMessageStorage()->getData());
        // var_dump('$arrayOutput', $arrayOutput->execute($service->getMessageStorage()->getData()));
    }

    private function deleteOrdersExternally()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfile\Console\Command\DeleteOrder::class);
        // $model->process([7183, 7182]);
    }

    private function plentyAddressModel()
    {
        // -- CUSTOMER ADDRESS - REPO
        /** @var \Plenty\Customer\Api\AddressRepositoryInterface $repository */
        $repository = $this->_objectManager->get(\Plenty\Customer\Api\AddressRepositoryInterface::class);
        /** @var SearchCriteriaBuilder $searchCriteriaObj */
        $searchCriteriaObj = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $search = $searchCriteriaObj
            // ->addFilter('customer_id', 237)
            ->create();
        $collection = $repository->getList($search);
        var_dump('repo total count >>> ' . $collection->getTotalCount());
        foreach ($collection->getItems() as $item) {
            var_dump(get_class($item));
            var_dump($item->getData());
        }

        // -- CUSTOMER ADDRESS - COLLECTION
        $collectionFactory = $this->_objectManager
            ->get(\Plenty\Customer\Model\ResourceModel\Address\CollectionFactory::class);
        /** @var \Plenty\Customer\Model\ResourceModel\Address\Collection $collection */
        $collection = $collectionFactory->create();
        var_dump($collection->getSize());
        foreach ($collection as $item) {
            var_dump($item->getData());
        }
    }

    private function plentyCustomerAddressModel()
    {
        // -- REPO
        /** @var \Plenty\Customer\Api\CustomerAddressRepositoryInterface $repository */
        $repository = $this->_objectManager->get(\Plenty\Customer\Api\CustomerAddressRepositoryInterface::class);
        /** @var SearchCriteriaBuilder $searchCriteriaObj */
        $searchCriteriaObj = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchFilterBuilder = $this->_objectManager->get(FilterBuilder::class);
        $searchFilterGroupBuilder = $this->_objectManager->get(FilterGroupBuilder::class);

        $filter = $searchFilterBuilder
            ->setField(CustomerAddressInterface::CUSTOMER_ID)
            ->setValue(570)
            ->create();
        $filterGroup[] = $searchFilterGroupBuilder->setFilters([$filter])->create();

        $filter = $searchFilterBuilder
            ->setField(AddressInterface::ADDRESS_ID)
            ->setValue(197)
            ->create();
        $filterGroup[] = $searchFilterGroupBuilder->setFilters([$filter])->create();

        $search = $searchCriteriaObj
            ->setFilterGroups($filterGroup)
            // ->addFilter('customer_id', 237)
            // ->addFilter('parent_id', 1025)
            ->create();

        var_dump('$search', $search);

        $collection = $repository->getList($search);
        var_dump('repo total count >>> ' . $collection->getTotalCount());
        foreach ($collection->getItems() as $item) {
            var_dump('typeId >>> ' . $item->getTypeId());
            var_dump('getRelationType', $item->getRelationType());
            var_dump('repo --', $item->getData());
        }

        return;
        // -- COLLECTION
        $collectionFactory = $this->_objectManager
            ->get(\Plenty\Customer\Model\ResourceModel\Customer\Address\CollectionFactory::class);
        /** @var \Plenty\Customer\Model\ResourceModel\Customer\Address\Collection $collection */
        $collection = $collectionFactory->create();
        var_dump($collection->getSize());
        foreach ($collection as $item) {
            var_dump($item->getData());
        }
    }

    private function plentyOrderAddressModel()
    {
        $repository = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Api\OrderAddressRepositoryInterface::class);
        try {
            // $address = $repository->getByOrderId(3855, 2);
            // var_dump('repo ---', $address->getData());
            // var_dump($address->getOrderId());
        } catch (\Exception $e) {
            // var_dump($e->getMessage());
        }

        // return;

        var_dump(__METHOD__);

        /** @var SearchCriteriaBuilder $searchCriteriaObj */
        $searchCriteriaObj = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $search = $searchCriteriaObj
            ->addFilter('order_id', 6664)
            // ->addFilter('parent_id', 1018)
            ->create();
        $collection = $repository->getList($search);
        var_dump('total count >>> ' . $collection->getTotalCount());
        foreach ($collection->getItems() as $item) {
            var_dump($item->getData());
        }

        return;
        // -- ORDER ADDRESS - COLLECTION
        $collectionFactory = $this->_objectManager
            ->get(\SoftCommerce\PlentyOrder\Model\ResourceModel\Order\Address\CollectionFactory::class);
        /** @var \SoftCommerce\PlentyOrder\Model\ResourceModel\Order\Address\Collection $collection */
        $collection = $collectionFactory->create();
        // var_dump($collection->getSize());
        foreach ($collection as $item) {
            // var_dump($item->getData());
        }
    }

    private function restOrderInterface()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_order_import', 'entity_id');

        $apiConfigFactory = $this->_objectManager->get(ApiConfigInterfaceFactory::class);
        $apiConfig = $apiConfigFactory->create(['profileId' => (int) $profileId]);

        $searchCriteriaBuilder = $this->_objectManager->create(OrderSearchCriteriaBuilderInterface::class);
        var_dump('$apiConfig->getOrderSearchFilters()', $apiConfig->getOrderSearchFilters());
        $searchCriteria = $searchCriteriaBuilder
            // ->setIds([600]) // 859
            ->setOrderIds([7134])
            ->setWithParameters($apiConfig->getOrderSearchFilters())
            /*
            ->setWithParameters([
                OrderSearchCriteriaInterface::WITH_FILTER_ADDRESS,
                OrderSearchCriteriaInterface::WITH_FILTER_RELATIONS,
                OrderSearchCriteriaInterface::WITH_FILTER_COMMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_LOCATIONS,
                OrderSearchCriteriaInterface::WITH_FILTER_PAYMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_DOCUMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_CONTACT_SENDER,
                OrderSearchCriteriaInterface::WITH_FILTER_CONTACT_RECEIVER,
                OrderSearchCriteriaInterface::WITH_FILTER_WAREHOUSE_SENDER,
                OrderSearchCriteriaInterface::WITH_FILTER_WAREHOUSE_RECEIVER,
                // OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_VARIATION,
                // OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_GIFT_CARD_CODES,
                // OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_TRANSACTIONS,
                OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_SERIAL_NUMBERS,
                OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_VARIATION_BARCODES,
                OrderSearchCriteriaInterface::WITH_FILTER_ORDER_ITEMS_COMMENTS,
                OrderSearchCriteriaInterface::WITH_FILTER_ORIGIN_ORDER_REFERENCES,
                OrderSearchCriteriaInterface::WITH_FILTER_SHIPPING_PACKAGES,
                OrderSearchCriteriaInterface::WITH_FILTER_SHIPPING_PACKAGES_ITEMS,
                OrderSearchCriteriaInterface::WITH_FILTER_SHIPPING_INFORMATION,
                OrderSearchCriteriaInterface::WITH_FILTER_PAYMENT_TERMS
            ])*/
            ->create();

        $httpFactory = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\OrderInterfaceFactory::class);
        $client = $httpFactory->create(['clientId' => 31241]);
        $orders = $client->getList($searchCriteria);
        var_dump('$orders', $orders->toArray());
    }

    private function restOrderDocumentInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(DocumentSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setContactId(237)
            // ->setDocumentId(383)
            ->setWithParameters([
                DocumentSearchCriteriaInterface::WITH_FILTER_REFERENCES
            ])
            ->create();

        $httpFactory = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Order\DocumentInterfaceFactory::class);
        $client = $httpFactory->create(['clientId' => 31241]);
        // $orders = $client->getList($searchCriteria);
        // var_dump('$orders', $orders->toArray());
        $orders = $client->getListOrderDocuments(6653, 'invoice', $searchCriteria);
        var_dump('$orders', $orders->toArray());
        return;
        foreach ($category as $item) {
            var_dump('$item', get_class($item));
            var_dump('item', $item->getData());
        }
    }

    private function restOrderAddressInterface()
    {
        $httpFactory = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Order\AddressInterfaceFactory::class);
        $client = $httpFactory->create(['clientId' => 31241]);
        $result = $client->getList(6653, 1);
        var_dump('$result', $result->toArray());
    }

    private function restOrderItemInterface()
    {
        $httpFactory = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Order\ItemInterfaceFactory::class);
        $client = $httpFactory->create(['clientId' => 31241]);
        $result = $client->getList(6653);
        var_dump('$result', $result->toArray());
    }

    private function restPaymentInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(PaymentSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setContactId(237)
            ->setPaymentId(5846)
            ->setPage(10)
            ->create();

        $httpFactory = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\PaymentInterfaceFactory::class);
        $client = $httpFactory->create(['clientId' => 31241]);
        $result = $client->getList($searchCriteria);
        var_dump('$result', $result->toArray());
    }

    private function cronScheduleOrderCollect()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_order_import', 'entity_id');

        $service = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfileSchedule\Model\OrderCollect::class);
        $response = $service->execute((int) $profileId);
        var_dump('response', $response->getData());
    }

    private function cronScheduleOrderExport()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_order_export', 'entity_id');

        $service = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfileSchedule\Model\OrderExport::class);
        $response = $service->execute((int) $profileId);
        var_dump('response', $response->getData());
    }

    private function cronScheduleOrderImport()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_order_import', 'entity_id');

        $service = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfileSchedule\Model\OrderImport::class);
        $response = $service->execute((int) $profileId);
        var_dump('response', $response->getData());
    }

    private function addressCollectService()
    {
        /** @var \Plenty\Customer\Api\AddressManagementInterface $management */
        $management = $this->_objectManager->create(\Plenty\Customer\Api\AddressManagementInterface::class);
        try {
            $management
                ->setFilterWith(['contactRelations','orderRelations'])
                // ->setFilterItemsPerPage(10)
                ->collect();

            var_dump($management->getCollectionResult());
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    private function salesOrderReservationRepository()
    {
        // $salesOrder = $this->_objectManager->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        // $order = $salesOrder->get(40);
        // var_dump('$order', $order->getEntityId(), 'increment', $order->getIncrementId());
        $object = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\SalesOrderReservationRepositoryInterface::class);
        $list = $object->getList('3000000134');
        var_dump('$list', $list);
        $list = $object->getList('3000000134', 'MS12-M-Red', 'order_placed');
        var_dump('$list with search', $list);
    }

    private function orderApiConfig()
    {
        $factory = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfile\Model\Config\ApiConfigInterfaceFactory::class);
        $model = $factory->create(['profileId' => 14]);
        var_dump('$model', $model->getOrderSearchFilters());
    }

    private function OrderDeleteManagementInterface()
    {
        $object = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\OrderDeleteManagementInterface::class);
        $object->execute([4704, 4700], true);
        var_dump('response', $object->getResponseStorage()->getData());
    }

    private function getSalesOrderIncrementIdByEntityId()
    {
        $object = $this->_objectManager->get(GetSalesOrderIncrementIdByEntityIdInterface::class);
        $result = $object->execute(39);
        var_dump('result', $result);
    }

    private function salesOrderReservationSourceAssignment()
    {
        $processor = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\SalesOrderReservationSourceAssignmentInterface::class);
        $processor->execute(['000000093']);
    }

    private function salesOrder()
    {
        /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->_objectManager->create(\Magento\Sales\Api\OrderRepositoryInterface::class);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $orderRepo->get(372);
        foreach ($order->getAllItems() as $item) {
            var_dump('item id >> ' . $item->getId());
            var_dump($item->getProductOptions());
        }
    }

    private function salesOrderPayment()
    {
        /** @var \Magento\Sales\Api\OrderRepositoryInterface $orderRepo */
        $orderRepo = $this->_objectManager->create(\Magento\Sales\Api\OrderRepositoryInterface::class);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $orderRepo->get(35);
        $payment = $order->getPayment();
        $address = $order->getAddresses();
        $items = $order->getItems();
        $shippingAddress = $order->getShippingAddress();
        var_dump($payment->getId());

        var_dump('$order', $order->canReviewPayment());
        var_dump('getIsTransactionPending()', $payment->getIsTransactionPending());
        var_dump('getAmountPaid', $payment->getAmountPaid());
        var_dump('canCapture', $payment->canCapture());
        var_dump('method instance', get_class($payment->getMethodInstance()));
        /** @var \Klarna\Kp\Model\Payment\Kp $methodInstance */
        $methodInstance = $payment->getMethodInstance();
        var_dump('$methodInstance->isGateway()', $methodInstance->isGateway());
        var_dump('$methodInstance->isOffline()', $methodInstance->isOffline());
        /** @var \Klarna\Kp\Model\Payment\Kp $klarna */
        $klarna = '';

        // lookout for sales_payment_transaction is_closed
        // sales_order_payment amount_paid < order amount paid
        // $payment->canCapture()
    }

    private function salesOrderShipmentSaveAfter()
    {
        $shipmentRepo = $this->_objectManager->get(\Magento\Sales\Api\ShipmentRepositoryInterface::class);
        $shipment = $shipmentRepo->get(1);
        $observer = $this->_objectManager->create(Observer::class, ['event' => $shipment]);
        $observerEvent = $this->_objectManager->create(SalesOrderShipmentSaveAfter::class);
        $observerEvent->execute($observer);
    }

    private function restPaymentTest()
    {
        /** @var \SoftCommerce\PlentyOrder\Rest\PaymentInterface $restPayment */
        $restPayment = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Rest\PaymentInterface::class);
        $response = $restPayment->getSearchPayments(1, 3390);

        foreach ($response as $item) {
            var_dump($item->getData());
        }
        // var_dump('$response', $response);
        return;
    }

    private function createOrderQueue()
    {
        $object = $this->_objectManager->get(\Dev\PlentyDev\Cron\Backend\CreateOrderQueue::class);
        $object->execute();
    }

    private function salesOrderIndexGridAsyncInsert()
    {
        $grid = $this->_objectManager->get(\Magento\Sales\Model\ResourceModel\Grid::class);
        $grid->refresh(323);
    }

    private function getOrderTaxRate()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\GetSalesOrderTaxRateInterface::class);
        $data = $model->getShippingItemTaxRate(1611);
        var_dump('data', $data);
    }

    private function salesOrderGridRefresh()
    {
        $repository = $this->_objectManager->get(OrderRepository::class);
        $order = $repository->get(51);
        $observer = $this->_objectManager->create(\Magento\Framework\Event\Observer::class);
        $observer->setObject($order);

        $model = $this->_objectManager->get('SalesOrderIndexGridSyncInsert');
        $model->execute($observer);
    }

    private function fakeOrderCronSchedule()
    {
        $model = $this->_objectManager->get(\SoftCommerce\FakeEntityCreator\Cron\Backend\OrderQueue::class);
        $model->execute();
    }

    private function fakeOrderCreator()
    {
        $model = $this->_objectManager->get(\SoftCommerce\FakeEntityCreator\Model\OrderServiceInterface::class);
        $model->execute();
    }

    private function unlockClientOrders()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfileSchedule\Cron\Backend\UnlockClientOrders::class);
        $model->execute();
    }

    private function unlockSalesOrders()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderProfileSchedule\Cron\Backend\UnlockSalesOrders::class);
        $model->execute();
    }
}
