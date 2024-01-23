<?php

namespace LuidevRecommendSimilarProducts\Helper;

use Shopware\Core\Content\Product\ProductEntity;

class SimilarProductHelper
{
    public function buildSimilarProductData(ProductEntity $product): array
    {
        if (!empty($product->getTranslation('name')) && $product->getCover()) {
            $categoriesList = [];
            if (!empty($categoriesElements = $product->getCategories()->getElements())) {
                foreach ($categoriesElements as $categoryElement) {
                    $categoriesList = array_merge($categoriesList, $categoryElement->getBreadcrumb());
                }
            }

            return [
                'productId' => $product->getId(),
                'name' => $product->getTranslation('name'),
                'imageUrl' =>  $product->getCover()->getMedia()->getUrl(),
                'categories' => $categoriesList
            ];
        }

        return [];
    }
}