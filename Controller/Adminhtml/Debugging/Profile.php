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
use SoftCommerce\PlentyItem\Api\Data\Profile\CategoryExportInterface;

class Profile extends Action
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
        // PROFILE SCHEDULE CRONJOB
        // $this->runCronSchedule();

        // PROFILE TYPE INSTANCE
        // $this->typeInstanceOptionsInterface();

        // PROFILE CONFIG
        // $this->configScopeInterface();
        // $this->getApiConfig();
        $this->storeConfig();
        // $this->websiteStorage();
        // $this->priceConfig();
        // $this->mediaConfig();
        // $this->scheduleConfig();
        // $this->stockConfig();
        // $this->getProfileScheduleData();
        // $this->getProfileIdByConfigConditionInterface();

        // SCHEDULE
        // $this->scheduleTypeInstanceFactoryInterface();
        // $this->profileScheduleProcessorInterface();
        // $this->profileHistoryCleanup();

        // General
        // $this->getLastCollectedAt();
    }

    private function typeInstanceOptionsInterface()
    {
        $typeInstance = $this->_objectManager->get(\SoftCommerce\Profile\Model\TypeInstanceOptionsInterface::class);
        $data = $typeInstance->getCronGroupByTypeId('plenty_category_export');
        var_dump('$data', $data);
        var_dump('getOptionArray', $typeInstance->getOptionArray());
    }

    private function configScopeInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\ProfileConfig\Model\ConfigScopeInterface::class);
        $data = $model->get(
            17,
            'plenty_item_import/property_config/property_mapping',
            'website',
            2
        );
        var_dump('data', $data);
        $data = $model->get(
            17,
            'plenty_item_import/property_config/property_mapping',
            'website',
            1
        );
        var_dump('data 2', $data);

        return;
        $data = $model->get(
            11,
            'product_import/tax_price_config/price_mapping',
            'website',
            2
        );
        var_dump('data', json_decode($data, true));
    }

    private function getApiConfig()
    {
        $config = $this->_objectManager->get(\SoftCommerce\PlentyProfile\Model\Config\ApiConfigInterface::class);
        $config->setProfileId(11);
        $data = $config->getBehaviour();
        var_dump('data', $data);
    }

    private function storeConfig()
    {
        $config = $this->_objectManager->get(\SoftCommerce\PlentyProfile\Model\Config\StoreConfigInterface::class);
        $config->setProfileId(17);
        $storeMapping = $config->getStoreMapping(true);
        var_dump('$storeMapping', $storeMapping);
        $storeMapping2 = $config->getReferrerIdByStoreId(2);
        var_dump('$storeMapping2', $storeMapping2);
    }

    private function websiteStorage()
    {
        $model = $this->_objectManager->get(\SoftCommerce\Core\Model\Store\WebsiteStorageInterface::class);
        var_dump('data', $model->getAdminStore());
    }

    private function priceConfig()
    {
        $config = $this->_objectManager->get(\SoftCommerce\PlentyItemProfile\Model\Config\PriceConfigInterface::class);
        $config->setProfileId(17);
        $priceConfig = $config->getCustomerGroupPriceMapping();
        var_dump('$priceConfig', $priceConfig);
    }

    private function mediaConfig()
    {
        $config = $this->_objectManager->get(\SoftCommerce\PlentyItemProfile\Model\Config\MediaConfigInterface::class);
        $config->setProfileId(17);
        var_dump('isActiveFileDownload', $config->isActiveFileDownload());
    }

    private function scheduleConfig()
    {
        $configFactory = $this->_objectManager->get(\SoftCommerce\ProfileSchedule\Model\Config\ScheduleConfigInterfaceFactory::class);
        foreach ([11,13] as $profileId) {
            $config = $configFactory->create(['profileId' => $profileId]);
            var_dump('$config', $config->getScheduleId());
        }
    }

    private function stockConfig()
    {
        $config = $this->_objectManager->get(\SoftCommerce\PlentyStockProfile\Model\Config\StockConfigInterface::class);
        $config->setProfileId(18);
        $test = $config->getSourceCodesByStockId(2);
        var_dump('$test', $test);
    }

    private function getProfileScheduleData()
    {
        $config = $this->_objectManager->get(\SoftCommerce\ProfileSchedule\Model\GetProfileIdByScheduleInterface::class);
        $config = $config->execute('plenty_category_export', 43);

        var_dump('$config', $config);
    }

    private function getProfileIdByConfigConditionInterface()
    {
        $config = $this->_objectManager->get(\SoftCommerce\ProfileConfig\Model\GetProfileIdByConfigCondition::class);
        $config = $config->execute('plenty_category_export/event_config/new_entity_observer', 1);

        var_dump('$config', $config);
    }

    private function scheduleTypeInstanceFactoryInterface()
    {
        $typeInstance = $this->_objectManager->get(\SoftCommerce\Profile\Model\TypeInstanceOptionsInterface::class);

        var_dump('$typeInstance', $typeInstance->getTypes());

        $data = $typeInstance->getCronGroupByTypeId('plenty_category_export');
        var_dump('$data', $data);
    }

    private function profileScheduleProcessorInterface()
    {
        $model = $this->_objectManager->get(\SoftCommerce\ProfileSchedule\Model\ScheduleProcessorInterface::class);
        $model->execute('plenty_category_export');
    }

    private function profileHistoryCleanup()
    {
        $model = $this->_objectManager->get(\SoftCommerce\ProfileHistory\Cron\Backend\HistoryCleanup::class);
        $model->execute();
    }

    private function getLastCollectedAt()
    {
        $model = $this->_objectManager->get(\SoftCommerce\PlentyProfile\Model\GetLastCollectedAtInterface::class);
        $data = $model->execute('plenty_order_entity', true, 5);
        var_dump('$data', $data);

    }
}
