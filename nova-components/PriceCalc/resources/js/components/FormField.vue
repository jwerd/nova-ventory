<template>
    <DefaultField :field="currentField" :errors="errors" :show-help-text="showHelpText">
        <template #field>
<!--            <button size="lg" align="center" component="button" dusk="update-and-continue-editing-button" class="shadow relative bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-900 cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-900">-->
<!--                <span class="">Re-Calculate</span>&lt;!&ndash;&ndash;&gt;-->
<!--            </button>-->
            <label class="inline-block pt-2 leading-tight mb-2">
                {{fields.com.placeholder}}
            </label>
            <input
                type="number"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :placeholder="fields.com.placeholder"
                @change="calculate"
                v-model="fields.com.value"
            />
            <label class="inline-block pt-2 leading-tight mb-2">
                {{fields.profit.placeholder}}
            </label>
            <input
                type="number"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :placeholder="fields.profit.placeholder"
                @change="calculate"
                v-model="fields.profit.value"
            />
            <label class="inline-block pt-2 leading-tight mb-2">
                {{fields.tax.placeholder}}
            </label>
            <input
                type="number"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                :placeholder="fields.tax.placeholder"
                @change="calculate"
                disabled
                v-model="fields.tax.value"
            />
            <label class="inline-block pt-2 leading-tight mb-2">
                List Price
            </label>
            <input
                :id="currentField.attribute"
                type="text"
                class="w-full form-control form-input form-input-bordered"
                :class="errorClasses"
                placeholder="List Price"
                v-model="value"
            />

        </template>
    </DefaultField>
</template>

<script>
import { DependentFormField, HandlesValidationErrors } from 'laravel-nova'

export default {
    mixins: [DependentFormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    data() {
        return {
            fields: {
                com: {
                    placeholder: 'Cost of Materials',
                    value: '',
                },
                tax: {
                    placeholder: 'Tax Rate: 8.1% ',
                    value: '',
                },
                profit: {
                    placeholder: 'Desired Profit',
                    value: '',
                }
            }
        }
    },

    methods: {
        /*
         * Set the initial, internal value for the field.
         */
        setInitialValue() {
            this.value = this.field.value || ''
        },

        /**
         * Fill the given FormData object with the field's internal value.
         */
        fill(formData) {
            formData.append(this.field.attribute, this.value || '')
        },

        roundBy5(x) { //@todo: move this to utils
            return Math.ceil(x/5)*5;
        },

        calculate() {

            const totals = []
            totals.push(this.field.price);
            if(!_.isEmpty(this.fields.com)) {
                totals.push(this.fields.com.value);
            }
            if(!_.isEmpty(this.fields.profit)) {
                totals.push(this.fields.profit.value);
            }
            if(!_.isEmpty(this.fields.tax)) {
                let taxRate = .0810;
                let sum = _.sum(totals)
                let taxTotal =  parseFloat(sum * taxRate).toFixed(2);
                this.fields.tax.value = taxTotal;

                let after_taxes = parseFloat(sum)+parseFloat(taxTotal);
                let total = this.roundBy5(parseFloat(after_taxes).toFixed(2));
                this.value = total
            }
            // let sum = this.fields.reduce((acc, item) => acc + item.value, 0);
            // console.log(sum)
        }
    },
}
</script>
