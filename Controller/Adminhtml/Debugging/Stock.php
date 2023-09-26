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
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Event\Observer;
use Magento\Framework\ObjectManagerInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use SoftCommerce\PlentyOrder\Model\GetSalesOrderEntityIdByIncrementIdInterface;
use SoftCommerce\PlentyProfile\Model\GetLastCollectedAtInterface;
use SoftCommerce\PlentyStock\Api\Data\InventoryInterface;
use SoftCommerce\PlentyStock\Model\MessageQueue\StockItemSchedulerInterface;
use SoftCommerce\PlentyStockProfile\Model\StockCollectServiceInterface;
use SoftCommerce\PlentyStockProfile\Model\StockImportServiceInterfaceFactory;
use SoftCommerce\PlentyStockReservation\Model\SourceAssignmentManagementInterface;
use SoftCommerce\PlentyStockRestApi\Model\Request\StockSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyStockRestApi\Model\Request\StockSearchCriteriaInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;

class Stock extends Action
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
        // Uncaught TypeError: Cannot read properties of null (reading 'padding')

        // HTTP CLIENT TEST
        // $this->httpStockInterface();

        // SERVICE
        // $this->stockCollectServiceInterface();
        // $this->stockImportService();

        // Message Queue
        // $this->stockItemSchedulerInterface();

        // CRON JOB
        // $this->cronStockCollect();
        // $this->cronStockImport();
        // $this->cronReservationCleanup();

        // INVENTORY
        // $this->getSalesOrderEntityIdByIncrementIdInterface();
        $this->getProductSalableQtyInterface();
        // $this->appendReservations();
        // $this->getStockBySalesChannelInterface();
        // $this->getDefaultStockProviderInterface();
        // $this->getDefaultSourceProviderInterface();
        // $this->placeReservationsForSalesEvent();
        // $this->cleanupReservations();
        // $this->cleanupClientReservations();
        // $this->salesOrderReservationRepository();
        // $this->salesOrderReservationSourceAssignmentQueue();
        // $this->salesOrderReservationSourceAssignment();
        // $this->sourceAssignmentManagementInterface();
        // $this->getStockSalesChannelData();
        // $this->sourceItemRepositoryInterface();
        // $this->ResolveOrderInconsistency();
        // $this->ChangeParentStockStatus();

        // CONSOLE COMMAND
        // $this->assignStockSource();

        // SALES ORDER
        // $this->salesOrder();
        // $this->salesOrderPayment();

        // OBSERVER EVENTS
        // $this->salesOrderShipmentSaveAfter();

        // SOURCE SELECTION ALGORITHM
        // $this->getSourcesByOrderIdSkuAndQty();
        // $this->getOrderItemSourceSelectionInterface();
        // $this->GetSourceSelectionAlgorithmListInterface();
        // $this->getInStockSourceItemsBySkusAndSortedSource();

        // SHIPMENT REGISTER RESERVATIONS SALES EVENT
        // $this->appendReservationsAfterOrderPlacementPlugin();
        // $this->appendReservationsAfterShipmentCreatedPlugin();

        // CONFIG COLLECT MANAGEMENT
        // $this->collectWarehouseConfigs();
        // $this->collectWarehouseDimensionConfigs();
        // $this->collectWarehouseLocationDetails();
        // $this->collectWarehouseLocations();
    }

    private function httpStockInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(StockSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setVariationId(8369)
            ->setIds([34450,34494], null, StockSearchCriteriaInterface::VARIATION_ID)
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            ->create();
        $client = $this->_objectManager->create(\SoftCommerce\PlentyStockRestApi\Model\StockInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function stockCollectServiceInterface()
    {
        $getLastCollectedAt = $this->_objectManager->get(GetLastCollectedAtInterface::class);
        $lastCollectedAt = $getLastCollectedAt->execute(InventoryInterface::DB_TABLE_NAME);
        var_dump('$lastCollectedAt', $lastCollectedAt);
        $searchCriteriaBuilder = $this->_objectManager->get(StockSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setVariationId(8369)
            ->setIds([34494], null, StockSearchCriteriaInterface::VARIATION_ID)
            // ->setUpdatedAtFrom($lastCollectedAt)
            // ->setUpdatedAtBetween('x-between')
            ->create();

        $service = $this->_objectManager->get(StockCollectServiceInterface::class);
        $service->execute($searchCriteria);
        var_dump('$service', $service->getEntityIdStorage()->getData());
        var_dump('$service', $service->getMessageStorage()->getData());
    }

    private function stockImportService()
    {
        $ids = [34656]; // 34494, 34595, 31025
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('variation_id', $ids, 'in')
            // ->addFilter('status', 'pending')
            ->create();

        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = (int) $getProfileByTypeId->execute('plenty_stock_import', 'entity_id');
        $serviceFactory = $this->_objectManager->get(StockImportServiceInterfaceFactory::class);
        $service = $serviceFactory->create(['data' => ['profile_id' => $profileId]]);
        $service->execute($searchCriteria);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function cronStockCollect()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_stock_import', 'entity_id');
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockProfileSchedule\Model\StockCollect::class);
        $response = $model->execute((int) $profileId);
    }

    private function cronStockImport()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockProfileSchedule\Cron\Backend\StockCollect::class);
        $response = $model->execute();
    }

    private function cronReservationCleanup()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockReservation\Cron\Backend\ReservationCleanup::class);
        $model->execute();
    }

    private function stockItemSchedulerInterface()
    {
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

        // Schedule data
        $model = $this->_objectManager->get(StockItemSchedulerInterface::class);
        // $model->execute(['24-UB02', '24-WB06']);

        // Process data
        $dataFactory = $this->_objectManager->get(\SoftCommerce\PlentyStock\Model\MessageQueue\StockItemScheduler\RequestDataProviderFactory::class);
        $data = $dataFactory->create(['sku' => ['24-UB02', '24-WB06']]);
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStock\Model\MessageQueue\StockItemScheduler\RequestDataProcessor::class);
        $model->execute($data);

        // Test reservatino saleableqtyupdate
        /*
        $reserveFactory = $this->_objectManager->get(\Magento\InventoryIndexer\Model\Queue\ReservationDataFactory::class);
        $reserveData = $reserveFactory->create(
            [
                'stock' => 2,
                'skus' => ['test-1', 'test-2']
            ]
        );
        var_dump('$reserveData', $reserveData);
        $publisher = $this->_objectManager->get(\Magento\Framework\MessageQueue\PublisherInterface::class);
        $publisher->publish(
            'inventory.reservations.updateSalabilityStatus',
            $reserveData
        );*/
    }

    private function getSalesOrderEntityIdByIncrementIdInterface()
    {
        $model = $this->_objectManager->get(GetSalesOrderEntityIdByIncrementIdInterface::class);
        $result = $model->execute('2020200202');
        var_dump('result ??', $result);
    }

    private function getProductSalableQtyInterface()
    {
        /** @var \Magento\InventorySalesApi\Api\GetProductSalableQtyInterface $getSaleableQty */
        $getSaleableQty = $this->_objectManager->create(\Magento\InventorySalesApi\Api\GetProductSalableQtyInterface::class);
        $saleableQty = $getSaleableQty->execute('24-MB04', 2);
        var_dump('$saleableQty stock 2 >>>', $saleableQty);
        // $saleableQty = $getSaleableQty->execute('24-MG03', 2);
        // var_dump('$saleableQty stock 3 >>>', $saleableQty);
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

    private function salesOrderReservationRepository()
    {
        $salesOrder = $this->_objectManager->get(\Magento\Sales\Api\OrderRepositoryInterface::class);
        $order = $salesOrder->get(40);
        var_dump('$order', $order->getEntityId(), 'increment', $order->getIncrementId());
        $object = $this->_objectManager->get(\SoftCommerce\PlentyOrder\Model\SalesOrderReservationRepositoryInterface::class);
        $list = $object->getList($order->getEntityId(), (string) $order->getIncrementId());
        var_dump($list);
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

    private function getSourcesByOrderIdSkuAndQty()
    {
        $orderId = 116;
        $sku = 'WS03-XS-Red';
        $qty = 1;

        $GetInventoryRequestFromOrder = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Model\GetInventoryRequestFromOrder::class);
        $ItemRequestInterfaceFactory = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Api\Data\ItemRequestInterfaceFactory::class);
        $SourceSelectionServiceInterface = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Api\SourceSelectionServiceInterface::class);
        $GetDefaultSourceSelectionAlgorithmCodeInterface = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Api\GetDefaultSourceSelectionAlgorithmCodeInterface::class);
        $SourceRepositoryInterface = $this->_objectManager->get(\Magento\InventoryApi\Api\SourceRepositoryInterface::class);

        $algorithmCode = $GetDefaultSourceSelectionAlgorithmCodeInterface->execute();
        $requestItem = $ItemRequestInterfaceFactory->create([
            'sku' => $sku,
            'qty' => $qty
        ]);

        $inventoryRequest = $GetInventoryRequestFromOrder->execute($orderId, [$requestItem]);
        var_dump('$inventoryRequest', $inventoryRequest->getStockId());
        foreach ($inventoryRequest->getItems() as $item) {
            var_dump('request sku >> ' . $item->getSku());
        }

        $sourceSelectionResult = $SourceSelectionServiceInterface->execute(
            $inventoryRequest,
            $algorithmCode
        );

        $result = [];
        foreach ($sourceSelectionResult->getSourceSelectionItems() as $item) {
            $sourceCode = $item->getSourceCode();
            $result[] = [
                'sourceName' => $SourceRepositoryInterface->get($sourceCode)->getName(),
                'sourceCode' => $sourceCode,
                'qtyAvailable' => $item->getQtyAvailable(),
                'qtyToDeduct' => $item->getQtyToDeduct()
            ];
        }

        var_dump('$result', $result);
        return $result;

        $GetSourcesByOrderIdSkuAndQty = $this->_objectManager->get(\Magento\InventoryShippingAdminUi\Ui\DataProvider\GetSourcesByOrderIdSkuAndQty::class);
        $itemSource = $GetSourcesByOrderIdSkuAndQty->execute($orderId, $sku, $qty);
        var_dump('$itemSource', $itemSource);
    }

    private function getInStockSourceItemsBySkusAndSortedSource()
    {
        $skus = ['WS03-XS-Red'];
        $sortedSources = [
            'ks_manchester',
            'ks_london',
        ];

        $itemsTdDeliver = [];
        foreach ($skus as $item) {
            $itemsTdDeliver[$item] = 1;
        }

        $sourceItemSelections = [];
        $GetInStockSourceItemsBySkusAndSortedSource = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Model\GetInStockSourceItemsBySkusAndSortedSource::class);
        $sourceItems = $GetInStockSourceItemsBySkusAndSortedSource->execute($skus, $sortedSources);
        foreach ($sourceItems as $sourceItem) {
            var_dump('sku => ' . $sourceItem->getSku(), 'status => ' . $sourceItem->getStatus(), 'qty => ' . $sourceItem->getQuantity(), 'sourceCode => ' . $sourceItem->getSourceCode());
            $normalizedSku = $sourceItem->getSku();
            $sourceItemQtyAvailable = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Model\GetSourceItemQtyAvailableInterface::class)->execute($sourceItem);
            $qtyToDeduct = min($sourceItemQtyAvailable, $itemsTdDeliver[$normalizedSku] ?? 0.0);

            $sourceSelectionFactory = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Api\Data\SourceSelectionItemInterfaceFactory::class);

            $sourceItemSelections[] = [
                'sourceCode' => $sourceItem->getSourceCode(),
                'sku' => $sourceItem->getSku(),
                'qtyToDeduct' => $qtyToDeduct,
                'qtyAvailable' => $sourceItemQtyAvailable
            ];

            continue;
            $sourceItemSelections[] = $sourceSelectionFactory->create(
                [
                    'sourceCode' => $sourceItem->getSourceCode(),
                    'sku' => $sourceItem->getSku(),
                    'qtyToDeduct' => $qtyToDeduct,
                    'qtyAvailable' => $sourceItemQtyAvailable
                ]
            );

            $itemsTdDeliver[$normalizedSku] -= $qtyToDeduct;
        }

        var_dump('$sourceItemSelections', $sourceItemSelections);
    }

    private function getOrderItemSourceSelectionInterface()
    {
        $orderId = 453;
        $sku = 'WS03-XS-Red';
        $qty = 1;
        $GetOrderItemSourceSelection = $this->_objectManager->get(\Plenty\StockSourceSelection\Model\GetOrderItemSourceSelectionInterface::class);
        $result = $GetOrderItemSourceSelection->execute($orderId, $sku, $qty, 'distance');
        var_dump('result', $result);
        $FilterApplicableSource = $this->_objectManager->get(\Plenty\StockSourceSelection\Model\OrderItemSourceSelection\FilterApplicableSource::class);
        $result = $FilterApplicableSource->execute($result, $qty);
        // $FilterSourceSelectionAlgorithm = $this->_objectManager->get(\Plenty\StockSourceSelection\Model\OrderItemSourceSelection\FilterSourceSelectionAlgorithm::class);
        // $result = $FilterSourceSelectionAlgorithm->execute($result, $qty);
        var_dump('result', $result);
    }

    private function salesOrderShipmentSaveAfter()
    {
        $shipmentRepo = $this->_objectManager->get(\Magento\Sales\Api\ShipmentRepositoryInterface::class);
        $shipment = $shipmentRepo->get(1);
        $observer = $this->_objectManager->create(Observer::class, ['event' => $shipment]);
        $observerEvent = $this->_objectManager->create(SalesOrderShipmentSaveAfter::class);
        $observerEvent->execute($observer);
    }

    private function placeReservationsForSalesEvent()
    {
        /** @var \Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface $placeReservationForSalesEvent */
        $placeReservationForSalesEvent = $this->_objectManager
            ->create(\Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface::class);
        $placeReservationForSalesEvent->execute();

        /** @var \Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin $appendReservationsAfterOrderPlacementPlugin */
        $appendReservationsAfterOrderPlacementPlugin = $this->_objectManager
            ->get(\Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin::class);
        $appendReservationsAfterOrderPlacementPlugin->aroundPlace();
    }

    private function cleanupReservations()
    {
        /** @var \Magento\InventoryReservationsApi\Model\CleanupReservationsInterface $object */
        $object = $this->_objectManager->create(\Magento\InventoryReservationsApi\Model\CleanupReservationsInterface::class);
        $object->execute();
    }

    private function cleanupClientReservations()
    {
        $object = $this->_objectManager->create(\Plenty\StockReservation\Model\CleanupClientReservationsInterface::class);
        $object->execute();
        // $object->_execute();
    }

    private function salesOrderReservationSourceAssignmentQueue()
    {
        \Magento\MysqlMq\Model\QueueManagement::MESSAGE_STATUS_ERROR;

        $event = 'shipment_created'; // order_placed / shipment_created
        $incrementId = '000000148'; // order 000000118 / shipment 000000079
        $dataFactory = $this->_objectManager->get(\Plenty\StockReservation\Model\Queue\SourceAssignment\RequestDataProviderFactory::class);
        $request = $dataFactory->create(
            [
                'event' => $event,
                'object' => 'order',
                'id' => $incrementId
            ]
        );

        $handler = $this->_objectManager->get(\Plenty\StockReservation\Model\Queue\SourceAssignment\RequestDataHandler::class);
        $handler->execute($request);
    }

    private function salesOrderReservationSourceAssignment()
    {
        $processor = $this->_objectManager->get(\Plenty\StockReservation\Model\Queue\SourceAssignment\Processor\SalesOrder::class);
        $processor->execute('order_placed', '000000188');
    }

    private function sourceAssignmentManagementInterface()
    {
        $processor = $this->_objectManager->get(SourceAssignmentManagementInterface::class);
        $processor->execute('order', 'order_placed', '000000032');
        var_dump('response', $processor->getResponseStorage()->getData());
    }

    private function getStockSalesChannelData()
    {
        $processor = $this->_objectManager->get(\SoftCommerce\PlentyStock\Model\GetStockSalesChannelData::class);
        $test = $processor->getCodeByStockId(2);
        var_dump('test', $test);
    }

    private function sourceItemRepositoryInterface()
    {
        $processor = $this->_objectManager->get(\Plenty\Stock\Model\SourceItemRepositoryInterface::class);
        $sourceCode = 'ks_london'; // ks_manchester ks_london
        try {
            $list = $processor->getList('WS03-XS-Red');
            var_dump('list', $list);
            $get = $processor->getList('WS03-XS-Red');
            var_dump('$get', $get);
            $phyQty = $processor->getQtyPhysical('WS03-XS-Red', $sourceCode);
            var_dump('$phyQty', $phyQty);
            $reservedQty = $processor->getQtyReserved('WS03-XS-Red', $sourceCode);
            var_dump('$reservedQty', $reservedQty);
            $netQty = $processor->getQtyNet('WS03-XS-Red', $sourceCode);
            var_dump('$netQty', $netQty);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    private function ResolveOrderInconsistency()
    {
        $object = $this->_objectManager->get(\Plenty\StockReservation\Console\Command\ResolveOrderInconsistency::class);
        $object->test();
    }

    private function ChangeParentStockStatus()
    {
        $object = $this->_objectManager->get(\Magento\ConfigurableProduct\Model\Inventory\ChangeParentStockStatus::class);
        $object->execute([2042, 2041]);
    }

    private function reservationRepositoryInterface()
    {
        $repo = $this->_objectManager->get(\Plenty\StockReservation\Model\ReservationRepositoryInterface::class);
        $repo->getList();
    }

    private function assignStockSource()
    {
        $console = $this->_objectManager->get(\Plenty\StockReservation\Console\Command\AssignStockSource::class);
        $console->test();
    }

    private function GetSourceSelectionAlgorithmListInterface()
    {
        $obj = $this->_objectManager->get(\Magento\InventorySourceSelectionApi\Api\GetSourceSelectionAlgorithmListInterface::class);
        foreach ($obj->execute() as $item) {
            var_dump($item->getCode(), $item->getDescription(), $item->getTitle());
        }
    }

    private function getDefaultStockProviderInterface()
    {
        $DefaultStockProviderInterface = $this->_objectManager->get(\Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface::class);
        $defaultStockId = $DefaultStockProviderInterface->getId();
        var_dump('$defaultStockId', $defaultStockId);
    }

    private function getDefaultSourceProviderInterface()
    {
        $DefaultStockProviderInterface = $this->_objectManager->get(\Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface::class);
        $defaultSource = $DefaultStockProviderInterface->getCode();
        var_dump('$defaultSource', $defaultSource);
    }

    private function appendReservationsAfterOrderPlacementPlugin()
    {
        $order = $this->_objectManager
            ->get(\Magento\Sales\Api\OrderRepositoryInterface::class)
            ->get(9);

        $observer = $this->_objectManager->create(\Magento\Framework\Event\Observer::class);
        $eventFactory = $this->_objectManager->get(\Magento\Framework\EventFactory::class);
        $event = $eventFactory->create();
        $event->setData('order', $order);
        $observer->setEvent($event);

        $this->_objectManager
            ->get(\Magento\InventorySales\Plugin\Sales\OrderManagement\AppendReservationsAfterOrderPlacementPlugin::class)
            ->execute($observer);
    }

    private function appendReservationsAfterShipmentCreatedPlugin()
    {
        $shipment = $this->_objectManager
            ->get(\Magento\Sales\Api\ShipmentRepositoryInterface::class)
            ->get(9);

        $observer = $this->_objectManager->create(\Magento\Framework\Event\Observer::class);
        $eventFactory = $this->_objectManager->get(\Magento\Framework\EventFactory::class);
        $event = $eventFactory->create();
        $event->setData('shipment', $shipment);
        $observer->setEvent($event);

        $this->_objectManager
            ->get(\Magento\InventoryShipping\Observer\SourceDeductionProcessor::class)
            ->execute($observer);
    }

    private function sourceDeductionRequestFromShipmentFactory()
    {
        $shipment = $this->_objectManager
            ->get(\Magento\Sales\Api\ShipmentRepositoryInterface::class)
            ->get(9);
        $shipmentItems = $this->_objectManager
            ->get(\Magento\InventoryShipping\Model\GetItemsToDeductFromShipment::class)
            ->execute($shipment);
        var_dump('shipmentItems', $shipmentItems);

        $sourceDeductionRequest = $this->_objectManager
            ->get(\Magento\InventoryShipping\Model\SourceDeductionRequestFromShipmentFactory::class)
            ->execute($shipment, 'ks_manchester', $shipmentItems);

        var_dump('getSourceCode >> ' . $sourceDeductionRequest->getSourceCode());
        var_dump('getSalesEvent getType >> ' . $sourceDeductionRequest->getSalesEvent()->getType());
        var_dump('getSalesEvent getObjectId >> ' . $sourceDeductionRequest->getSalesEvent()->getObjectId());
        var_dump('get class', get_class($sourceDeductionRequest->getSalesEvent()->getExtensionAttributes()));
        var_dump('getSalesEvent getExtensionAttributes >> ', $sourceDeductionRequest->getSalesEvent()->getExtensionAttributes());

        foreach ($sourceDeductionRequest->getItems() as $item) {
            var_dump('$item', $item);
        }
    }

    private function collectWarehouseConfigs()
    {
        $management = $this->_objectManager->create(\Plenty\Stock\Model\Client\ConfigManagementInterface::class);
        $management->collectWarehouseConfigs();
    }

    private function collectWarehouseDimensionConfigs()
    {
        $management = $this->_objectManager->create(\Plenty\Stock\Model\Client\ConfigManagementInterface::class);
        $management->collectWarehouseDimensionConfigs();
    }

    private function collectWarehouseLocationDetails()
    {
        $management = $this->_objectManager->create(\Plenty\Stock\Model\Client\ConfigManagementInterface::class);
        $management->collectWarehouseLocationDetails();
    }

    private function collectWarehouseLocations()
    {
        $management = $this->_objectManager->create(\Plenty\Stock\Model\Client\ConfigManagementInterface::class);
        $management->collectWarehouseLocations();
    }

    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lon1 *= $pi80;
        $lat2 *= $pi80;
        $lon2 *= $pi80;
        $r = 6372.797; // mean radius of Earth in km  6371000
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;
        return $km;
    }
}
