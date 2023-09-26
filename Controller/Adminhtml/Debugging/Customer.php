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
use Magento\Framework\ObjectManagerInterface;
use SoftCommerce\PlentyCustomer\Api\Data\AddressInterface;
use SoftCommerce\PlentyCustomer\Api\Data\Customer\AddressInterface as CustomerAddressInterface;
use SoftCommerce\PlentyCustomer\Model\GetCustomerRawDataInterface;
use SoftCommerce\PlentyCustomerProfile\Model\AddressCollectServiceInterface;
use SoftCommerce\PlentyCustomerProfile\Model\CustomerCollectServiceInterface;
use SoftCommerce\PlentyCustomerProfile\Model\CustomerExportServiceInterface;
use SoftCommerce\PlentyCustomerProfile\Model\CustomerExportServiceInterfaceFactory;
use SoftCommerce\PlentyCustomerProfile\Model\CustomerImportServiceInterface;
use SoftCommerce\PlentyCustomerProfile\Model\CustomerImportServiceInterfaceFactory;
use SoftCommerce\PlentyCustomerRestApi\Model\Request\AddressSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyCustomerRestApi\Model\Request\AddressSearchCriteriaInterface;
use SoftCommerce\PlentyCustomerRestApi\Model\Request\ContactSearchCriteriaBuilderInterface;
use SoftCommerce\PlentyCustomerRestApi\Model\Request\ContactSearchCriteriaInterface;
use SoftCommerce\PlentyOrderProfile\Model\OrderExportServiceInterface;
use SoftCommerce\PlentyOrderProfile\Model\OrderExportServiceInterfaceFactory;
use SoftCommerce\PlentyOrderProfile\Model\OrderImportServiceInterface;
use SoftCommerce\Profile\Model\GetProfileDataByTypeIdInterface;

class Customer extends Action
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
        // COLLECT MANAGEMENT
        // $this->customerCollectServiceInterface();
        // $this->addressCollectServiceInterface();

        // SERVICES
        $this->customerExportService();
        // $this->customerImportService();

        // MODEL TESTING
        // $this->plentyAddressModel();
        // $this->plentyCustomerAddressModel();
        // $this->plentyOrderAddressModel();
        // $this->plentyOrderModel();

        // REST API
        // $this->restAddressInterface();
        // $this->restContactInterface();

        // Customer interfaces
        // $this->GetCustomerRawDataInterface();
    }

    private function customerExportService()
    {
        $ids = [129]; // 90,91,92,93,94,95
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();
        // $searchCriteria = null;
        $serviceFactory = $this->_objectManager->get(CustomerExportServiceInterfaceFactory::class);
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute(CustomerExportServiceInterface::TYPE_ID, 'entity_id');
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        try {
            $service->execute($searchCriteria);
        } catch (\Exception $e) {
            var_dump('error', $e->getMessage());
        }
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        // var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function customerImportService()
    {
        $ids = [1541]; // 1604, 1606, 1607, 1609
        $searchCriteriaBuilder = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchCriteria = $searchCriteriaBuilder
            ->addFilter('entity_id', $ids, 'in')
            ->create();
        $serviceFactory = $this->_objectManager->get(CustomerImportServiceInterfaceFactory::class);
        $getProfileByTypeId = $this->_objectManager->get(GetProfileDataByTypeIdInterface::class);
        $profileId = $getProfileByTypeId->execute(CustomerImportServiceInterface::TYPE_ID, 'entity_id');
        $service = $serviceFactory->create(['data' => ['profile_id' => (int) $profileId]]);
        try {
            $service->execute($searchCriteria);
        } catch (\Exception $e) {
            var_dump('error', $e->getMessage());
        }
        var_dump('message storage ---', $service->getMessageStorage()->getData());
        var_dump('response storage ---', $service->getResponseStorage()->getData());
    }

    private function plentyCustomerAddressModel()
    {
        $repository = $this->_objectManager->get(\SoftCommerce\PlentyCustomer\Api\CustomerAddressRepositoryInterface::class);
        /** @var SearchCriteriaBuilder $searchCriteriaObj */
        $searchCriteriaObj = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $searchFilterBuilder = $this->_objectManager->get(FilterBuilder::class);
        $searchFilterGroupBuilder = $this->_objectManager->get(FilterGroupBuilder::class);

        $filter = $searchFilterBuilder
            ->setField(CustomerAddressInterface::CUSTOMER_ID)
            ->setValue(1414)
            ->create();
        $filterGroup[] = $searchFilterGroupBuilder->setFilters([$filter])->create();

        $filter = $searchFilterBuilder
            ->setField(AddressInterface::ADDRESS_ID)
            ->setValue(1589)
            ->create();
        $filterGroup[] = $searchFilterGroupBuilder->setFilters([$filter])->create();

        $search = $searchCriteriaObj
            ->setFilterGroups($filterGroup)
            // ->addFilter('customer_id', 237)
            // ->addFilter('parent_id', 1025)
            ->create();

        var_dump('$search', $search);

        $collection = $repository->getList($search);
        var_dump('$collection count >>> ' . $collection->getTotalCount());
        foreach ($collection->getItems() as $item) {
            var_dump('typeId >>> ' . $item->getTypeId());
            var_dump('getRelationType', $item->getRelationType());
            var_dump('$item --', $item->getData());
        }

        return;
        // -- COLLECTION
        $collectionFactory = $this->_objectManager
            ->get(\Plenty\Customer\Model\ResourceModel\Customer\Address\CollectionFactory::class);
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
        /** @var SearchCriteriaBuilder $searchCriteriaObj */
        $searchCriteriaObj = $this->_objectManager->get(SearchCriteriaBuilder::class);
        $search = $searchCriteriaObj
            ->addFilter('order_id', 6407)
            // ->addFilter('parent_id', 1018)
            ->create();
        $collection = $repository->getList($search);
        var_dump('total count >>> ' . $collection->getTotalCount());
        /** @var \SoftCommerce\PlentyCustomer\Api\Data\AddressInterface $item */
        foreach ($collection->getItems() as $item) {
            var_dump('order id', $item->getOrderId());
            var_dump('typeId ', $item->getTypeId());
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

    private function restAddressInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(AddressSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(6218)
            // ->setOrderIds([6648])
            // ->setContactId(103)
            ->setWith([
                AddressSearchCriteriaInterface::WITH_FILTER_CONTACT_RELATIONS,
                AddressSearchCriteriaInterface::WITH_FILTER_ORDER_RELATIONS,
            ])
            ->create();
        $client = $this->_objectManager->get(\SoftCommerce\PlentyCustomerRestApi\Model\AddressInterface::class);
        $result = $client->getList($searchCriteria);
        // $result = $client->getListByContact($searchCriteria);
        var_dump('$result', $result->toArray());
    }

    private function restContactInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(ContactSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setContactId(237)
            ->setWith([
                ContactSearchCriteriaInterface::WITH_FILTER_ACCOUNTS
            ])
            ->create();

        $client = $this->_objectManager->get(\SoftCommerce\PlentyCustomerRestApi\Model\ContactInterface::class);
        $list = $client->getList($searchCriteria);
        var_dump('$list', $list->toArray());
    }

    private function addressCollectServiceInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(AddressSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setId(6218)
            // ->setOrderIds([6648])
            // ->setContactId(103)
            ->setWith([
                AddressSearchCriteriaInterface::WITH_FILTER_CONTACT_RELATIONS,
                AddressSearchCriteriaInterface::WITH_FILTER_ORDER_RELATIONS,
            ])
            ->create();

        $model = $this->_objectManager->get(AddressCollectServiceInterface::class);
        $model->execute($searchCriteria);
    }

    private function customerCollectServiceInterface()
    {
        $searchCriteriaBuilder = $this->_objectManager->create(ContactSearchCriteriaBuilderInterface::class);
        $searchCriteria = $searchCriteriaBuilder
            ->setContactId(1479) // 237 1365
            ->setWith([
                ContactSearchCriteriaInterface::WITH_FILTER_ADDRESS,
                ContactSearchCriteriaInterface::WITH_FILTER_ACCOUNTS,
                ContactSearchCriteriaInterface::WITH_FILTER_OPTIONS
            ])
            ->create();

        $model = $this->_objectManager->get(CustomerCollectServiceInterface::class);
        $model->execute($searchCriteria);
        var_dump('$model', $model->getMessageStorage()->getData());
    }

    private function GetCustomerRawDataInterface()
    {
        $model = $this->_objectManager->create(GetCustomerRawDataInterface::class);
        $result = $model->execute('serhiy@softcommerce.io');
        var_dump('result', $result);
    }
}
