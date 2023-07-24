<template>
    <div>
        <!--页头-->
        <div class="header">
            <el-row type="flex" justify="space-between">
                <el-col :span="6">
                    <el-button icon="el-icon-circle-plus" size="mini" type="primary" @click="addRow">
                        Mail
                    </el-button>
                </el-col>
            </el-row>
        </div>
        <!--数据表格-->
        <el-table
            :data="rows"
            border
            stripe
            style="width: 100%"
            v-loading="loading"
            element-loading-text="拼命加载中" >

            
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>dbid</div>
                        <div>
                            <el-input v-model="params.dbid" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.dbid}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>玩家账号ID</div>
                        <div>
                            <el-input v-model="params.playerid" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.playerid}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>邮件读取状态</div>
                        <div>
                            <el-input v-model="params.readstatus" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.readstatus}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>邮件发送时间</div>
                        <div>
                            <el-input v-model="params.sendtime" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.sendtime}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>邮件标题</div>
                        <div>
                            <el-input v-model="params.head" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.head}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>邮件内容</div>
                        <div>
                            <el-input v-model="params.context" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.context}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>奖励的物品</div>
                        <div>
                            <el-input v-model="params.award" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.award}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>奖励物品的领奖状态</div>
                        <div>
                            <el-input v-model="params.awardstatus" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.awardstatus}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>邮件奖励记录类型</div>
                        <div>
                            <el-input v-model="params.log_type" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.log_type}}</div>
                    </template>
                </el-table-column>
                <el-table-column align="center" width="180">
                    <template slot="header" slot-scope="scope">
                        <div>邮件奖励记录</div>
                        <div>
                            <el-input v-model="params.log" @keyup.enter.native="columnSearch" size="mini"/>
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <div>{{scope.row.log}}</div>
                    </template>
                </el-table-column>
            <el-table-column fixed="right" align="center" label="操作" width="115">
                <template slot="header" slot-scope="scope">
                    <div>操作</div>
                    <div style="text-align: center;">
                        <el-button @click="search" style="width:100%;" size="mini" icon="el-icon-search" type="primary">
                            搜索
                        </el-button>
                    </div>
                </template>
                <template slot-scope="scope">
                    <el-row type="flex" class="row-bg" justify="space-between">
                        <el-button @click="editRow(scope.row)" icon="el-icon-edit" size="mini" type="primary" />
                        <el-popconfirm
                            title="你确定删除吗？"
                            icon="el-icon-delete"
                            @confirm="deleteRow(scope.row,scope.$index)">
                            <el-button slot="reference" icon="el-icon-delete" type="danger" size="mini"/>
                        </el-popconfirm>
                    </el-row>
                </template>
            </el-table-column>
        </el-table>
        <!--分页组件-->
        <div style="padding:30px;text-align:center;">
                <el-pagination
                    v-if="meta"
                    @size-change="handleSizeChange"
                    @current-change="handlePageChange"
                    background
                    :current-page="params.currentPage"
                    :page-sizes="[10, 20, 30, 50]"
                    :page-size="params.perPage"
                    layout="total, sizes, prev, pager, next, jumper"
                    :total="meta.total">
                </el-pagination>
        </div>
        <!--添加或编辑表单弹窗-->
        <el-dialog :title="dialogTitle" :visible.sync="dialogVisible">
            <div class="dialog-body">
                <el-form :model="formData" ref="form" :rules="formRules" label-width="80px">
                    <el-form-item label="dbid">
                                <el-input v-model="formData.dbid" autocomplete="off" palceholder="dbid"></el-input>
                            </el-form-item><el-form-item label="玩家账号ID">
                                <el-input v-model="formData.playerid" autocomplete="off" palceholder="玩家账号ID"></el-input>
                            </el-form-item><el-form-item label="邮件读取状态">
                                <el-input v-model="formData.readstatus" autocomplete="off" palceholder="邮件读取状态"></el-input>
                            </el-form-item><el-form-item label="邮件发送时间">
                                <el-input v-model="formData.sendtime" autocomplete="off" palceholder="邮件发送时间"></el-input>
                            </el-form-item><el-form-item label="邮件标题">
                                <el-input v-model="formData.head" autocomplete="off" palceholder="邮件标题"></el-input>
                            </el-form-item><el-form-item label="邮件内容">
                                <el-input v-model="formData.context" autocomplete="off" palceholder="邮件内容"></el-input>
                            </el-form-item><el-form-item label="奖励的物品">
                                <el-input v-model="formData.award" autocomplete="off" palceholder="奖励的物品"></el-input>
                            </el-form-item><el-form-item label="奖励物品的领奖状态">
                                <el-input v-model="formData.awardstatus" autocomplete="off" palceholder="奖励物品的领奖状态"></el-input>
                            </el-form-item><el-form-item label="邮件奖励记录类型">
                                <el-input v-model="formData.log_type" autocomplete="off" palceholder="邮件奖励记录类型"></el-input>
                            </el-form-item><el-form-item label="邮件奖励记录">
                                <el-input v-model="formData.log" autocomplete="off" palceholder="邮件奖励记录"></el-input>
                            </el-form-item>
                </el-form>
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" :loading="loading" @click="submitForm">{{dialogBtnTxt}}</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import https from "../https";

    export default {
        name: "Mail",
        data: () => {
            return {
                loading: false,
                meta: null,
                rows: null,
                params: {
                    perPage: 10,
                    currentPage: 1
                },
                formData: {},
                formRules: {},
                //Dialog
                dialogVisible: false,
                dialogAction: 'Dialog',
                dialogTitle: 'Title',
                dialogBtnTxt: 'Submit',
            }
        },

        mounted() {
            this.search()
        },

        methods: {
            handleSizeChange: function (size) {
                this.params.perPage = size;
                this.params.currentPage = 1;
                this.search();
            },
            handlePageChange: function (page) {
                this.params.currentPage = page;
                this.search();
            },
            columnSearch: function(){
                this.params.currentPage = 1;
                this.search();
            },
            search: function () {
                this.loading = true;
                https.get("admin/mails", this.params).then(resp => {
                    this.rows = resp.data;
                    this.meta = resp.meta;
                }).catch(error => {
                    this.$message.error(error.config.url + "#" + error.message);
                }).finally(() => {
                    this.loading = false;
                });
            },
            reset: function () {
                this.params = {perPage: 10, currentPage: 1};
                this.search();
            },
            addRow: function () {
                this.dialogVisible = true;
                this.dialogAction = "add";
                this.dialogTitle = "添加一条记录";
                this.dialogBtnTxt = "提 交";
                this.formData = {};
            },
            saveRow: function () {
                let params = Object.assign({},this.formData);
                https.post("admin/mails", params).then(resp => {
                    this.dialogVisible = false;
                    this.$message.success("添加成功");
                    this.params = {id: resp.data.id};
                    this.search();
                    this.formData = {};
                }).catch(error => {
                    this.$message.error(error.config.url + "#" + error.message);
                });
            },
            editRow: function (row) {
                this.dialogVisible = true;
                this.dialogAction = 'edit';
                this.dialogTitle = "修改这条记录";
                this.dialogBtnTxt = "保 存";
                this.formData = row;
            },
            updateRow: function () {
                this.loading = true;
                let params = Object.assign({},this.formData);
                https.put("admin/mails/" + params.id, params).then(resp => {
                    this.dialogVisible = false;
                    this.loading = false;
                    this.$message.success("保存成功");
                    this.formData = {};
                }).catch(error => {
                    this.$message.error(error.config.url + "#" + error.message);
                }).finally(() => {
                    this.loading = false;
                });
            },
            deleteRow: function (row, index) {
                this.loading = true;
                https.destroy("admin/mails/" + row.id).then(resp => {
                    this.rows.splice(index, 1);
                    this.$message.success('删除成功');
                    //当前页面删除完之后刷新页面
                    if (this.rows.length == 0) {
                        if (this.meta.last_page != this.meta.current_page) {
                            this.search();
                        } else {
                            this.handlePageChange(this.params.currentPage - 1);
                        }
                    }
                }).catch(error => {
                    this.$message.error(error.config.url + "#" + error.message);
                }).finally(() => {
                    this.loading = false;
                });
            },
            submitForm: function (e) {
                this.$refs['form'].validate((valid) => {
                    if (valid) {
                        if (this.dialogAction == "add") {
                            this.saveRow();
                        } else if (this.dialogAction == "edit") {
                            this.updateRow();
                        }
                    } else {
                        console.log("error");
                        return false;
                    }
                });
            }
        }
    }
</script>

<style scoped>

</style>
