<template>
    <div>
        <el-table
            :data="rows"
            border
            stripe
            style="width: 100%"
            v-loading="loading"
            element-loading-text="拼命加载中">


            <el-table-column align="center" width="130">
                <template slot="header" slot-scope="scope">
                    <div>玩家账号ID</div>
                    <div>
                        <el-input v-model="params.dbid" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.dbid }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="120">
                <template slot="header" slot-scope="scope">
                    <div>玩家账号</div>
                    <div>
                        <el-input v-model="params.account" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.account }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="100">
                <template slot="header" slot-scope="scope">
                    <div>创建服务器</div>
                    <div>
                        <el-input v-model="params.serverid" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.serverid }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="100">
                <template slot="header" slot-scope="scope">
                    <div>玩家名称</div>
                    <div>
                        <el-input v-model="params.name" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.name }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="100">
                <template slot="header" slot-scope="scope">
                    <div>封号时间</div>
                    <div>
                        <el-input v-model="params.sealed" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.sealed }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="100">
                <template slot="header" slot-scope="scope">
                    <div>禁言时间</div>
                    <div>
                        <el-input v-model="params.silent" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.silent }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>玩家创建时间</div>
                    <div>
                        <el-input v-model="params.createtime" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.createtime }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>最后登录时间</div>
                    <div>
                        <el-input v-model="params.lastonlinetime" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.lastonlinetime }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="120">
                <template slot="header" slot-scope="scope">
                    <div>创建账号IP</div>
                    <div>
                        <el-input v-model="params.createip" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.createip }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="120">
                <template slot="header" slot-scope="scope">
                    <div>上次登录的IP</div>
                    <div>
                        <el-input v-model="params.lastloginip" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.lastloginip }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>职业</div>
                    <div>
                        <el-input v-model="params.job" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.job }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>性别</div>
                    <div>
                        <el-input v-model="params.sex" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.sex }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>玩家等级</div>
                    <div>
                        <el-input v-model="params.level" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.level }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>玩家战力</div>
                    <div>
                        <el-input v-model="params.totalpower" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.totalpower }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>经验值</div>
                    <div>
                        <el-input v-model="params.exp" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.exp }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>金钱</div>
                    <div>
                        <el-input v-model="params.gold" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.gold }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>元宝</div>
                    <div>
                        <el-input v-model="params.yuanbao" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.yuanbao }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>绑定的元宝</div>
                    <div>
                        <el-input v-model="params.byb" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.byb }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>充值金额</div>
                    <div>
                        <el-input v-model="params.recharge" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.recharge }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>充值公告记录</div>
                    <div>
                        <el-input v-model="params.rechargenotice" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.rechargenotice }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>单笔最大充值金额</div>
                    <div>
                        <el-input v-model="params.recharge_maxone" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.recharge_maxone }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>最后充值时间</div>
                    <div>
                        <el-input v-model="params.recharge_lasttime" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.recharge_lasttime }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>vip等级</div>
                    <div>
                        <el-input v-model="params.vip" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.vip }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>vip领奖记录</div>
                    <div>
                        <el-input v-model="params.vipstate" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.vipstate }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>vip额外奖励</div>
                    <div>
                        <el-input v-model="params.vipaddedreward" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.vipaddedreward }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>背包的格子数量</div>
                    <div>
                        <el-input v-model="params.bagnum" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.bagnum }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>公会ID</div>
                    <div>
                        <el-input v-model="params.guildid" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.guildid }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>公会贡献</div>
                    <div>
                        <el-input v-model="params.contrib" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.contrib }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>客户端保存的数据</div>
                    <div>
                        <el-input v-model="params.clientvalue" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.clientvalue }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>客户端保存的数据列表</div>
                    <div>
                        <el-input v-model="params.clientvaluelist" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.clientvaluelist }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>已获取的全局邮件</div>
                    <div>
                        <el-input v-model="params.global_mails" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.global_mails }}</div>
                </template>
            </el-table-column>
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>玩家兑换数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.exchange_data" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.exchange_data }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>玩家护送数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.escort_data" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.escort_data }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>玩家好友数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.friend_data" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.friend_data }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>欢迎提示</div>
                    <div>
                        <el-input v-model="params.welcome" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.welcome }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>功能预告奖励状态</div>
                    <div>
                        <el-input v-model="params.openfuncstate" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.openfuncstate }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>排行榜膜拜</div>
                    <div>
                        <el-input v-model="params.rankworship" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.rankworship }}</div>
                </template>
            </el-table-column>
            <el-table-column align="center" width="180">
                <template slot="header" slot-scope="scope">
                    <div>改名次数</div>
                    <div>
                        <el-input v-model="params.renamecount" @keyup.enter.native="columnSearch" size="mini"/>
                    </div>
                </template>
                <template slot-scope="scope">
                    <div>{{ scope.row.renamecount }}</div>
                </template>
            </el-table-column>
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>头像框</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.head" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.head }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>结婚</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.marry" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.marry }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>章节关卡</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.chapter" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.chapter }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>任务数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.task" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.task }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>宠物捕捉时间</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.catchpet" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.catchpet }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>材料副本数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.material" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.material }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>藏宝图副本数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.treasuremap" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.treasuremap }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>大雁塔副本数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.wildgeeseFb" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.wildgeeseFb }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>勇闯天庭本数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.heavenFb" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.heavenFb }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>全民Boss副本数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.publicboss" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.publicboss }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>至尊Boss副本数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.vipboss" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.vipboss }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>竞技场数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.arena" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.arena }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>跨服组队</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.crossTeamFb" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.crossTeamFb }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>八十一难</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.eightyOneHard" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.eightyOneHard }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>总登录天数</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.totalloginday" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.totalloginday }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>上次登录的天数</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.lastloginday" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.lastloginday }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>仙侣</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.xianlv" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.xianlv }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>宠物</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.pet" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.pet }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>天女</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.tiannv" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.tiannv }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>神将</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.tianshen" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.tianshen }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>阵型</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.formation" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.formation }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>灵童</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.baby" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.baby }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>日常任务</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.daily_task" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.daily_task }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>月卡周卡</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.welfare" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.welfare }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>进阶奖励</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.Advanced" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.Advanced }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>答题</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.answer" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.answer }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>商店</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.shop" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--&lt;!&ndash;                    <div>{{ scope.row.shop }}</div>&ndash;&gt;-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>玩家帮派数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.guild_data" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.guild_data }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>缘分配置</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.brother" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.brother }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>玩家福利数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.welfare_data" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.welfare_data }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>充值记录</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.recharger_data" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.recharger_data }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>活动数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.activity_record" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--&lt;!&ndash;                    <div>{{ scope.row.activity_record }}</div>&ndash;&gt;-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>抽奖数据</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.luck" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.luck }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>拍卖行</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.auction" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.auction }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>图腾</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.totems" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.totems }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>激活码</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.redeemcode" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.redeemcode }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>我要变强</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.enhance" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.enhance }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>摇钱树</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.cashCow" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.cashCow }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>单人天降好礼</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.recharge_holyshit" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.recharge_holyshit }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>条件天降好礼</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.recharge_godlike" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.recharge_godlike }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column align="center" width="180">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>阵位系统</div>-->
<!--                    <div>-->
<!--                        <el-input v-model="params.position" @keyup.enter.native="columnSearch" size="mini"/>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <div>{{ scope.row.position }}</div>-->
<!--                </template>-->
<!--            </el-table-column>-->
<!--            <el-table-column fixed="right" align="center" label="操作" width="115">-->
<!--                <template slot="header" slot-scope="scope">-->
<!--                    <div>操作</div>-->
<!--                    <div style="text-align: center;">-->
<!--                        <el-button @click="search" style="width:100%;" size="mini" icon="el-icon-search" type="primary">-->
<!--                            搜索-->
<!--                        </el-button>-->
<!--                    </div>-->
<!--                </template>-->
<!--                <template slot-scope="scope">-->
<!--                    <el-row type="flex" class="row-bg" justify="space-between">-->
<!--                        <el-button @click="editRow(scope.row)" icon="el-icon-edit" size="mini" type="primary"/>-->
<!--                        <el-popconfirm-->
<!--                            title="你确定删除吗？"-->
<!--                            icon="el-icon-delete"-->
<!--                            @confirm="deleteRow(scope.row,scope.$index)">-->
<!--                            <el-button slot="reference" icon="el-icon-delete" type="danger" size="mini"/>-->
<!--                        </el-popconfirm>-->
<!--                    </el-row>-->
<!--                </template>-->
<!--            </el-table-column>-->
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
                    <el-form-item label="玩家账号ID">
                        <el-input v-model="formData.dbid" autocomplete="off" palceholder="玩家账号ID"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家账号">
                        <el-input v-model="formData.account" autocomplete="off" palceholder="玩家账号"></el-input>
                    </el-form-item>
                    <el-form-item label="创建服务器">
                        <el-input v-model="formData.serverid" autocomplete="off" palceholder="创建服务器"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家名称">
                        <el-input v-model="formData.name" autocomplete="off" palceholder="玩家名称"></el-input>
                    </el-form-item>
                    <el-form-item label="封号时间">
                        <el-input v-model="formData.sealed" autocomplete="off" palceholder="封号时间"></el-input>
                    </el-form-item>
                    <el-form-item label="禁言时间">
                        <el-input v-model="formData.silent" autocomplete="off" palceholder="禁言时间"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家创建时间">
                        <el-input v-model="formData.createtime" autocomplete="off" palceholder="玩家创建时间"></el-input>
                    </el-form-item>
                    <el-form-item label="最后登录时间">
                        <el-input v-model="formData.lastonlinetime" autocomplete="off" palceholder="最后登录时间"></el-input>
                    </el-form-item>
                    <el-form-item label="创建账号的IP">
                        <el-input v-model="formData.createip" autocomplete="off" palceholder="创建账号的IP"></el-input>
                    </el-form-item>
                    <el-form-item label="上次登录的IP">
                        <el-input v-model="formData.lastloginip" autocomplete="off" palceholder="上次登录的IP"></el-input>
                    </el-form-item>
                    <el-form-item label="职业">
                        <el-input v-model="formData.job" autocomplete="off" palceholder="职业"></el-input>
                    </el-form-item>
                    <el-form-item label="性别">
                        <el-input v-model="formData.sex" autocomplete="off" palceholder="性别"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家等级">
                        <el-input v-model="formData.level" autocomplete="off" palceholder="玩家等级"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家战力">
                        <el-input v-model="formData.totalpower" autocomplete="off" palceholder="玩家战力"></el-input>
                    </el-form-item>
                    <el-form-item label="经验值">
                        <el-input v-model="formData.exp" autocomplete="off" palceholder="经验值"></el-input>
                    </el-form-item>
                    <el-form-item label="金钱">
                        <el-input v-model="formData.gold" autocomplete="off" palceholder="金钱"></el-input>
                    </el-form-item>
                    <el-form-item label="元宝">
                        <el-input v-model="formData.yuanbao" autocomplete="off" palceholder="元宝"></el-input>
                    </el-form-item>
                    <el-form-item label="绑定的元宝">
                        <el-input v-model="formData.byb" autocomplete="off" palceholder="绑定的元宝"></el-input>
                    </el-form-item>
                    <el-form-item label="充值金额">
                        <el-input v-model="formData.recharge" autocomplete="off" palceholder="充值金额"></el-input>
                    </el-form-item>
                    <el-form-item label="充值公告记录">
                        <el-input v-model="formData.rechargenotice" autocomplete="off" palceholder="充值公告记录"></el-input>
                    </el-form-item>
                    <el-form-item label="单笔最大充值金额">
                        <el-input v-model="formData.recharge_maxone" autocomplete="off"
                                  palceholder="单笔最大充值金额"></el-input>
                    </el-form-item>
                    <el-form-item label="最后充值时间">
                        <el-input v-model="formData.recharge_lasttime" autocomplete="off"
                                  palceholder="最后充值时间"></el-input>
                    </el-form-item>
                    <el-form-item label="vip等级">
                        <el-input v-model="formData.vip" autocomplete="off" palceholder="vip等级"></el-input>
                    </el-form-item>
                    <el-form-item label="vip领奖记录">
                        <el-input v-model="formData.vipstate" autocomplete="off" palceholder="vip领奖记录"></el-input>
                    </el-form-item>
                    <el-form-item label="vip额外奖励">
                        <el-input v-model="formData.vipaddedreward" autocomplete="off" palceholder="vip额外奖励"></el-input>
                    </el-form-item>
                    <el-form-item label="背包的格子数量">
                        <el-input v-model="formData.bagnum" autocomplete="off" palceholder="背包的格子数量"></el-input>
                    </el-form-item>
                    <el-form-item label="公会ID">
                        <el-input v-model="formData.guildid" autocomplete="off" palceholder="公会ID"></el-input>
                    </el-form-item>
                    <el-form-item label="公会贡献">
                        <el-input v-model="formData.contrib" autocomplete="off" palceholder="公会贡献"></el-input>
                    </el-form-item>
                    <el-form-item label="客户端保存的数据">
                        <el-input v-model="formData.clientvalue" autocomplete="off" palceholder="客户端保存的数据"></el-input>
                    </el-form-item>
                    <el-form-item label="客户端保存的数据列表">
                        <el-input v-model="formData.clientvaluelist" autocomplete="off"
                                  palceholder="客户端保存的数据列表"></el-input>
                    </el-form-item>
                    <el-form-item label="已获取的全局邮件">
                        <el-input v-model="formData.global_mails" autocomplete="off" palceholder="已获取的全局邮件"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家兑换数据">
                        <el-input v-model="formData.exchange_data" autocomplete="off" palceholder="玩家兑换数据"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家护送数据">
                        <el-input v-model="formData.escort_data" autocomplete="off" palceholder="玩家护送数据"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家好友数据">
                        <el-input v-model="formData.friend_data" autocomplete="off" palceholder="玩家好友数据"></el-input>
                    </el-form-item>
                    <el-form-item label="欢迎提示">
                        <el-input v-model="formData.welcome" autocomplete="off" palceholder="欢迎提示"></el-input>
                    </el-form-item>
                    <el-form-item label="功能预告奖励状态">
                        <el-input v-model="formData.openfuncstate" autocomplete="off" palceholder="功能预告奖励状态"></el-input>
                    </el-form-item>
                    <el-form-item label="排行榜膜拜">
                        <el-input v-model="formData.rankworship" autocomplete="off" palceholder="排行榜膜拜"></el-input>
                    </el-form-item>
                    <el-form-item label="改名次数">
                        <el-input v-model="formData.renamecount" autocomplete="off" palceholder="改名次数"></el-input>
                    </el-form-item>
                    <el-form-item label="头像框">
                        <el-input v-model="formData.head" autocomplete="off" palceholder="头像框"></el-input>
                    </el-form-item>
                    <el-form-item label="结婚">
                        <el-input v-model="formData.marry" autocomplete="off" palceholder="结婚"></el-input>
                    </el-form-item>
                    <el-form-item label="章节关卡">
                        <el-input v-model="formData.chapter" autocomplete="off" palceholder="章节关卡"></el-input>
                    </el-form-item>
                    <el-form-item label="任务数据">
                        <el-input v-model="formData.task" autocomplete="off" palceholder="任务数据"></el-input>
                    </el-form-item>
                    <el-form-item label="宠物捕捉时间">
                        <el-input v-model="formData.catchpet" autocomplete="off" palceholder="宠物捕捉时间"></el-input>
                    </el-form-item>
                    <el-form-item label="材料副本数据">
                        <el-input v-model="formData.material" autocomplete="off" palceholder="材料副本数据"></el-input>
                    </el-form-item>
                    <el-form-item label="藏宝图副本数据">
                        <el-input v-model="formData.treasuremap" autocomplete="off" palceholder="藏宝图副本数据"></el-input>
                    </el-form-item>
                    <el-form-item label="大雁塔副本数据">
                        <el-input v-model="formData.wildgeeseFb" autocomplete="off" palceholder="大雁塔副本数据"></el-input>
                    </el-form-item>
                    <el-form-item label="勇闯天庭本数据">
                        <el-input v-model="formData.heavenFb" autocomplete="off" palceholder="勇闯天庭本数据"></el-input>
                    </el-form-item>
                    <el-form-item label="全民Boss副本数据">
                        <el-input v-model="formData.publicboss" autocomplete="off" palceholder="全民Boss副本数据"></el-input>
                    </el-form-item>
                    <el-form-item label="至尊Boss副本数据">
                        <el-input v-model="formData.vipboss" autocomplete="off" palceholder="至尊Boss副本数据"></el-input>
                    </el-form-item>
                    <el-form-item label="竞技场数据">
                        <el-input v-model="formData.arena" autocomplete="off" palceholder="竞技场数据"></el-input>
                    </el-form-item>
                    <el-form-item label="跨服组队">
                        <el-input v-model="formData.crossTeamFb" autocomplete="off" palceholder="跨服组队"></el-input>
                    </el-form-item>
                    <el-form-item label="八十一难">
                        <el-input v-model="formData.eightyOneHard" autocomplete="off" palceholder="八十一难"></el-input>
                    </el-form-item>
                    <el-form-item label="总登录天数">
                        <el-input v-model="formData.totalloginday" autocomplete="off" palceholder="总登录天数"></el-input>
                    </el-form-item>
                    <el-form-item label="上次登录的天数">
                        <el-input v-model="formData.lastloginday" autocomplete="off" palceholder="上次登录的天数"></el-input>
                    </el-form-item>
                    <el-form-item label="仙侣">
                        <el-input v-model="formData.xianlv" autocomplete="off" palceholder="仙侣"></el-input>
                    </el-form-item>
                    <el-form-item label="宠物">
                        <el-input v-model="formData.pet" autocomplete="off" palceholder="宠物"></el-input>
                    </el-form-item>
                    <el-form-item label="天女">
                        <el-input v-model="formData.tiannv" autocomplete="off" palceholder="天女"></el-input>
                    </el-form-item>
                    <el-form-item label="神将">
                        <el-input v-model="formData.tianshen" autocomplete="off" palceholder="神将"></el-input>
                    </el-form-item>
                    <el-form-item label="阵型">
                        <el-input v-model="formData.formation" autocomplete="off" palceholder="阵型"></el-input>
                    </el-form-item>
                    <el-form-item label="灵童">
                        <el-input v-model="formData.baby" autocomplete="off" palceholder="灵童"></el-input>
                    </el-form-item>
                    <el-form-item label="日常任务">
                        <el-input v-model="formData.daily_task" autocomplete="off" palceholder="日常任务"></el-input>
                    </el-form-item>
                    <el-form-item label="月卡周卡">
                        <el-input v-model="formData.welfare" autocomplete="off" palceholder="月卡周卡"></el-input>
                    </el-form-item>
                    <el-form-item label="进阶奖励">
                        <el-input v-model="formData.Advanced" autocomplete="off" palceholder="进阶奖励"></el-input>
                    </el-form-item>
                    <el-form-item label="答题">
                        <el-input v-model="formData.answer" autocomplete="off" palceholder="答题"></el-input>
                    </el-form-item>
                    <el-form-item label="商店">
                        <el-input v-model="formData.shop" autocomplete="off" palceholder="商店"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家帮派数据">
                        <el-input v-model="formData.guild_data" autocomplete="off" palceholder="玩家帮派数据"></el-input>
                    </el-form-item>
                    <el-form-item label="缘分配置">
                        <el-input v-model="formData.brother" autocomplete="off" palceholder="缘分配置"></el-input>
                    </el-form-item>
                    <el-form-item label="玩家福利数据">
                        <el-input v-model="formData.welfare_data" autocomplete="off" palceholder="玩家福利数据"></el-input>
                    </el-form-item>
                    <el-form-item label="充值记录">
                        <el-input v-model="formData.recharger_data" autocomplete="off" palceholder="充值记录"></el-input>
                    </el-form-item>
                    <el-form-item label="活动数据">
                        <el-input v-model="formData.activity_record" autocomplete="off" palceholder="活动数据"></el-input>
                    </el-form-item>
                    <el-form-item label="抽奖数据">
                        <el-input v-model="formData.luck" autocomplete="off" palceholder="抽奖数据"></el-input>
                    </el-form-item>
                    <el-form-item label="拍卖行">
                        <el-input v-model="formData.auction" autocomplete="off" palceholder="拍卖行"></el-input>
                    </el-form-item>
                    <el-form-item label="图腾">
                        <el-input v-model="formData.totems" autocomplete="off" palceholder="图腾"></el-input>
                    </el-form-item>
                    <el-form-item label="激活码">
                        <el-input v-model="formData.redeemcode" autocomplete="off" palceholder="激活码"></el-input>
                    </el-form-item>
                    <el-form-item label="我要变强">
                        <el-input v-model="formData.enhance" autocomplete="off" palceholder="我要变强"></el-input>
                    </el-form-item>
                    <el-form-item label="摇钱树">
                        <el-input v-model="formData.cashCow" autocomplete="off" palceholder="摇钱树"></el-input>
                    </el-form-item>
                    <el-form-item label="单人天降好礼">
                        <el-input v-model="formData.recharge_holyshit" autocomplete="off"
                                  palceholder="单人天降好礼"></el-input>
                    </el-form-item>
                    <el-form-item label="条件天降好礼">
                        <el-input v-model="formData.recharge_godlike" autocomplete="off"
                                  palceholder="条件天降好礼"></el-input>
                    </el-form-item>
                    <el-form-item label="阵位系统">
                        <el-input v-model="formData.position" autocomplete="off" palceholder="阵位系统"></el-input>
                    </el-form-item>
                </el-form>
            </div>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogVisible = false">取 消</el-button>
                <el-button type="primary" :loading="loading" @click="submitForm">{{ dialogBtnTxt }}</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import https from "../https";

export default {
    name: "Player",
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
        columnSearch: function () {
            this.params.currentPage = 1;
            this.search();
        },
        search: function () {
            this.loading = true;
            https.get("admin/players", this.params).then(resp => {
                this.rows = resp.data.data;
                this.meta = resp.data.meta;
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
            let params = Object.assign({}, this.formData);
            https.post("admin/players", params).then(resp => {
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
            let params = Object.assign({}, this.formData);
            https.put("admin/players/" + params.id, params).then(resp => {
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
            https.destroy("admin/players/" + row.id).then(resp => {
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
