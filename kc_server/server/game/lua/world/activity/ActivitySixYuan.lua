--6元充值
local oo = require "class"
local server = require "server"
local lua_app = require "lua_app"
local lua_util = require "lua_util"
local ItemConfig = require "resource.ItemConfig"
local ActCfg = require "resource.ActivityConfig"
local ActivityBaseType = require "activity.ActivityBaseType"

local ActivitySixYuan = oo.class(ActivityBaseType)

function ActivitySixYuan:ctor(activityId)
	self.levelRecords = {}
end

function ActivitySixYuan:GetMyConfig(activityId)
	return ActCfg:GetActConfig("ActivityType30Config", activityId)
end

function ActivitySixYuan:OpenHandler(activityId)
end

function ActivitySixYuan:AddRechargeCash(player, cashnum)
	for activityId,activity in pairs(self.activityListByType) do
		if activity.openStatus then
			local record = server.activityMgr:GetActData(player, activityId)
			local upCfg = self:GetMyConfig(activityId)
			record.rechargeNumber = record.rechargeNumber + cashnum
			-- print("-----ActivitySixYuan:AddRechargeCash----",activity.runday,record.rechargeNumber)
			local cfg = upCfg[1]
			if record.rechargeNumber >= cfg.cost then
				record.drawBin = math.max(record.drawBin, ActCfg.LevelStatus.ReachNoReward)
			end
			player.activityPlug:SetActData(activityId, record)
			self:SendActivityDataOne(player, activityId)
		end
	end
end

function ActivitySixYuan:OpenHandler(activityId)
	self:SetActivityDay(activityId)
end

function ActivitySixYuan:DayTimer()
	for activityId, activity in pairs(self.activityListByType) do
		self:SetActivityDay(activityId)
	end
end

function ActivitySixYuan:SetActivityDay(activityId)
	local activity = self.activityListByType[activityId]
	local runday = os.intervalDays(activity.startTime) + 1
	activity.runday = runday
end

function ActivitySixYuan:PackData(player, activityId)
	local activity = self.activityListByType[activityId]
	local data1 = {}
	data1.id = activityId
	data1.startTime = activity.startTime
	data1.endTime = activity.stopTime
	data1.type = activity.activityType
	data1.openState = activity.openStatus and 1 or 0

	local data2 = {}
	local record = server.activityMgr:GetActData(player, activityId)	
	data2.baseData = data1
	data2.runday = activity.runday
	data2.rechargeNumber = record.rechargeNumber
	data2.record = record.drawBin

	local data3 = {}
	data3.type29 = data2 
	return data3
end

function ActivitySixYuan:Reward(dbid, index, activityId)
	local activity = self.activityListByType[activityId]
	local player = server.playerCenter:GetPlayerByDBID(dbid)
	local record = server.activityMgr:GetActData(player, activityId)
	local data = player.activityPlug:PlayerData()
	local ActivityType1Config = self:GetMyConfig(activityId)

	-- print("-----ActivitySixYuan:AddRechargeCash----",activity.runday,record.rechargeNumber)
	local cfg = ActivityType1Config[1]
	if cfg == nil then
		lua_app.log_error("activity Oadvance config not exist")
		return
	end

	if record.drawBin ~= ActCfg.LevelStatus.ReachNoReward then
		server.sendErr(player, "领取失败")
		return
	end

	record.drawBin = ActCfg.LevelStatus.Reward
	player.activityPlug:GiveReward(activityId, record, table.wcopy(cfg.item), "6元充值")
	self:SendActivityDataOne(player, activityId)
	server.serverCenter:SendLocalMod("logic", "chatCenter", "ChatLink", 47, nil, nil, player.cache.name(), ItemConfig:ConverLinkText(cfg.item[1]), cfg.name)
end

return ActivitySixYuan
