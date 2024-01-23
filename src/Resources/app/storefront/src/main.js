import LuidevSimilarProductsClicks from "./js/plugin/luidev-similar-products-clicks.plugin";
import LuidevProductStatistics from "./js/plugin/luidev-product-statistics.plugin";

const PluginManager = window.PluginManager;
PluginManager.register('LuidevSimilarProductsClicks', LuidevSimilarProductsClicks, '[data-luidev-similar-products-clicks]');
PluginManager.register('LuidevProductStatistics', LuidevProductStatistics, '[data-luidev-product-statistics]');