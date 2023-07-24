<template>
    <div style="display: inline-block;">
        <el-input
            v-model="keyword"
            :placeholder="placeholder"
            prefix-icon="el-icon-location"
            clearable
            @input="inputKeyword"
            @clear="clearInput"
            style="width: 150px;">
        </el-input>
        <el-popover
            placement="bottom"
            width="200"
            trigger="manual"
            v-model="popVisible" style="padding: 0px;">
            <div v-loading="loading">
                <div v-for="(item,index) in searchResult"
                     :key="index"
                     @click="selectCity(item)"
                     class="item"
                >
                    <div class="name">{{item.district}}</div>
                    <div class="address">{{item.address}}</div>
                </div>
            </div>
        </el-popover>
    </div>
</template>

<script>
    import utils from "../utils";

    export default {
        name: "CityPicker",
        props: {
            placeholder: String
        },
        data: () => {
            return {
                loading: false,
                popVisible: false,
                keyword: '',
                location: null,
                searchResult: []
            }
        },
        methods: {
            clearInput: function () {
                this.keyword = '';
                this.loading = false;
                this.popVisible = false;
                this.$emit('clear');
            },
            inputKeyword: function (keyword) {
                if (keyword != '') {
                    this.loading = true;
                    this.popVisible = true;
                    this.searchResult = [];
                    utils.amap().then(AMap => {
                        let autoOptions = {city: '全国'};
                        let autoComplete = new AMap.AutoComplete(autoOptions);
                        autoComplete.search(keyword, (status, result) => {
                            if (status == "complete") {
                                if (result.count > 0) {
                                    this.searchResult = result.tips.filter(item => {
                                        if (item.location && item.location.lng) {
                                            return item;
                                        }
                                    });
                                }
                            }
                            this.loading = false;
                        })
                    });
                }
            },
            selectCity: function (city) {
                this.searchResult = [];
                this.keyword = '';
                this.loading = true;
                utils.amap().then(AMap => {
                    let geocoder = new AMap.Geocoder({});
                    let postion = [city.location.lng, city.location.lat];
                    geocoder.getAddress(postion, (status, result) => {
                        if (status === 'complete' && result.info === 'OK') {
                            let info = {
                                ...result.regeocode.addressComponent,
                                formattedAddress: result.regeocode.formattedAddress,
                                location: city.location
                            };
                            this.keyword = info.formattedAddress;
                            this.$emit('click', info);
                        } else {
                            this.$emit('click', null);
                        }
                        this.loading = false;
                        this.popVisible = false;
                    })
                });
            },
        }
    }
</script>

<style scoped>
    .item {
        padding: 5px;
        border-radius: 5px;
    }

    .item:hover {
        background: #f0f0f0;
    }

    .name {
        font-size: 14px;
    }

    .address {
        font-size: 12px;
        color: #b4b4b4;
    }
</style>
