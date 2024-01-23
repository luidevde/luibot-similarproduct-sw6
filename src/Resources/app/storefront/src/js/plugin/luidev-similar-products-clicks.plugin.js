import Plugin from "src/plugin-system/plugin.class";

export default class LuidevSimilarProductsClicks extends Plugin {
    init() {
        let me = this;

        const sliderItems = this.el.querySelectorAll('.product-slider-item');
        if (!sliderItems || sliderItems.length === 0) {
            return;
        }

        sliderItems.forEach(function (item) {
            item.addEventListener('click', () => {
                me._saveClickedProduct(item);
            })
        });
    }

    _saveClickedProduct(item) {
        const productIdField = item.querySelector("input[name='product-id']");
        if (!productIdField) {
            return;
        }

        const productId = productIdField.value;

        if (!productId) {
            return;
        }

        let clickedProducts = [];

        const clickedProductsFromStorage = localStorage.getItem('luidevClickedProducts');
        if (clickedProductsFromStorage) {
            clickedProducts = [...clickedProducts, ...JSON.parse(clickedProductsFromStorage)];
        }

        clickedProducts.push(productId);
        clickedProducts = clickedProducts.filter((value, index, self) => self.indexOf(value) === index);

        localStorage.setItem("luidevClickedProducts", JSON.stringify(clickedProducts));
    }
}