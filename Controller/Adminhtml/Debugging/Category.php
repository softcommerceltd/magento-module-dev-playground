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
use SoftCommerce\Core\Framework\MessageStorage\OutputArrayInterface;
use SoftCommerce\PlentyCategory\Api\CategoryExportQueueManagementInterface;
use SoftCommerce\PlentyCategoryProfile\Model\CategoryCollectServiceInterface;
use SoftCommerce\PlentyCategoryProfile\Model\CategoryCollectServiceInterfaceFactory;
use SoftCommerce\PlentyCategoryProfile\Model\CategoryImportServiceInterface;
use SoftCommerce\PlentyCategoryProfile\Model\CategoryImportServiceInterfaceFactory;
use SoftCommerce\PlentyCategoryRestApi\Model\Request\CategorySearchCriteriaBuilderInterface;
use SoftCommerce\PlentyCategoryRestApi\Model\Request\CategorySearchCriteriaInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;

class Category extends Action
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
        // SERVICE
        $this->categoryCollectService();
        // $this->categoryTreeManagement();
        // $this->categoryExportQueueManagementInterface();
        // $this->categoryExportService();
        // $this->categoryImportService();

        // REST API
        // $this->restCategoryInterface();

        // SCHEDULER
        // $this->categoryExportSchedule();
        // $this->categoryExportCron();
        // $this->categoryImportCron();

        // MODEL TESTING
        // $this->catalogCategoryRepositoryInterface();
        // $this->categoryRepositoryInterface();
        // $this->categoryCollectService();
        // $this->categoryTreeManagement();
        // $this->installClientCategoryAttributeTest();
        // $this->GetClientCategoryIdByCategoryIdInterface();
        // $this->CatalogCategoryManagementInterface();
        // $this->updateCategoryAttribute();

        // Profile Config
        // $this->categoryConfig();
    }

    private function categoryCollectService()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(CategorySearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setIds([964]) // 859,856
            // ->setId(856) // 859
            // ->setName('Gear')
            // ->setLevel(2)
            ->setWith([
                CategorySearchCriteriaInterface::WITH_FILTER_DETAILS,
                CategorySearchCriteriaInterface::WITH_FILTER_CLIENTS,
                CategorySearchCriteriaInterface::WITH_FILTER_BRANCH,
                CategorySearchCriteriaInterface::WITH_FILTER_TAG_RELATION,
                CategorySearchCriteriaInterface::WITH_FILTER_ELMAR_CATEGORIES
            ])
            ->create();

        $service = $this->_objectManager->get(CategoryCollectServiceInterface::class);
        $service->execute($searchCriteria);

        $arrayOutput = $this->_objectManager->get(OutputArrayInterface::class);
        var_dump('response', $service->getEntityIdStorage()->getData());
        var_dump('message', $service->getMessageStorage()->getData());
        var_dump('$arrayOutput', $arrayOutput->execute($service->getMessageStorage()->getData()));
    }

    private function categoryTreeManagement()
    {
        $manager = $this->_objectManager->create(\SoftCommerce\PlentyCategoryProfile\Model\CategoryTreeManagementInterface::class);
        $manager->buildCategoryPath(6, [964]);
        var_dump('response', $manager->getResponseStorage()->getData());
    }

    private function categoryExportQueueManagementInterface()
    {
        $service = $this->_objectManager->get(CategoryExportQueueManagementInterface::class);
        $service->addById([2, 7], true);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function categoryExportService()
    {
        $ids = [72];
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();
        $serviceFactory = $this->_objectManager->get(\SoftCommerce\PlentyCategoryProfile\Model\CategoryExportServiceInterfaceFactory::class);
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute('plenty_category_export', 'entity_id');
        $service = $serviceFactory->create(['data' => ['profile_id' => $profileId]]);
        $service->execute($searchCriteria);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function categoryImportService()
    {
        $ids = [1104]; // 3, 67, 867, 862, 858, 895
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();

        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute(CategoryImportServiceInterface::TYPE_ID, 'entity_id');
        $serviceFactory = $this->_objectManager->get(CategoryImportServiceInterfaceFactory::class);
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        $service->execute($searchCriteria);
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function categoryExportSchedule()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCategoryProfileSchedule\Model\CategoryExport::class);
        $response = $model->execute(11);
        var_dump('$response->getData()', $response->getData());
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

    private function catalogCategoryRepositoryInterface()
    {
        $categoryRepository = $this->_objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
        $category = $categoryRepository->get(3);
        var_dump('$category', $category->getData());

        foreach ($category->getAttributes() as $attribute) {
            var_dump('$attribute', $attribute->getAttributeCode());
        }
    }

    private function categoryRepositoryInterface()
    {
        $categoryRepository = $this->_objectManager->get(\SoftCommerce\PlentyCategory\Api\CategoryRepositoryInterface::class);
        // $category = $categoryRepository->get(1);
        $category = $categoryRepository->getById(31241, 600);
        var_dump('$category', $category->getData());
    }

    private function restCategoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(CategorySearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setIds([856]) // 859,856
            // ->setId(856) // 859
            // ->setName('Gear')
            // ->setLevel(2)
            ->setWith([
                CategorySearchCriteriaInterface::WITH_FILTER_DETAILS,
                CategorySearchCriteriaInterface::WITH_FILTER_CLIENTS,
                CategorySearchCriteriaInterface::WITH_FILTER_BRANCH,
                CategorySearchCriteriaInterface::WITH_FILTER_TAG_RELATION,
                CategorySearchCriteriaInterface::WITH_FILTER_ELMAR_CATEGORIES
            ])
            ->create();

        $client = $this->_objectManager->get(\SoftCommerce\PlentyCategoryRestApi\Model\CategoryInterface::class);
        $category = $client->getList($searchCriteria);
        var_dump('$category', $category->toArray());
        return;
        foreach ($category as $item) {
            var_dump('$item', get_class($item));
            var_dump('item', $item->getData());
        }


        return;
        $searchCriteriaBuilder = $this->_objectManager->create(CategorySearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setIds([855])
            ->setWith([
                CategorySearchCriteriaInterface::WITH_FILTER_DETAILS,
                CategorySearchCriteriaInterface::WITH_FILTER_CLIENTS,
                CategorySearchCriteriaInterface::WITH_FILTER_BRANCH,
                CategorySearchCriteriaInterface::WITH_FILTER_TAG_RELATION,
                CategorySearchCriteriaInterface::WITH_FILTER_ELMAR_CATEGORIES
            ])
            ->create();
        $manager = $this->_objectManager->create(\SoftCommerce\PlentyCategory\Api\CategoryCollectManagementInterface::class);
        $manager->execute($searchCriteria);
    }

    private function categoryConfig()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyCategoryProfile\Model\Config\CategoryConfigInterface::class);
        $model->setProfileId(11);
        $data = $model->getRootCategoryMapping();
        var_dump('data', $data);
    }

    private function installClientCategoryAttributeTest()
    {
        $installer = $this->_objectManager->create(\Plenty\Category\Setup\Patch\Data\InstallCategoryClientAttributes::class);
        $installer->apply();
    }

    private function GetClientCategoryIdByCategoryIdInterface()
    {
        $model = $this->_objectManager->create(\Plenty\Category\Model\GetClientCategoryIdByCategoryIdInterface::class);
        $id = $model->execute(212);
        var_dump('id', $id);
    }

    private function CatalogCategoryManagementInterface()
    {
        $management = $this->_objectManager->create(\Magento\Catalog\Api\CategoryManagementInterface::class);
        $tree = $management->getTree(2);
        var_dump('$tree', $tree->getName());
        foreach ($tree->getChildrenData() as $item) {
            var_dump('item', $item->getName());
        }
    }

    private function updateCategoryAttribute()
    {
        $updateHandler = $this->_objectManager->create(\Magento\Eav\Model\ResourceModel\UpdateHandler::class);
        $updateHandler->execute(
            \Magento\Catalog\Api\Data\CategoryInterface::class,
            [
                'store_id' => 2,
                'entity_id' => 38,
                'attribute_set_id' => 3,
                'plenty_category_id' => 600
            ]
        );
    }
}
