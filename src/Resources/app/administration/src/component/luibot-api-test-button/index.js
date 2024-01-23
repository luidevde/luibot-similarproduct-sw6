const {Component, Mixin} = Shopware;
import template from './luibot-api-test-button.html.twig';

Component.register('luibot-api-test-button', {
    template,

    props: ['label'],
    inject: ['luibotApiTest'],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            isLoading: false,
            isSaveSuccessful: false,
        };
    },

    computed: {
        pluginConfig() {
            let $parent = this.$parent;

            while ($parent.actualConfigData === undefined) {
                $parent = $parent.$parent;
            }

            const currentSalesChannelId = $parent.currentSalesChannelId;
            if (currentSalesChannelId === null
                || !(currentSalesChannelId in $parent.actualConfigData)) {
                return $parent.actualConfigData.null;
            }

            return {
                ...$parent.actualConfigData.null,
                ...this.clean($parent.actualConfigData[currentSalesChannelId])
            };
        }
    },

    methods: {
        saveFinish() {
            this.isSaveSuccessful = false;
        },

        clean(obj) {
            for (let propName in obj) {
                if (obj[propName] === null || obj[propName] === undefined) {
                    delete obj[propName];
                }
            }
            return obj
        },

        check() {
            this.isLoading = true;
            this.luibotApiTest.check(this.pluginConfig).then((res) => {
                if (res.success) {
                    this.isSaveSuccessful = true;
                    this.createNotificationSuccess({
                        title: this.$tc('luibot-api-test-button.title'),
                        message: this.$tc('luibot-api-test-button.success')
                    });
                } else {
                    this.createNotificationError({
                        title: this.$tc('luibot-api-test-button.title'),
                        message: this.$tc('luibot-api-test-button.error')
                    });
                }

                this.isLoading = false;
            });
        }
    }
})