local lua_app = require "lua_app"
local server = require "server"

local _beforeupdate = {}
local MysqlConfig = {
beforeupdate = _beforeupdate,
update = require "mysql.update",
db = {

players = {
	columns = {
		{ "dbid" 				,"bigint(20)"		,0		,"玩家账号ID" },
		{ "account" 			,"varchar(128)"		,""		,"玩家账号" },
		{ "serverid" 			,"int(11)"			,0		,"创建服务器" },
		{ "name" 				,"varchar(128)"		,""		,"玩家名称" },
		{ "sealed" 				,"int(11)"			,0		,"封号时间" },
		{ "silent" 				,"int(11)"			,0		,"禁言时间" },
		{ "createtime" 			,"int(11)"			,0		,"玩家创建时间" },
		{ "lastonlinetime" 		,"int(11)"			,0		,"最后登录时间" },
		{ "createip" 			,"varchar(128)"		,""		,"创建账号的IP" },
		{ "lastloginip" 	 	,"varchar(128)"		,""		,"上次登录的IP" },
		{ "job" 				,"tinyint(4)"		,0		,"职业" },
		{ "sex" 				,"tinyint(4)"		,0		,"性别" },
		{ "level" 	 			,"int(11)"			,1		,"玩家等级" },
		{ "totalpower" 	 		,"bigint(20)"		,0		,"玩家战力" },
		{ "exp" 				,"bigint(20)"		,0		,"经验值" },
		{ "gold" 	 			,"bigint(20)"		,0		,"金钱" },
		{ "yuanbao" 	 		,"bigint(20)"		,0		,"元宝" },
		{ "byb" 	 			,"bigint(20)"		,0		,"绑定的元宝" },
		{ "recharge" 	 		,"float"			,0		,"充值金额" },
		{ "rechargenotice" 		,"mediumblob"		,{}		,"充值公告记录" },
		{ "recharge_maxone" 	,"float"			,0		,"单笔最大充值金额" },
		{ "recharge_lasttime" 	,"int(11)"			,0		,"最后充值时间" },
		{ "vip" 	 			,"int(10)"			,0		,"vip等级" },
		{ "vipstate" 	 		,"int(10)"			,0		,"vip领奖记录" },
		{ "vipaddedreward" 		,"int(10)"			,0		,"vip额外奖励" },
		{ "bagnum" 	 			,"int(11)"			,0		,"背包的格子数量" },
		{ "guildid" 	 		,"bigint(20)"		,0		,"公会ID" },
		{ "contrib" 			,"bigint(20)"		,0		,"公会贡献" },
		{ "clientvalue" 	 	,"int(11)"			,0		,"客户端保存的数据" },
		{ "clientvaluelist" 	,"mediumblob"		,{}		,"客户端保存的数据列表" },
		{ "global_mails" 		,"mediumblob"		,{}		,"已获取的全局邮件" },
		{ "exchange_data"		,"mediumblob"		,{}		,"玩家兑换数据" },
		{ "escort_data"			,"mediumblob"		,{}		,"玩家护送数据" },
		{ "friend_data"			,"mediumblob"		,{}		,"玩家好友数据" },
		{ "welcome" 	 		,"int(11)"			,1		,"欢迎提示" },
		{ "openfuncstate" 	 	,"int(11)"			,0		,"功能预告奖励状态" },
		{ "rankworship" 	 	,"int(11)"			,0		,"排行榜膜拜" },
		{ "renamecount" 	 	,"int(11)"			,0		,"改名次数" },
		{ "head"				,"mediumblob"		,{
				frame = 0,
				term = 0,
		}		,"头像框" },
		{ "marry" 	 			,"mediumblob"		,{
				partnerid = 0,
				partnername = "",
				spouse = 0,
				time = 0,
				level = 1,
				exp = 0,
				intimate = 0,	-- 甜蜜
				intimacy = 0,	-- 亲密
				grade = 0,
				houselv = 1,
				houseup = 0,
				partnerhouseup = 0,
				partnerhouseuptime = {},
				loves = {},
				ex = {},
		}		,"结婚"},
		{ "chapter" 	 		,"mediumblob"		,{
				chapterlevel = 1,
				chapterreward = {},
				nextmap = false,
				autopk = false,
				appealtime = 0,
				helptime = 0,
		}		,"章节关卡" },
		{ "task" 	 		,"mediumblob"		,{
				tasks = {},
				record = {},
		}		,"任务数据" },
		{ "catchpet" 	 		,"mediumblob"		,{
				state = 0,
		}		,"宠物捕捉时间" },
		{ "material" 		,"mediumblob"		,{
				clearanceNum = {},
				todayNum = {},
				buyNum = {},
				bossNum = 0,
				materialNum = 0,
			}		,"材料副本数据" },
		{ "treasuremap" 		,"mediumblob"		,{
				clearanceNum = {},
				todayNum = {},
				star = {},
				starReward = {},
			}		,"藏宝图副本数据" },
		{ "wildgeeseFb" 		,"mediumblob"		,{
				hard = 1,
				layer = 0,
			}		,"大雁塔副本数据" },
		{ "heavenFb" 		,"mediumblob"		,{
				layer = 0,
				todayLayer = 0,
				rewardNo = {},
			}		,"勇闯天庭本数据" },
		{ "publicboss" 		,"mediumblob"		,{
				rebornmark = 0,
				deficount = 0,
				refreshtime = 1,
				purchasecount = 0,
				reborncount = 0,
				totalcount = 0, --总次数
			}		,"全民Boss副本数据" },
		{ "vipboss" 		,"mediumblob"		,{
				list = {},
			}		,"至尊Boss副本数据" },
		{ "arena" 		,"mediumblob"		,{
				medal = 0,
				pkcount = 0,
				reverttime = 0,
				maxrank = 0,
				buycount = 0,
				buytime = 0,
			}		,"竞技场数据" },
		{ "crossTeamFb" 		,"mediumblob"		,{
				clearlist = {},
				rewardcount = 0,
				lastresettime = 0,
			}		,"跨服组队" },
		{ "eightyOneHard" 		,"mediumblob"		,{
				helpReward = 0,
				buy = {},
				clear = 0,
				todayClearlist = {},
			}		,"八十一难" },
		{ "totalloginday" 		,"int(11)"			,1		,"总登录天数" },
		{ "lastloginday"		,"int(11)"			,1		,"上次登录的天数" },
		{ "xianlv"				,"mediumblob"		,{
				list = {},				-- 仙侣列表
				outbound = {0,0},		-- 出站顺序
				totalpower = 0,			-- 总战力
				xianlv_position_data = {	--仙侣仙位
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
				xianlv_circle_data = {		--仙侣法阵
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
			}		,"仙侣" },
		{ "pet"					,"mediumblob"		,{
				list = {},			-- 宠物列表
				outbound = {0,0,0,0},	-- 出站顺序
				totalpower = 0,		-- 总战力
				pet_soul_data = {	--宠物通灵
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
				pet_psychic_data = {	--宠物兽魂
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
			}		,"宠物" },
		{ "tiannv"				,"mediumblob"		,{
				attrdatas = {
					attrs = {},
				},			-- 应用中的属性和技能
				refreshattr = {},		-- 备用替换技能
				tiannv_data = {	--天女,没有装备和技能
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
				totalpower = 0,			-- 总战力
				tiannv_nimbus_data = {		--天女灵气
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
				tiannv_flower_data = {		--天女花辇
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
			}		,"天女" },
		{ "tianshen"				,"mediumblob"		,{
				use = 0,
				list = {},
				drugNum = 0,
				tianshen_spells = {},
				totalpower = 0,			-- 总战力
			}		,"神将" },
		{ "formation"				,"mediumblob"		,{
				use = 0,
				list = {},
				drugNum = 0,
			}		,"阵型" },
		{ "baby"					,"mediumblob"		,{
				sex = 0,
				open = 0,
				name = "",
				buffs = {},
				bufftypes = {},
				giftexp	= 0,
				giftlv	= 1,
				xilian	= 0,
				totalpower = 0,		-- 总战力
				baby_data = {	--灵童
					totalpower = 0,
					startUp = 0,
					upNum = 0,
					drugNum = 0,
					lv = 1,
					useClothes = 1,
					clothesList = {},
					skillList = {},
					equipList = {},
					rewards = 0,
					attrTitle = 0,
				},
				baby_star = {
					data = {},
					star = 1,
					isBuy = 0,
				},
			}		,"灵童" },
		{ "daily_task"				,"mediumblob"		,{
				lv = 1,
				exp = 0,
				openDay = 0,
				find = 0,
				today = {},
				yesterday = {},
				findData = {
					item = {},
					exp = {},
				},
				active = 0,
				activeReward = 0,
				otherActivity = {
					monster = {
						monsterList = {},
						num = 0,
						time = 0,
						timeout = 0,
						reward = 0,
					},
					chapterWar = {
						num = 0,
						reward = 0,
					},
					teamFB = {
						num = 0,
						reward = 0,
					},
				},
			}		,"日常任务" },
		{ "welfare"				,"mediumblob"		,{
				month = 0,
				week = 0,
				monthReward = 0,
				weekReward = 0,
				lvReward = 0,
				welfareReward = 0,
				firstMonth = 0,
				forever = 0,
			}		,"月卡周卡" },
		{ "Advanced"				,"mediumblob"		,{
				typ = 0,
				dayAllCharger = 0, 		--每日累计充值
				chargerReward = {}, 	--每日累计充值奖励
				advancedReward = {}, 	--进阶奖励领取情况
				advancedShop = {}, 		--进阶商店购买情况
			}		,"进阶奖励" },
		{ "answer"				,"mediumblob"		,{
				answerNum = 0,
				answerBuy = 0,
			}		,"答题" },
		{ "shop"					,"mediumblob"		,{
				mystical = {
					refreshcount = 0,
					refreshtime = -1,
					datas = {}
				},
			}		,"商店" },
		{ "guild_data"					,"mediumblob"		,{
				guilddata = {},
				applyrecord = {},
			}		,"玩家帮派数据" },
		{ "brother" 	 		,"mediumblob"		,{
				data = {}
		}		,"缘分配置" },
		{ "welfare_data"		,"mediumblob"		,{
				signin = {},	--签到
				logingift = {
					receivemark = 0,
				},
		}		,"玩家福利数据" },
		{ "recharger_data" 	 		,"mediumblob"		,{
				dailyrechare = 0, 	--每日充值
				doubleCharger = 0, 		--首充双倍
				doubleChargerList = {},
				lastday = 0,
				daycount = 0,
				daycash = 0,
				firstReward = 0,
				firstRewardList = {},
				dailyid = 1,
				rewardmark = 0,
				total = 0,
				choicerechare = 0,
				firsttime = 0,
				finishList = {},	--已充过的套餐列表
		}		,"充值记录" },
		{ "activity_record"		,"mediumblob"		,{
				list = {},	--活动记录
				acculogin = {}
		}		,"活动数据" },
		{ "luck"				,"mediumblob"		,{
				list = {},	--抽奖数据
				daylist = {}, --每天数据
				lucky = 0,
				lastlucky = 0,
				round = 1,	--轮
				equiplist = {}, --神装抽奖数据
				equipdaylist = {}, --神装每天抽奖数据
				equiplucky = 0,
				equiplastlucky = 0,
				equipround = 1,	--轮
				
				totemslist = {}, --图腾抽奖数据
				totemsdaylist = {}, --图腾每天抽奖数据
				totemslucky = 0,
				totemslastlucky = 0,
				totemsround = 1,	--轮

				tianshen = {
					num = 0,
					tenNum = 0,
					allNum = 0,
					reward = 0,
				},
		}		,"抽奖数据" },
		{ "auction"				,"mediumblob"		,{
				aucratio = 0,		--拍卖行额度(充值)
				aucratioAct = 0,	-- 拍卖行额度(活跃)
				lockratio = 0
		}		,"拍卖行" },
		{ "totems"				,"mediumblob"		,{
				data = {},
			}		,"图腾" },
		{ "redeemcode"			,"mediumblob"		,{
				list = {},
			}		,"激活码" },
			
		{ "enhance"				,"mediumblob"		,{
				day = 0,
				data = {},
				point = 0,
				rewards ={},
			}		,"我要变强" },
		{ "cashCow"				,"mediumblob"		,{
				level = 1,
				exp = 0,
				odds = 1,
				shake = 0,
				drawBin = {},
			}		,"摇钱树" },
		{ "recharge_holyshit"	,"mediumblob"		,{}		,"单人天降好礼" },
		{ "recharge_godlike"	,"mediumblob"		,{}		,"条件天降好礼" },
		{ "position"				,"mediumblob"		,{
				data = {},
			}		,"阵位系统" },
	},
	prikey = { "dbid" },
	key = {
		account = { "account" },
		name = { "name" },
	},
	comment = "玩家基础表",
	initdbid = "platonly",
},
roles = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,""				,"AUTO_INCREMENT"},
		{ "playerid"		,"bigint(20)"		,0		,"玩家账号ID" },
		{ "totalpower" 		,"bigint(20)"		,0		,"战力" },
		{ "skill" 			,"mediumblob"		,{
				skillLevel = {0, 0, 0, 0, 0, 0, 0, 0},
				skillSort = {4, 2, 1, 3, 5, 6, 7, 8},
			}		,"技能数据" },
		{ "ride_data" 		,"mediumblob"		,{
				totalpower = 0,
				startUp = 0,
				upNum = 0,
				drugNum = 0,
				lv = 1,
				useClothes = 1,
				clothesList = {},
				skillList = {},
				equipList = {},
				rewards = 0,
				attrTitle = 0,
			}		,"坐骑数据" },
		{ "wing_data" 		,"mediumblob"		,{
				totalpower = 0,
				startUp = 0,
				upNum = 0,
				drugNum = 0,
				lv = 1,
				useClothes = 1,
				clothesList = {},
				skillList = {},
				equipList = {},
				rewards = 0,
				attrTitle = 0,
			}		,"翅膀数据" },
		{ "fairy_data" 		,"mediumblob"		,{
				totalpower = 0,
				startUp = 0,
				upNum = 0,
				drugNum = 0,
				lv = 1,
				useClothes = 1,
				clothesList = {},
				skillList = {},
				equipList = {},
				rewards = 0,
				attrTitle = 0,
			}		,"天仙数据" },
		{ "weapon_data" 		,"mediumblob"		,{
				totalpower = 0,
				startUp = 0,
				upNum = 0,
				drugNum = 0,
				lv = 1,
				useClothes = 1,
				clothesList = {},
				skillList = {},
				equipList = {},
				rewards = 0,
				attrTitle = 0,
			}		,"神兵数据" },
		{ "equips_data" 	,"mediumblob"		,{
				equipList = {},
				suitlv = 0,
			}		,"装备数据" },
		{ "title_data" 	,"mediumblob"		,{
				ownlist = {},
				termlist = {},	-- 有效期
				wearid = 0,
			}		,"称号数据" },
		{ "skin_data" 	,"mediumblob"		,{
				ownlist = {},
				termlist = {},	-- 有效期
				wearid = 0,
			}		,"皮肤数据" },
		{ "vein_data" 	,"mediumblob"		,{
				level = 0,
			}		,"经脉数据" },
		{ "panacea_data" 	,"mediumblob"		,{
				lvlist = {},
				allattrs = {},
			}		,"丹药数据" },
		{ "spells_res" 	,"mediumblob"		,{
				key = 1 ,
				perfectNum = 0,
				useSpells = {},
				spellsList = {},
			}		,"法宝数据" },
	},
	prikey = { "dbid" },
	key = {
		playerid = { "playerid" }
	},
	comment = "角色基础表",
},
items = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,""				,"AUTO_INCREMENT"},
		{ "playerid"		,"bigint(20)"		,0		,"玩家账号ID" },
		{ "bag_type" 		,"int(11)"			,0		,"背包类型" },
		{ "id" 				,"bigint(20)"		,0		,"物品配置id" },
		{ "count" 			,"bigint(20)"		,1		,"物品数量" },
		{ "attrs" 			,"mediumblob"		,{}		,"物品属性" },
		{ "invalidtime" 	,"int(11)"			,0		,"物品到期时间" },
	},
	prikey = { "dbid" },
	key = {
		playerid = { "playerid" },
	},
	comment = "物品表",
	initdbid = "tableonly",
},
mails = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,""				,"AUTO_INCREMENT"},
		{ "playerid"		,"bigint(20)"		,0		,"玩家账号ID" },
		{ "readstatus" 		,"int(11)"			,0		,"邮件读取状态" },
		{ "sendtime" 		,"int(11)"			,0		,"邮件发送时间" },
		{ "head" 			,"varchar(256)"		,""		,"邮件标题" },
		{ "context" 		,"varchar(4096)"	,""		,"邮件内容" },
		{ "award" 			,"mediumblob"		,{}		,"奖励的物品" },
		{ "awardstatus" 	,"int(11)"			,0		,"奖励物品的领奖状态" },
		{ "log_type" 		,"int(11)"			,0		,"邮件奖励记录类型" },
		{ "log" 			,"varchar(128)"		,""		,"邮件奖励记录" },
	},
	prikey = { "dbid" },
	key = {
		playerid = { "playerid" },
	},
	comment = "邮件表",
},
global_mails = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,"全局邮件id"			,"AUTO_INCREMENT"},
		{ "sendtime" 		,"int(11)"			,0		,"邮件发送时间" },
		{ "head" 			,"varchar(256)"		,""		,"邮件标题" },
		{ "context" 		,"varchar(4096)"	,""		,"邮件内容" },
		{ "award" 			,"mediumblob"		,{}		,"奖励的物品" },
		{ "log_type" 		,"int(11)"			,0		,"邮件奖励记录类型" },
		{ "log" 			,"varchar(128)"		,""		,"邮件奖励记录" },
	},
	prikey = { "dbid" },
	comment = "全局邮件表",
},
records = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,"主键"			,"AUTO_INCREMENT"},
		{ "type"			,"bigint(20)"		,0		,"记录类型" },
		{ "id"				,"bigint(20)"		,0		,"记录索引" },
		{ "record" 			,"mediumblob"		,{}		,"记录内容" },
	},
	prikey = { "dbid" },
	comment = "记录表",
},
--add wupeng
pay = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,""				,"AUTO_INCREMENT"},
		{ "playerid"		,"bigint(20)"		,0		,"玩家id" },
		{ "serverid" 		,"int(11)"			,0		,"服务器id" },
		{ "goodsid" 		,"int(11)"			,0		,"充值档位" },
	},
	prikey = { "dbid" },
	key = {
		serverid = { "serverid" },
	},
	comment = "充值表",	
},
--add wupeng
gmcmd = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,""				,"AUTO_INCREMENT"},
		{ "serverid" 		,"int(11)"			,0		,"服务器id" },		
		{ "cmd" 			,"varchar(256)"		,0		,"GM指令" },
		{ "param1" 			,"varchar(256)"		,0		,"参数1" },
		{ "param2" 			,"varchar(256)"		,0		,"参数2" },
		{ "param3" 			,"varchar(256)"		,0		,"参数3" },
		{ "param4" 			,"varchar(256)"		,0		,"参数4" },
		{ "param5" 			,"varchar(256)"		,0		,"参数5" },
		{ "param6" 			,"varchar(256)"		,0		,"参数6" },
		{ "param7" 			,"varchar(256)"		,0		,"参数7" },
	},
	prikey = { "dbid" },
	key = {
		serverid = { "serverid" },
	},
	comment = "GM命令表",
},
globaluser = {
	columns = {
		{ "account"			,"varchar(128)"		,""		,"玩家账号" },
		{ "password" 		,"varchar(128)"		,""		,"密码" },
	},
	prikey = { "account" },
	comment = "账号管理表",	
},
--add wupeng
log = {
	columns = {
		{ "dbid" 			,"bigint(20)"		,0		,""				,"AUTO_INCREMENT"},		
		{ "serverid" 		,"int(11)"			,0		,"服务器id" },		
		{ "type" 			,"varchar(256)"		,""		,"上报类型" },
		{ "log_time" 		,"varchar(128)"		,""		,"上报时间" },
		{ "value1" 			,"varchar(256)"		,""		,"参数1" },
		{ "value2" 			,"varchar(256)"		,""		,"参数2" },
		{ "value3" 			,"varchar(256)"		,""		,"参数3" },
		{ "value4" 			,"varchar(256)"		,""		,"参数4" },
		{ "value5" 			,"varchar(256)"		,""		,"参数5" },
		{ "value6" 			,"varchar(256)"		,""		,"参数6" },		
	},
	prikey = { "dbid" },
	comment = "log表",
},
guild = {
	columns = {
		{ "dbid"			,"bigint(20)"		,0		,"公会id" },
		{ "name"			,"varchar(128)"		,""		,"公会名称" },
		{ "serverid" 		,"int(11)"			,0		,"公会创建所在服务器" },
		{ "changename_count","int(11)"			,0		,"剩余改名次数" },
		{ "players"			,"mediumblob"		,{}		,"公会成员表" },
		{ "variable"		,"mediumblob"		,{}		,"公会其他数据" },
		{ "records"			,"mediumblob"		,{}		,"公会信息记录" },
	},
	prikey = { "dbid" },
	comment = "公会表",
	initdbid = "platonly",
},
arena = {
	columns = {
		{ "rank"			,"int(11)"			,0		,"排名" },
		{ "playerid"		,"bigint(20)"		,0		,"玩家id" },
		{ "updatetime" 		,"int(11)"			,0		,"更新时间" }
	},
	prikey = { "rank" },
	comment = "竞技表",
},
--add by tang begin---
login = {
columns = {
{ "user_id","varchar(128)","","user_id"},
{ "role_id","bigint(20)",0,"role_id"},
{ "role_name","varchar(128)","","role_name"},
{ "role_level","int(11)",1,"role_level"},
{ "role_c_time","int(11)",0,"role_c_time"},
{ "zone_id","int(11)",0,"zone_id"},
{ "zone_name","varchar(128)","","zone_name"},
{ "login_time","int(11)",0,"login_time"},
{ "channel","varchar(128)","","channel"},
{ "platform","varchar(128)","","platform"},
},
prikey = { "user_id"},
comment = "login表",
},
behavior = {
columns = {
{ "uniquevalue","int(11)",1,"类型唯一","AUTO_INCREMENT"},
{ "dbid","bigint(20)",0,"玩家ID"},
{ "serverid","tinyint(4)",1,"服务器id"},
{ "account" ,"varchar(128)"	,"","玩家账号" },
{ "curTime","int(11)",0,"记录时间"},
{ "rechargeCash","bigint(20)",0,"充值金额"},
{ "rechargeData","varchar(128)","","充值类型和金额"},
{ "getYuanbao","int(11)",0,"获得元宝"},
{ "costYuanbao","int(11)",0,"消耗元宝"},
{ "costYuanbao_getItem","mediumblob",{},"元宝购买的道具ID和数量"},
{ "getBindYuanbao","int(11)",0,"获得绑定元宝数量"},
{ "costBindYuanbao_getItem","mediumblob",{},"消耗绑定元宝购买的道具ID和数量"},
{ "getCoin","bigint(20)",0,"获得银币数量"},
{ "playerLv","int(11)",1,"玩家等级"},
{ "allExp","bigint(20)",0,"玩家总经验"},
{ "mainTaskId","int(11)",0,"当前进行中的主线任务ID"},
{ "equipId","mediumblob",{},"已装备的所有装备ID"},
{ "divineEquipLv","mediumblob",{},"所有神装觉醒等级"},
{ "divineEquipCost","mediumblob",{},"神装觉醒消耗"},
{ "fabaoCost","mediumblob",{},"装备的法宝数量和等级"},
{ "guardLv","int(11)",0,"守护等级"},
{ "guardCost","mediumblob",{},"守护升阶消耗"},
{ "shenbingLv","int(11)",0,"神兵等级"},
{ "shenbingCost","mediumblob",{},"神兵升阶消耗"},
{ "medicinalLv","mediumblob",{},"每个丹药的等级"},
{ "meridiansLv","int(11)",0,"经脉等级"},
{ "playerSkillLv","mediumblob",{},"主角每个技能等级"},
{ "mountLv","int(11)",0,"坐骑等级"},
{ "mountCost","mediumblob",{},"坐骑升阶消耗"},
{ "wingLv","int(11)",0,"翅膀等级"},
{ "wingCost","mediumblob",{},"翅膀升阶消耗"},
{ "strengthenLv","int(11)",0,"强化等级"},
{ "refineLv","int(11)",0,"精炼等级"},
{ "exerciseLv","int(11)",0,"锻练等级"},
{ "gemstoneLv","int(11)",0,"宝石等级"},
{ "soulboyLv","int(11)",0,"灵童等级"},
{ "soulboyCost","mediumblob",{},"灵童升阶消耗"},
{ "soulboyTalentLv","int(11)",0,"灵童天赋等级"},
{ "lifestyleLv","mediumblob",{},"灵童装备的每个命格等级"},
{ "xianlvId","mediumblob",{},"获得的所有的仙侣ID"},
{ "xianlvLv","mediumblob",{},"获得的每个仙侣阶数"},
{ "xianlvLvCost","mediumblob",{},"获得的每个仙侣升阶消耗"},
{ "xianlvFazhenLv","int(11)",0,"仙侣法阵等级"},
{ "xianlvFazhenCost","mediumblob",{},"仙侣法阵升阶消耗"},
{ "xianlvXianweiLv","int(11)",0,"仙侣仙位阶数"},
{ "xianlvXianweiCost","mediumblob",{},"仙侣仙位升阶消耗"},
{ "xuannvLv","int(11)",0,"玄女阶数"},
{ "xuannvCost","mediumblob",{},"玄女升阶消耗"},
{ "xuannvClearCost","mediumblob",{},"玄女洗练消耗"},
{ "xuannvHuanianLv","int(11)",0,"玄女花辇阶数"},
{ "xuannvHuanianCost","mediumblob",{},"玄女花辇升阶消耗"},
{ "xuannvAuraLv","int(11)",0,"玄女灵气阶数"},
{ "xuannvAuraCost","int(11)",0,"玄女灵气升阶消耗"},
{ "petId","mediumblob",{},"获得的每个宠物ID"},
{ "petLv","mediumblob",{},"每个宠物的等级"},
{ "petQualificationsLv","mediumblob",{},"宠物资质等级"},
{ "petSkillLv","mediumblob",{},"宠物技能等级"},
{ "petSkillCoat","mediumblob",{},"宠物技能升级消耗"},
{ "petClearCoat","mediumblob",{},"宠物洗练消耗"},
{ "petTonglingLv","int(11)",0,"宠物通灵阶数"},
{ "petTonglingCost","mediumblob",{},"宠物通灵升阶消耗"},
{ "petShouhunLv","int(11)",0,"宠物兽魂阶数"},
{ "petShouhunCost","mediumblob",{},"宠物兽魂升阶消耗"},
{ "mapNum","int(11)",0,"玩家当前藏宝图关数"},
{ "linglongbaotaNum","int(11)",0,"玩家当前玲珑宝塔层数"},
{ "yongchuangtiantingNum","int(11)",0,"当前勇闯天庭层数"},
},
prikey = { "uniquevalue" },
	key = {
		dbid = { "dbid" },
		account = { "account" },
		curTime = { "curTime" },
	},
comment = "玩家行为表",
},
orders = {
columns = {
{ "dbid","bigint(20)",0,""},
{ "serverid","int(11)",0,"服务器id"},
{ "account","varchar(128)","",""},
{ "cp_no","varchar(128)","","订单号"},
{ "goodsid","int(11)",0,"充值档位"},
{ "goodsName","varchar(128)","","商品名称"},
{ "ct_time","int(11)",0,"创建时间"},
{ "end_time","int(11)",0,"完成时间"},
{ "is_verify_sign","tinyint(4)",0,""},
{ "verify_result","tinyint(4)",0,"验签结果 0失败 1成功"},
{ "sign","varchar(1024)","","签名"},
{ "pay_channel","int(11)",0,"渠道id"},
{ "app_trade_no","varchar(128)","",""},
{ "out_trade_no","varchar(128)","",""},
{ "amount","double",0,"商品价格"},
{ "complete","tinyint(4)",0,""},
},
prikey = { "cp_no"},
comment = "订单表",
},
whitelist = {
columns = {
{ "ip","varchar(128)","",""},
{ "name","varchar(128)","",""},
},
prikey = { "ip"},
comment = "白名单",
},
-------- add by tang end ------
datalist = {
	columns = {
		{ "uniquevalue"		,"int(11)"			,1		,"类型唯一" },
		{ "timers"			,"mediumblob"		,{
				serverOpenTime = 0,
				serverRunDay = 1,
			}		,"时间值" },
		{ "escort"			,"mediumblob"		,{
				escortList = {},
			}		,"西游护送" },
		{ "kfboss"			,"mediumblob"		,{
				ranklist = {},
				first = {},
				firstopen = true,
			}		,"跨服Boss" },
		{ "guildboss"			,"mediumblob"		,{
				ranklist = {},
				first = {},
				firstopen = true,
			}		,"帮会Boss" },
		{ "eightyonehard"		,"mediumblob"		,{
				first = {},
				fast = {},
				firstReward = {},
			}		,"八十一难" },
		{ "teachers"		,"mediumblob"		,{
				no = 1,
				msessage = {},
				dataKey = {},
				data = {},
			}		,"师徒" },
		{ "guildwar"		,"mediumblob"		,{
					champions = {},
					openinterval = 0,
				}		,"帮会战" },
		{ "babystar"		,"mediumblob"		,{
					data = {},
				}		,"灵童命格" },
		{ "holyPetData"		,"mediumblob"		,{
					version = 0,
					starTime = 0,
					endTime = 0,
					data = {},
					playerList = {},
					luckLog = {},
				}		,"神兽活动" },
		{ "catchpet"		,"mediumblob"		,{
				waitfinishlist = {},
				}		,"捉宠" },
		{ "climb"		,"mediumblob"		,{
				open = 0,
				openinterval = 0,
				}		,"九重天" },
		{ "kingarena"		,"mediumblob"		,{
				athletelist = {},
				priorathletelist = {},
				openStatus = 0,
				rewardslist = {},
				kingrecord = {},
				priorranks = {},
				worshipRecord = {},
				}		,"王者争霸" },
		{ "recharge_godlike"		,"mediumblob"		,{}		,"天降好礼" },
	},
	prikey = { "uniquevalue" },
	comment = "其他数据表",
	uniquevalue = true,
},

},
}
_beforeupdate[1] = function(self, conn)
	conn:call_execute("ALTER TABLE `records` ADD COLUMN `dbid` bigint(20) NULL AUTO_INCREMENT COMMENT '主键' FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (`dbid`);")
end

server.SetCenter(MysqlConfig, "mysqlConfig")
return MysqlConfig
