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
use Magento\Framework\ObjectManagerInterface;
use SoftCommerce\Core\Model\Catalog\SkuStorageInterfaceFactory;
use SoftCommerce\PlentyItemProfile\Model\Config\ApiConfigInterfaceFactory;
use SoftCommerce\PlentyItemProfile\Model\ItemExportServiceInterfaceFactory;
use SoftCommerce\PlentyItemProfile\Model\ItemImportServiceInterface;
use SoftCommerce\PlentyItemProfile\Model\ItemImportServiceInterfaceFactory;
use SoftCommerce\PlentyItemProfile\Model\PimCollectServiceInterface;
use SoftCommerce\PlentyItemProfile\Model\PimScrollCollectServiceInterface;
use SoftCommerce\PlentyItemRestApi\Model\Request\ItemSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyItemRestApi\Model\Request\ItemSearchCriteriaInterface;
use SoftCommerce\PlentyItemRestApi\Model\Request\Pim\VariationSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyItemRestApi\Model\Request\Pim\VariationSearchCriteriaInterface;
use SoftCommerce\PlentyRestApi\Model\RequestSearchCriteriaInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;

class Item extends Action
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
        var_dump(__METHOD__);

        // SERVICES ::: PROCESS
        // $this->productImportService();
        // $this->productExportService();

        // SERVICES ::: COLLECT
        // $this->pimCollectServiceInterface();
        // $this->pimScrollCollectServiceInterface();
        // $this->ItemVariationRelationInterface();
        // $this->ItemCollectManagementInterface();
        // $this->VariationCollectManagementInterface();
        // $this->profileCollectClientData();

        // HTTP CLIENT TEST
        // $this->httpItemInterface();
        // $this->httpPimVariationInterface();
        // $this->httpVariation();

        // CRON JOB
        // $this->cronCollectItems();
        // $this->cronItemExport();
        // $this->cronItemImport();

        // CONFIGS REPOSITORY NEW
        // $this->clientConfigStoreRepository();
        // $this->clientConfigItemBarcodeRepository();
        // $this->clientConfigItemSalesPriceRepository();

        // CONFIGS COLLECT
        // $this->collectClientConfigItemBarcodes();
        // $this->collectItemSalesPriceConfigs();
        // $this->collectConfigurationTest();

        // Various functions
        // $this->getProductIdSkuPairByVariationId();
        // $this->getProductVariationIdBySku();
        // $this->getVariationIdBySku();
        // $this->skuStorageInterface();

        //
        $this->testItemProductMapping();
    }

    private function productImportService()
    {
        $ids = [8358]; // simple: 8409 | bundle: 8378 | config: 8398, 8399, 8403, 8341
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute(ItemImportServiceInterface::TYPE_ID, 'entity_id');
        $serviceFactory = $this->_objectManager->get(ItemImportServiceInterfaceFactory::class);
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        $service->execute($searchCriteria);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function productExportService()
    {
        $ids = [2159]; // simple: 2146 | bundle: 8378 | config: 2147
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_item_export', 'entity_id');
        $serviceFactory = $this->_objectManager->get(ItemExportServiceInterfaceFactory::class);
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        $service->execute($searchCriteria);

        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function ItemCollectManagementInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(8369)
            ->setWith([
                \SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaInterface::WITH_ITEM_CROSS_SELLING,
                \SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaInterface::WITH_ITEM_IMAGES,
                \SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaInterface::WITH_ITEM_SHIPPING_PROFILES,
                \SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaInterface::WITH_EBAY_TITLES,
                \SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaInterface::WITH_VARIATIONS,
                \SoftCommerce\PlentyItem\Rest\Request\ItemSearchCriteriaInterface::WITH_ITEM_PROPERTIES
            ])
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            // ->setPage(1)
            // ->setItemsPerPage(2)
            ->create();

        $manager = $this->_objectManager->get(\SoftCommerce\PlentyItem\Api\ItemCollectManagementInterface::class);
        $manager->execute($searchCriteria);
        var_dump('imported ids', $manager->getDataStorage()->getData());
    }

    private function pimCollectServiceInterface()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_item_import', 'entity_id');
        $apiConfigFactory = $this->_objectManager->get((ApiConfigInterfaceFactory::class));
        $apiConfig = $apiConfigFactory->create(['profileId' => (int) $profileId]);
        // 34494 [34494, 34449] // config size: 8398
        $variationIds = [8411];
        $itemIds = [8399]; // simple: 8409,8369 | bundle: 8378 | config: 8358 | config size: 8398
        $searchCriteriaBuilder = $this->_objectManager->get(VariationSearchCriteriaBuilderInterface::class);
        $withFilter = $apiConfig->isUserDefinedSearchCriteria() ? $apiConfig->getPimSearchCriteria() : $searchCriteriaBuilder->getWithSearchCriteria();
        // $withFilter = $searchCriteriaBuilder->getWithSearchCriteria();
        $searchCriteria = $searchCriteriaBuilder
            // ->setIds($variationIds)
            ->setItemIds($itemIds)
            // ->setNumber('sku')
            ->setWith($withFilter)
            ->setItemsPerPage(2)
            /* ->setWith([
                VariationSearchCriteriaInterface::WITH_ADDITIONAL_SKUS,
                VariationSearchCriteriaInterface::WITH_ATTRIBUTE_VALUES,
                VariationSearchCriteriaInterface::WITH_ATTRIBUTE_VALUES_ATTRIBUTE,
                VariationSearchCriteriaInterface::WITH_ATTRIBUTE_VALUES_ATTRIBUTE_VALUE,
                VariationSearchCriteriaInterface::WITH_BARCODES,
                VariationSearchCriteriaInterface::WITH_BARCODES_BARCODE,
                VariationSearchCriteriaInterface::WITH_CATEGORIES,
                VariationSearchCriteriaInterface::WITH_CATEGORIES_CATEGORY,
                // VariationSearchCriteriaInterface::WITH_CATEGORIES_CATEGORY_BRANCH,
                VariationSearchCriteriaInterface::WITH_BASE,
                VariationSearchCriteriaInterface::WITH_BASE_ITEM,
                VariationSearchCriteriaInterface::WITH_BASE_ITEM_SERIAL_NUMBER,
                VariationSearchCriteriaInterface::WITH_BASE_FEEDBACK,
                // VariationSearchCriteriaInterface::WITH_BASE_CHARACTERISTICS,
                VariationSearchCriteriaInterface::WITH_BASE_CROSS_SELLS,
                VariationSearchCriteriaInterface::WITH_BASE_TEXTS,
                VariationSearchCriteriaInterface::WITH_BASE_AVAILABILITY,
                VariationSearchCriteriaInterface::WITH_BASE_IMAGES,
                VariationSearchCriteriaInterface::WITH_BASE_SHIPPING_PROFILES,
                VariationSearchCriteriaInterface::WITH_BASE_STOCK,
                VariationSearchCriteriaInterface::WITH_BASE_STOCK_STORAGE_LOCATIONS,
                VariationSearchCriteriaInterface::WITH_BUNDLE_COMPONENTS,
                VariationSearchCriteriaInterface::WITH_CLIENTS,
                VariationSearchCriteriaInterface::WITH_DEFAULT_CATEGORIES,
                // VariationSearchCriteriaInterface::WITH_DEFAULT_CATEGORIES_CATEGORY,
                VariationSearchCriteriaInterface::WITH_IMAGES, // image linked to variation
                // VariationSearchCriteriaInterface::WITH_IMAGES_IMAGE,
                VariationSearchCriteriaInterface::WITH_MARKETS,
                VariationSearchCriteriaInterface::WITH_MARKET_IDENT_NUMBER,
                VariationSearchCriteriaInterface::WITH_SALES_PRICES,
                VariationSearchCriteriaInterface::WITH_SALES_PRICES_SALES_PRICE,
                VariationSearchCriteriaInterface::WITH_SKUS,
                VariationSearchCriteriaInterface::WITH_SUPPLIER,
                VariationSearchCriteriaInterface::WITH_SUPPLIER_SUPPLIER,
                // VariationSearchCriteriaInterface::WITH_TIMESTAMPS,
                VariationSearchCriteriaInterface::WITH_WAREHOUSES,
                VariationSearchCriteriaInterface::WITH_WAREHOUSES_WAREHOUSE,
                VariationSearchCriteriaInterface::WITH_UNIT,
                VariationSearchCriteriaInterface::WITH_UNIT_UNIT,
                VariationSearchCriteriaInterface::WITH_TAGS,
                VariationSearchCriteriaInterface::WITH_TAGS_TAG,
                VariationSearchCriteriaInterface::WITH_PROPERTIES,
                // VariationSearchCriteriaInterface::WITH_PROPERTIES_PROPERTY,
                VariationSearchCriteriaInterface::WITH_COMMENTS
            ]) */
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            ->create();

        try {
            $manager = $this->_objectManager->get(PimCollectServiceInterface::class);
            $manager->execute($searchCriteria);
            var_dump('ID response', $manager->getResponseStorage()->getData());
            var_dump('Message response', $manager->getMessageStorage()->getData());
        } catch (\Exception $e) {
            var_dump('e -- ', $e->getMessage());
        }
    }

    private function pimScrollCollectServiceInterface()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_item_import', 'entity_id');
        $apiConfigFactory = $this->_objectManager->get((ApiConfigInterfaceFactory::class));
        $apiConfig = $apiConfigFactory->create(['profileId' => (int) $profileId]);
        // 34494 [34494, 34449] // config size: 8398
        $variationIds = [8411];
        $itemIds = [8409, 8408]; // simple: 8409,8369 | bundle: 8378 | config: 8358 | config size: 8398
        $searchCriteriaBuilder = $this->_objectManager->get(VariationSearchCriteriaBuilderInterface::class);
        $withFilter = $apiConfig->isUserDefinedSearchCriteria() ? $apiConfig->getPimSearchCriteria() : $searchCriteriaBuilder->getWithSearchCriteria();
        // $withFilter = $searchCriteriaBuilder->getWithSearchCriteria();
        var_dump('$withFilter', $withFilter);
        $searchCriteria = $searchCriteriaBuilder
            // ->setIds($variationIds)
            // ->setItemIds($itemIds)
            // ->setNumber('sku')
            ->setWith($withFilter)
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            ->create();

        try {
            $manager = $this->_objectManager->get(PimScrollCollectServiceInterface::class);
            $manager->execute($searchCriteria);
            var_dump('ID response', $manager->getResponseStorage()->getData());
            var_dump('Message response', $manager->getMessageStorage()->getData());
        } catch (\Exception $e) {
            var_dump('e -- ', $e->getMessage());
        }
    }

    private function VariationCollectManagementInterface()
    {
        // 34494 [34449, 34450]
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(34494)
            ->setWith([
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_PROPERTIES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_PROPERTIES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_BARCODES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_BUNDLE_COMPONENTS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_COMPONENT_BUNDLES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_SALES_PRICES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_MARKET_ITEM_NUMBERS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_CATEGORIES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_CLIENTS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_MARKETS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_DEFAULT_CATEGORY,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_SUPPLIERS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_WAREHOUSES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_IMAGES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_ITEM_IMAGES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_ATTRIBUTE_VALUES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_SKUS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_ADDITIONAL_SKUS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_UNIT,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_PARENT,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_ITEM,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_STOCK,
            ])
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            // ->setPage(1)
            // ->setItemsPerPage(2)
            ->create();

        try {
            $manager = $this->_objectManager->get(\SoftCommerce\PlentyItem\Api\VariationCollectManagementInterface::class);
            $manager->execute($searchCriteria);
            var_dump('imported ids', $manager->getDataStorage()->getData());
        } catch (\Exception $e) {
            var_dump('error', $e->getMessage());
        }
    }

    private function httpItemInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(ItemSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(8369)
            ->setWith([
                ItemSearchCriteriaInterface::WITH_ITEM_CROSS_SELLING,
                ItemSearchCriteriaInterface::WITH_ITEM_IMAGES,
                ItemSearchCriteriaInterface::WITH_ITEM_SHIPPING_PROFILES,
                ItemSearchCriteriaInterface::WITH_EBAY_TITLES,
                ItemSearchCriteriaInterface::WITH_VARIATIONS,
                ItemSearchCriteriaInterface::WITH_ITEM_PROPERTIES
            ])
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            ->create();
        $client = $this->_objectManager->get(\SoftCommerce\PlentyItemRestApi\Model\ItemInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpPimVariationInterface()
    {
        // 34494 [34449, 34450]
        $searchCriteriaBuilder = $this->_objectManager->get(VariationSearchCriteriaBuilderInterface::class);
        $variationIds = [34322];
        $itemIds = [8362, 8332, 8369]; // 34595, 34594
        $searchCriteria = $searchCriteriaBuilder
            // ->setIds($variationIds, RequestSearchCriteriaInterface::COMPARISON_OPERATOR_IN)
            ->setItemIds($itemIds)
            ->setWith([VariationSearchCriteriaInterface::WITH_BASE])
            /*
            ->setWith([
                VariationSearchCriteriaInterface::WITH_ADDITIONAL_SKUS,
                VariationSearchCriteriaInterface::WITH_ATTRIBUTE_VALUES,
                VariationSearchCriteriaInterface::WITH_ATTRIBUTE_VALUES_ATTRIBUTE,
                VariationSearchCriteriaInterface::WITH_ATTRIBUTE_VALUES_ATTRIBUTE_VALUE,
                VariationSearchCriteriaInterface::WITH_BARCODES,
                VariationSearchCriteriaInterface::WITH_BARCODES_BARCODE,
                VariationSearchCriteriaInterface::WITH_CATEGORIES,
                VariationSearchCriteriaInterface::WITH_CATEGORIES_CATEGORY,
                // VariationSearchCriteriaInterface::WITH_CATEGORIES_CATEGORY_BRANCH,
                VariationSearchCriteriaInterface::WITH_BASE,
                VariationSearchCriteriaInterface::WITH_BASE_ITEM,
                VariationSearchCriteriaInterface::WITH_BASE_ITEM_SERIAL_NUMBER,
                VariationSearchCriteriaInterface::WITH_BASE_FEEDBACK,
                // VariationSearchCriteriaInterface::WITH_BASE_CHARACTERISTICS,
                VariationSearchCriteriaInterface::WITH_BASE_CROSS_SELLS,
                VariationSearchCriteriaInterface::WITH_BASE_TEXTS,
                VariationSearchCriteriaInterface::WITH_BASE_AVAILABILITY,
                VariationSearchCriteriaInterface::WITH_BASE_IMAGES,
                VariationSearchCriteriaInterface::WITH_BASE_SHIPPING_PROFILES,
                VariationSearchCriteriaInterface::WITH_BASE_STOCK,
                VariationSearchCriteriaInterface::WITH_BASE_STOCK_STORAGE_LOCATIONS,
                VariationSearchCriteriaInterface::WITH_BUNDLE_COMPONENTS,
                VariationSearchCriteriaInterface::WITH_CLIENTS,
                VariationSearchCriteriaInterface::WITH_DEFAULT_CATEGORIES,
                // VariationSearchCriteriaInterface::WITH_DEFAULT_CATEGORIES_CATEGORY,
                VariationSearchCriteriaInterface::WITH_IMAGES, // image linked to variation
                // VariationSearchCriteriaInterface::WITH_IMAGES_IMAGE,
                VariationSearchCriteriaInterface::WITH_MARKETS,
                VariationSearchCriteriaInterface::WITH_MARKET_IDENT_NUMBER,
                VariationSearchCriteriaInterface::WITH_SALES_PRICES,
                VariationSearchCriteriaInterface::WITH_SALES_PRICES_SALES_PRICE,
                VariationSearchCriteriaInterface::WITH_SKUS,
                VariationSearchCriteriaInterface::WITH_SUPPLIER,
                VariationSearchCriteriaInterface::WITH_SUPPLIER_SUPPLIER,
                // VariationSearchCriteriaInterface::WITH_TIMESTAMPS,
                VariationSearchCriteriaInterface::WITH_WAREHOUSES,
                VariationSearchCriteriaInterface::WITH_WAREHOUSES_WAREHOUSE,
                VariationSearchCriteriaInterface::WITH_UNIT,
                VariationSearchCriteriaInterface::WITH_UNIT_UNIT,
                VariationSearchCriteriaInterface::WITH_TAGS,
                VariationSearchCriteriaInterface::WITH_TAGS_TAG,
                VariationSearchCriteriaInterface::WITH_PROPERTIES,
                // VariationSearchCriteriaInterface::WITH_PROPERTIES_PROPERTY,
                VariationSearchCriteriaInterface::WITH_COMMENTS
            ]) */
            // ->setUpdatedAt('2022-05-11T13:20:00+01:00')
            // ->setItemUpdatedAt('2022-05-11T12:37:51+00:00')
            // ->setUpdatedAtBetween('x-between')
            // ->setItemsPerPage(10)
            ->create();

        $client = $this->_objectManager->create(\SoftCommerce\PlentyItemRestApi\Model\Pim\VariationInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpVariation()
    {
        // bundle: 34529 // simple: 34494
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(34494)
            ->setWith([
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_PROPERTIES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_PROPERTIES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_BARCODES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_BUNDLE_COMPONENTS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_COMPONENT_BUNDLES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_SALES_PRICES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_MARKET_ITEM_NUMBERS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_CATEGORIES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_CLIENTS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_MARKETS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_DEFAULT_CATEGORY,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_SUPPLIERS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_WAREHOUSES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_IMAGES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_ITEM_IMAGES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_ATTRIBUTE_VALUES,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_SKUS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_VARIATION_ADDITIONAL_SKUS,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_UNIT,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_PARENT,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_ITEM,
                \SoftCommerce\PlentyItem\Rest\Request\VariationSearchCriteriaInterface::WITH_STOCK,
            ])
            // ->setUpdatedAtFrom('xxxx')
            // ->setUpdatedAtBetween('x-between')
            // ->setPage(1)
            // ->setItemsPerPage(2)
            ->create();

        $client = $this->_objectManager->create(\SoftCommerce\PlentyItem\Rest\VariationInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection size >> ' . $collection->getSize());
        foreach ($collection as $item) {
            // $variation = $item->getData('base');
            // var_dump('$variation', $variation['item']);
            var_dump('item', $item->getData());

        }
    }

    private function collectConfigurationTest()
    {
        $controller = $this->_objectManager->get(\SoftCommerce\PlentyItemProfile\Controller\Adminhtml\Client\CollectConfiguration::class);
        $controller->execute();
    }

    private function cronCollectItems()
    {
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute(ItemImportServiceInterface::TYPE_ID, 'entity_id');
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemProfileSchedule\Model\ItemCollect::class);
        $response = $model->execute((int) $profileId);
    }

    private function cronItemExport()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemProfileSchedule\Cron\Backend\ItemExport::class);
        $response = $model->execute();
    }

    private function cronItemImport()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemProfileSchedule\Cron\Backend\ItemImport::class);
        $model->execute();
    }

    private function categoryExportCron()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCategoryProfileSchedule\Cron\Backend\CategoryExport::class);
        $model->execute();
    }

    private function categoryImportCron()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCategoryProfileSchedule\Cron\Backend\CategoryImport::class);
        $model->execute();
    }

    private function getProductIdSkuPairByVariationId()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItem\Model\ResourceModel\Variation::class);
        $result = $model->getProductIdSkuPairByVariationId(31009);
        var_dump('$result', $result);
    }

    private function getProductVariationIdBySku()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItem\Model\ResourceModel\Variation::class);
        $result = $model->getProductVariationIdBySku('24-MB06');
        var_dump('$result', $result);
    }

    private function getVariationIdBySku()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItem\Model\GetVariationIdBySku::class);
        $result = $model->execute('24-MB02');
        var_dump('$result', $result);
    }

    private function skuStorageInterface()
    {
        $factory = $this->_objectManager->get(SkuStorageInterfaceFactory::class);
        $model = $factory->create(); // ['attributes' => ['test']]
        $data = $model->getData('24-MB04');
        var_dump('data', $data);
    }

    private function testItemProductMapping()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyItemProfile\Console\Command\ItemProductMapping::class);
        $model->test();
    }
}
