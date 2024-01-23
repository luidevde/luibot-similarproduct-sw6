import Plugin from "src/plugin-system/plugin.class";
import HttpClient from 'src/service/http-client.service';

export default class LuidevProductStatistics extends Plugin {
    static options = {
        url: null,
        productId: null,
        parentId: null
    };

    init() {
        this.client = new HttpClient();

        if (!this.options.url || !this.options.productId) {
            return;
        }

        this._isClickedProduct();
    }

    _isClickedProduct() {
        const clickedProductsFromStorage = localStorage.getItem('luidevClickedProducts');
        if (!clickedProductsFromStorage) {
            return;
        }

        let clickedProducts = JSON.parse(clickedProductsFromStorage);
        if (clickedProducts.length === 0) {
            return;
        }

        if (this.options.parentId) {
            const parentIndex = clickedProducts.indexOf(this.options.parentId);
            if (parentIndex !== -1) {
                this._sendClickStatistics().then(() => {
                    clickedProducts.splice(parentIndex, 1);
                    localStorage.setItem('luidevClickedProducts', JSON.stringify(clickedProducts))
                });
            }
        }

        const index = clickedProducts.indexOf(this.options.productId);
        if (index !== -1) {
            this._sendClickStatistics().then(() => {
                clickedProducts.splice(index, 1);
                localStorage.setItem('luidevClickedProducts', JSON.stringify(clickedProducts))
            });
        }
    }

    async _sendClickStatistics() {
        const body = {"productId": this.options.productId};

        this.client.post(
            this.options.url,
            JSON.stringify(body)
        );
    }
}