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
use SoftCommerce\Core\Api\ProfileRepositoryInterface;
use SoftCommerce\PlentyProperty\Profile\PropertyCollectServiceInterface;
use SoftCommerce\PlentyPropertyRestApi\Model\PropertyInterface;
use SoftCommerce\PlentyPropertyRestApi\Model\Request\Property\RelationSearchCriteriaInterface;
use SoftCommerce\PlentyPropertyRestApi\Model\Request\PropertySearchCriteriaBuilderInterface;
use SoftCommerce\PlentyPropertyRestApi\Model\Request\PropertySearchCriteriaInterface;

class Property extends Action
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
        // $this->PropertyRepositoryInterface();
        // $this->propertyGroupCollectServiceInterface();
        // $this->propertyCollectServiceInterface();
        // $this->PropertyRelationCollectManagementInterface();
        // $this->PropertyRelationValueCollectManagementInterface();
        // $this->PropertySelectionCollectManagementInterface();

        // HTTP CLIENT
        // $this->httpPropertyGroupInterface();
        // $this->httpPropertyGroupNameInterface();
        $this->httpPropertyInterface();
        // $this->httpPropertyNamesInterface();
        // $this->httpPropertyRelationInterface();
        // $this->httpPropertyRelationValueInterface();
        // $this->httpPropertySelectionInterface();
    }

    private function PropertyRepositoryInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder->addFilter('attribute_id', 1)->create();
        $object = $this->_objectManager->create(\Plenty\Property\Api\PropertyRepositoryInterface::class);
        // $model = $object->getById(3);
        foreach ($object->getList($searchCriteria)->getItems() as $item) {
            var_dump('item ---', $item->getData());
        }
    }

    private function propertyGroupCollectServiceInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyRestApi\Model\RequestSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setId(27)
            ->setWith([
                \SoftCommerce\PlentyPropertyRestApi\Model\Property\Group::NAMES,
                \SoftCommerce\PlentyPropertyRestApi\Model\Property\Group::OPTIONS
            ])
            ->create();
        $object = $this->_objectManager->create(\SoftCommerce\PlentyProperty\Profile\PropertyGroupCollectServiceInterface::class);
        $object->execute($searchCriteria);
        var_dump('$object message', $object->getResponseStorage()->getData());
    }

    private function propertyCollectServiceInterface()
    {
        // attribute_set: 244,324
        $searchCriteriaBuilder = $this->_objectManager->get(PropertySearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            ->setWith(explode(',', PropertySearchCriteriaInterface::WITH_FILTERS))
            // ->setPage(1)
            // ->setItemsPerPage(2)
            // ->setID(324)
            ->setIds([244,247], PropertySearchCriteriaBuilderInterface::COMPARISON_OPERATOR_IN, 'id')
            ->create();

        // $searchCriteria = null;
        $object = $this->_objectManager->create(PropertyCollectServiceInterface::class);
        $object->execute($searchCriteria);
        var_dump('$object message', $object->getResponseStorage()->getData());
    }

    private function PropertyRelationCollectManagementInterface()
    {
        $with = RelationSearchCriteriaInterface::WITH_FILTER;
        $with = 'values,selectionValues';
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyPropertyRestApi\Model\Request\Property\RelationSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            ->setWith(explode(',', $with))
            // ->setId(4053) // 4454 / 4456
            ->setTargetId([34595], RelationSearchCriteriaInterface::COMPARISON_OPERATOR_IN)
            ->create();

        $object = $this->_objectManager->create(\SoftCommerce\PlentyProperty\Profile\PropertyRelationCollectServiceInterface::class);
        $object->execute($searchCriteria);
        // var_dump('getResponseStorage', $object->getResponseStorage()->getData());
        var_dump('getMessageStorage', $object->getMessageStorage()->getData());
    }

    private function PropertyRelationValueCollectManagementInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Plenty\Property\Rest\Request\Property\Relation\ValueSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            // ->setItemsPerPage(1)
            ->setID(6562) // 4005, 4008
            ->create();

        $object = $this->_objectManager->create(\Plenty\Property\Api\PropertyRelationValueCollectManagementInterface::class);
        $object->execute($searchCriteria);
        var_dump('$object message', $object->getResponseStorage()->getData());
    }

    private function PropertySelectionCollectManagementInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Plenty\Property\Rest\Request\Property\SelectionSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            // ->setItemsPerPage(1)
            // ->setID(603) // 4005, 4008
            ->create();

        $object = $this->_objectManager->create(\Plenty\Property\Api\PropertySelectionCollectManagementInterface::class);
        $object->execute($searchCriteria);
        var_dump('$object message', $object->getResponseStorage()->getData());
    }

    private function httpPropertyGroupInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyRestApi\Model\RequestSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(27)
            ->setWith([
                \SoftCommerce\PlentyPropertyRestApi\Model\Property\Group::NAMES,
                \SoftCommerce\PlentyPropertyRestApi\Model\Property\Group::OPTIONS
                ])
            ->create();
        $client = $this->_objectManager->create(\SoftCommerce\PlentyPropertyRestApi\Model\Property\GroupInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpPropertyGroupNameInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyRestApi\Model\RequestSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            // ->setId(29)
            ->create();
        $client = $this->_objectManager->create(\SoftCommerce\PlentyPropertyRestApi\Model\Property\Group\NameInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpPropertyInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(PropertySearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            ->setTypeIdentifier('item')
            // ->setId(244) // 244, 245,
            ->setWith(explode(',', PropertySearchCriteriaInterface::WITH_FILTERS))
            // ->setIds([247], PropertySearchCriteriaBuilderInterface::COMPARISON_OPERATOR_IN, 'id')
            ->create();
        $client = $this->_objectManager->create(PropertyInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpPropertyNamesInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Plenty\Property\Rest\Request\Property\NameSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(260)
            ->create();

        $client = $this->_objectManager->create(\Plenty\Property\Rest\Property\NameInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection size >> ' . $collection->getSize());
        foreach ($collection as $item) {
            var_dump('item', $item->getData());
        }
    }

    private function httpPropertyRelationInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\SoftCommerce\PlentyPropertyRestApi\Model\Request\Property\RelationSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            ->setWith(explode(',', RelationSearchCriteriaInterface::WITH_FILTER))
            // ->setId(4052)
            ->setTargetId([34494], RelationSearchCriteriaInterface::COMPARISON_OPERATOR_IN)
            ->create();

        $client = $this->_objectManager->create(\SoftCommerce\PlentyPropertyRestApi\Model\Property\RelationInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection', $collection->toArray());
    }

    private function httpPropertyRelationValueInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Plenty\Property\Rest\Request\Property\Relation\ValueSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            ->setId(6180)
            ->create();

        $client = $this->_objectManager->create(\Plenty\Property\Rest\Property\Relation\ValueInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection size >> ' . $collection->getSize());
        foreach ($collection as $item) {
            var_dump('item', $item->getData());
        }
    }

    private function httpPropertySelectionInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->get(\Plenty\Property\Rest\Request\Property\SelectionSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setShouldPaginate(true)
            // ->setPropertyId(279)
            // ->setSelectionRelationId(603)
            ->setRelationTargetId(34494)
            // ->setId(603)
            ->create();

        $client = $this->_objectManager->create(\Plenty\Property\Rest\Property\SelectionInterface::class);
        $collection = $client->getList($searchCriteria);
        var_dump('$collection size >> ' . $collection->getSize());
        foreach ($collection as $item) {
            var_dump('item', $item->getData());
        }
    }

}
