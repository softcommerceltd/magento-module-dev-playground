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
use SoftCommerce\PlentyAttribute\Profile\AttributeCollectServiceInterface;
use SoftCommerce\PlentyAttribute\Profile\AttributeExportServiceInterface;
use SoftCommerce\PlentyAttribute\Profile\ManufacturerCollectServiceInterface;
use SoftCommerce\PlentyAttributeRestApi\Model\Request\AttributeSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyAttributeRestApi\Model\Request\AttributeSearchCriteriaInterface;
use SoftCommerce\PlentyAttributeRestApi\Model\Request\ManufacturerSearchCriteriaBuilderInterface;

class Attribute extends Action
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
        // PLENTY MODEL TESTING
        // $this->AttributeRepositoryInterface();
        // $this->attributeCollectServiceInterface();
        // $this->manufacturerRepositoryInterface();
        $this->manufacturerCollectServiceInterface();
        // $this->profileClientEnquire();
        // $this->profileCollectClientData();

        // IMPORT / EXPORT
        // $this->attributeExportServiceInterface();

        // HTTP CLIENT
        // $this->httpAttributeInterface();
        // $this->httpManufactureInterface();
    }

    private function AttributeRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('attribute_id', 1)->create();
        $object = $this->_objectManager->create(\SoftCommerce\PlentyAttribute\Api\AttributeRepositoryInterface::class);
        // $model = $object->getById(3);
        // var_dump('$object', $model->getData());
        foreach ($object->getList($searchCriteria)->getItems() as $item) {
            // var_dump('item ---', $item->getData());
        }
    }

    private function attributeCollectServiceInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(AttributeSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setWith([
                AttributeSearchCriteriaInterface::WITH_FILTER_ATTRIBUTE_NAMES,
                AttributeSearchCriteriaInterface::WITH_FILTER_VALUES,
                AttributeSearchCriteriaInterface::WITH_FILTER_MAPS
            ])
            ->setIds([166], AttributeSearchCriteriaBuilderInterface::COMPARISON_OPERATOR_IN)
            ->create();

        $collectService = $this->_objectManager->create(AttributeCollectServiceInterface::class);
        $collectService->execute($searchCriteria);
        var_dump('storage data.', $collectService->getResponseStorage()->getData());
    }

    private function manufacturerRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('attribute_id', 4)->create();
        $object = $this->_objectManager->create(\SoftCommerce\PlentyAttribute\Api\ManufacturerRepositoryInterface::class);
        // $model = $object->getById(4);
        // var_dump('$object', $model->getData());
        // return;
        foreach ($object->getList($searchCriteria)->getItems() as $item) {
            var_dump('item ---', $item->getData());
        }
    }

    private function manufacturerCollectServiceInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(ManufacturerSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setWith(['commissions', 'externals'])
            ->setId(4)
            ->create();

        $manager = $this->_objectManager->get(ManufacturerCollectServiceInterface::class);
        $manager->execute($searchCriteria);
    }

    private function httpAttributeInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(AttributeSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setWith([
                AttributeSearchCriteriaInterface::WITH_FILTER_ATTRIBUTE_NAMES,
                AttributeSearchCriteriaInterface::WITH_FILTER_VALUES,
                AttributeSearchCriteriaInterface::WITH_FILTER_MAPS
            ])
            ->setIds([1, 2], AttributeSearchCriteriaInterface::COMPARISON_OPERATOR_IN)
            ->create();

        $client = $this->_objectManager->create(\SoftCommerce\PlentyAttributeRestApi\Model\AttributeInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpManufactureInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(ManufacturerSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder->setWith(['commissions', 'externals'])
            ->setId(4)
            ->create();

        $clientFactory = $this->_objectManager->create(\SoftCommerce\PlentyAttributeRestApi\Model\ManufacturerInterfaceFactory::class);
        $client = $clientFactory->create(['clientId' => 31241]);
        $collection = $client->getList($searchCriteria);
        foreach ($collection as $item) {
            var_dump('item', $item->getData());
        }
    }

    private function attributeExportServiceInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setWith([
                AttributeSearchCriteriaInterface::WITH_FILTER_ATTRIBUTE_NAMES,
                AttributeSearchCriteriaInterface::WITH_FILTER_VALUES,
                AttributeSearchCriteriaInterface::WITH_FILTER_MAPS
            ])
            ->setIds([1, 2], AttributeSearchCriteriaBuilderInterface::COMPARISON_OPERATOR_IN)
            ->create();

        $collectService = $this->_objectManager->create(AttributeExportServiceInterface::class);
        $collectService->execute($searchCriteria);
        var_dump('storage data.', $collectService->getResponseStorage()->getData());
    }
}
