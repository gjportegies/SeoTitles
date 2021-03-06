<?php
/**
 * Space48_SeoTitles
 *
 * @category    Space48
 * @package     Space48_SeoTitles
 * @Date        04/2017
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @author      @diazwatson
 */

namespace Space48\SeoTitles\Plugin\Catalog\Model;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Category
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Category constructor.
     *
     * @param CollectionFactory     $collectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $category
     * @param $result
     *
     * @return string
     */
    public function afterGetName($category, $result)
    {
        return $category->getData('h1_override') ? $category->getData('h1_override') : $result;
    }

    /**
     * @param $subject
     *
     * @return \Magento\Framework\DataObject[]
     */
    public function afterGetParentCategories($subject)
    {
        $pathIds = array_reverse(explode(',', $subject->getPathInStore()));
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categories */
        $categories = $this->collectionFactory->create();

        return $categories->setStore(
            $this->storeManager->getStore()
        )->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'h1_override'
        )->addAttributeToSelect(
            'url_key'
        )->addFieldToFilter(
            'entity_id',
            ['in' => $pathIds]
        )->addFieldToFilter(
            'is_active',
            1
        )->load()->getItems();
    }
}