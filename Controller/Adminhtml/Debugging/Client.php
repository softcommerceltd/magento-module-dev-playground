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
use Magento\Framework\ObjectManagerInterface;
use SoftCommerce\PlentyLog\RestApi\Request\LogSearchCriteriaBuilderInterface;

class Client extends Action
{
    public function __construct(
        Action\Context $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function execute()
    {
        // $this->clientSystemConfig();

        // DOCUMENT
        // $this->httpDocumentInterface();

        // WEB STORE
        // $this->httpWebStoreInterface();
        // $this->webStoreCollectManagementInterface();
        // $this->storeRepositoryInterface();

        // WEB STORE LOCATIONS
        // $this->httpWebStoreLocations();
        // $this->webStoreLocationCollectManagement();
        // $this->storeLocationRepositoryInterface();

        // VAT CONFIGS
        // $this->httpVats();
        // $this->vatCollectManagement();
        // $this->vatRepositoryInterface();

        // CUSTOMER CONFIGS
        // $this->httpCustomerClass();
        // $this->customerClassCollectManagement();
        // $this->customerClassRepositoryInterface();

        // ADDRESS OTPTION TYPE
        // $this->httpAddressOptionType();
        // $this->addressOptionTypeCollectManagement();
        $this->addressOptionTypeRepositoryInterface();
        // $this->addressOptionTypeController();

        // ITEM AVAILABILITY CONFIG
        // $this->httpAvailabilityInterface();
        // $this->availabilityCollectManagement();
        // $this->availabilityRepositoryInterface();

        // ITEM BARCODE CONFIG
        // $this->httpBarcodeInterface();
        // $this->barcodeCollectManagement();
        // $this->barcodeRepositoryInterface();

        // ITEM SALES PRICE CONFIG
        // $this->httpSalesPriceInterface();
        // $this->salesPriceCollectManagement();
        // $this->salesPriceRepositoryInterface();

        // ITEM UNIT CONFIG
        // $this->httpUnitInterface();
        // $this->unitCollectManagement();
        // $this->unitRepositoryInterface();

        // ORDER PAYMENT METHODS CONFIG
        // $this->httpPaymentMethods();
        // $this->paymentMethodCollectManagement();
        // $this->paymentMethodRepositoryInterface();

        // ORDER PROPERTY CONFIG
        // $this->httpPropertyInterface();
        // $this->propertyCollectManagement();
        // $this->propertyRepositoryInterface();

        // ORDER REFERRER CONFIG
        // $this->httpReferrerInterface();
        // $this->referrerCollectManagement();
        // $this->referrerRepositoryInterface();

        // ORDER SHIPPING COUNTRY CONFIG
        // $this->httpShippingCountryInterface();
        // $this->shippingCountryCollectManagement();
        // $this->shippingCountryRepositoryInterface();

        // ORDER SHIPPING PROFILE CONFIG
        // $this->httpShippingProfileInterface();
        // $this->shippingProfileCollectManagement();
        // $this->shippingProfileRepositoryInterface();

        // ORDER STATUS CONFIG
        // $this->httpStatusInterface();
        // $this->statusCollectManagement();
        // $this->statusRepositoryInterface();

        // STOCK WAREHOUSE CONFIG
        // $this->httpWarehouseInterface();
        // $this->warehouseCollectManagement();
        // $this->warehouseRepositoryInterface();

        // STOCK WAREHOUSE DIMENSIONS CONFIG
        // $this->httpWarehouseDimensionInterface();
        // $this->warehouseDimensionCollectManagement();
        // $this->warehouseDimensionRepositoryInterface();

        // STOCK WAREHOUSE LOCATIONS CONFIG
        // $this->HttpWarehouseLocationInterface();
        // $this->warehouseLocationCollectManagement();
        // $this->warehouseLocationRepositoryInterface();

        // STOCK WAREHOUSE LOCATION DETAILS CONFIG
        // $this->httpWarehouseLocationDetailsInterface();
        // $this->warehouseLocationDetailsCollectManagement();
        // $this->warehouseLocationDetailsRepositoryInterface();

        // LOG HTTP / COLLECT SERVICE
        // $this->restApiLogInterface();
        // $this->logCollectServiceInterface();
        // $this->logProcessServiceInterface();
    }

    private function clientSystemConfig()
    {
        $module = $this->_objectManager->get(\SoftCommerce\PlentyClient\Model\ClientConfigInterface::class);
        var_dump($module->getPassword());
        var_dump($module->getUrl());
        $module->setPassword('test AAA new pass');
        $module->setAccessToken('test access # token');
        // var_dump($module->getClientPassword());
        var_dump('getTokenLastUpdatedAt', $module->getTokenLastUpdatedAt());

        return;
        $module->saveData(
            [
                [
                    'path' => 'plentymarkets/client_config/password',
                    'value' => 'BBB test pass --'
                ],
                [
                    'path' => 'plentymarkets/client_config/access_token',
                    'value' => 'zzzs test token --'
                ]
            ]
        );

        var_dump('getTokenLastUpdatedAt', $module->getTokenLastUpdatedAt());
        var_dump($module->getPassword());
        var_dump($module->getName());
    }

    private function httpDocumentInterface()
    {
        $client = $this->_objectManager->get(\SoftCommerce\PlentyClientRestApi\Model\DocumentInterface::class);
        try {
            $response = $client->getById(667);
            var_dump('$response', $response);
        } catch (\Exception $e) {
            var_dump('error', $e->getMessage());
        }
    }

    private function httpWebStoreInterface()
    {
        $client = $this->_objectManager->get(\SoftCommerce\PlentyClientRestApi\Model\WebStoreInterface::class);
        $list = $client->getList();
        var_dump('$list', $list->toArray());
    }

    private function webStoreCollectManagementInterface()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyClient\Model\Config\WebStoreCollectManagement::class);
        $object->execute();
    }

    private function storeRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyClient\Model\Config\StoreRepositoryInterface::class);
        $list = $repository->getList();

        var_dump('$list', $list->toArray());
        /** @var \SoftCommerce\PlentyClient\Model\Config\StoreInterface $item */
        foreach ($list->getItems() as $item) {
            var_dump('item', $item->getDefaultShippingCountryId());
        }
    }

    private function httpWebStoreLocations()
    {
        $factory = $this->_objectManager->get(\SoftCommerce\PlentyClientRestApi\Model\AccountingInterfaceFactory::class);
        $model = $factory->create();
        $storeLocations = $model->getListStoreLocations();
        var_dump('$storeLocations ::', $storeLocations->toArray());
    }

    private function webStoreLocationCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyClient\Model\Config\WebStoreLocationCollectManagement::class);
        $object->execute();
    }

    private function storeLocationRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyClient\Model\Config\StoreLocationRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpVats()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyClientRestApi\Model\AccountingInterface::class);
        $vatList = $model->getListVat();
        var_dump('$vatList ::', $vatList->toArray());
    }

    private function vatCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyClient\Model\Config\VatCollectManagement::class);
        $object->execute();
    }

    private function vatRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyClient\Model\Config\VatRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpCustomerClass()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCustomerRestApi\Model\Config\CustomerClassInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function customerClassCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyCustomerClient\Model\CustomerClassCollectManagement::class);
        $object->execute();
    }

    private function customerClassRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyCustomerClient\Api\CustomerClassRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpAddressOptionType()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCustomerRestApi\Model\Config\AddressOptionTypeInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function addressOptionTypeCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyCustomerClient\Model\AddressOptionTypeCollectManagement::class);
        $object->execute();
    }

    private function addressOptionTypeRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyCustomerClient\Api\AddressOptionTypeRepositoryInterface::class);
        // $list = $repository->getList($searchCriteria);
        $list = $repository->getByName('customer_address_id');
        var_dump('$list', $list->getDefaultName());
    }

    private function addressOptionTypeController()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCustomerProfile\Controller\Adminhtml\Client\CollectConfiguration::class);
        $model->execute();
    }

    private function httpAvailabilityInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemRestApi\Model\Config\AvailabilityInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function availabilityCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Model\AvailabilityCollectManagement::class);
        $object->execute();
    }

    private function availabilityRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Api\AvailabilityRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpBarcodeInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemRestApi\Model\Config\BarcodeInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function barcodeCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Model\BarcodeCollectManagement::class);
        $object->execute();
    }

    private function barcodeRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Api\BarcodeRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpSalesPriceInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemRestApi\Model\Config\SalesPriceInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function salesPriceCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Model\SalesPriceCollectManagement::class);
        $object->execute();
    }

    private function salesPriceRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Api\SalesPriceRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpUnitInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemRestApi\Model\Config\UnitInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function unitCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Model\UnitCollectManagement::class);
        $object->execute();
    }

    private function unitRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 2)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyItemClient\Api\UnitRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpPaymentMethods()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Config\PaymentMethodInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function paymentMethodCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Model\PaymentMethodCollectManagement::class);
        $object->execute();
    }

    private function paymentMethodRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Api\PaymentMethodRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpPropertyInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Config\PropertyInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function propertyCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Model\PropertyCollectManagement::class);
        $object->execute();
    }

    private function propertyRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Api\PropertyRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpReferrerInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Config\ReferrerInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function referrerCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Model\ReferrerCollectManagement::class);
        $object->execute();
    }

    private function referrerRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_Id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Api\ReferrerRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpShippingCountryInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Config\ShippingCountryInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function shippingCountryCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Model\ShippingCountryCollectManagement::class);
        $object->execute();
    }

    private function shippingCountryRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Api\ShippingCountryRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpShippingProfileInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Config\ShippingProfileInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function shippingProfileCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Model\ShippingProfileCollectManagement::class);
        $object->execute();
    }

    private function shippingProfileRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Api\ShippingProfileRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpStatusInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyOrderRestApi\Model\Config\StatusInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function statusCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Model\StatusCollectManagement::class);
        $object->execute();
    }

    private function statusRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyOrderClient\Api\StatusRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpWarehouseInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockRestApi\Model\Config\WarehouseInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list->toArray());
    }

    private function warehouseCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Model\WarehouseCollectManagement::class);
        $object->execute();
    }

    private function warehouseRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_Id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Api\WarehouseRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpWarehouseDimensionInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockRestApi\Model\Config\WarehouseDimensionInterface::class);
        $list = $model->getList(1);
        var_dump('$list ::', $list->toArray());
    }

    private function warehouseDimensionCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Model\WarehouseDimensionCollectManagement::class);
        $object->execute();
    }

    private function warehouseDimensionRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Api\WarehouseDimensionRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function HttpWarehouseLocationInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockRestApi\Model\Config\WarehouseLocationInterface::class);
        $list = $model->getList(1);
        var_dump('$list ::', $list->toArray());
    }

    private function warehouseLocationCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Model\WarehouseLocationCollectManagement::class);
        $object->execute();
    }

    private function warehouseLocationRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Api\WarehouseLocationRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function httpWarehouseLocationDetailsInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyStockRestApi\Model\Config\WarehouseLocationDetailsInterface::class);
        $list = $model->getList();
        var_dump('$list ::', $list);
    }

    private function warehouseLocationDetailsCollectManagement()
    {
        $object = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Model\WarehouseLocationDetailsCollectManagement::class);
        $object->execute();
    }

    private function warehouseLocationDetailsRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('entity_id', 1)->create();
        $repository = $this->_objectManager->create(\SoftCommerce\PlentyStockClient\Api\WarehouseLocationDetailsRepositoryInterface::class);
        $list = $repository->getList();
        var_dump('$list', $list->toArray());
    }

    private function restApiLogInterface()
    {
        $httpClient = $this->_objectManager->get(\SoftCommerce\PlentyLog\RestApi\LogInterface::class);
        $searchCriteriaBuilder = $this->_objectManager->get(LogSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setReferenceType(13)
            // ->setUpdatedAtBetween('2022-05-16')
            ->create();

        $response = $httpClient->getList($searchCriteria);
        var_dump('$response', $response->toArray());
    }

    private function logCollectServiceInterface()
    {
        $service = $this->_objectManager->get(\SoftCommerce\PlentyLog\Model\LogCollectServiceInterface::class);
        $searchCriteriaBuilder = $this->_objectManager->get(LogSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setReferenceType(13)
            ->setUpdatedAtBetween('2022-05-04 12:00:00')
            ->create();

        $service->execute($searchCriteria);
        var_dump('getMessageStorage', $service->getMessageStorage()->getData());
    }

    private function logProcessServiceInterface()
    {
        $service = $this->_objectManager->get(\SoftCommerce\PlentyLog\Model\LogProcessServiceInterface::class);
        $service->execute();
    }

    private function cronLogCollectServiceInterface()
    {
        $service = $this->_objectManager->get(\SoftCommerce\PlentyLog\Cron\Backend\LogCollectService::class);
        $service->execute();
    }

    private function cronLogProcessServiceInterface()
    {
        $service = $this->_objectManager->get(\SoftCommerce\PlentyLog\Cron\Backend\LogProcessService::class);
        $service->execute();
    }

}
