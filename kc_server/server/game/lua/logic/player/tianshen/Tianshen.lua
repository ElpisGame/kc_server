local oo = require "class"
local server = require "server"
local lua_app = require "lua_app"
local ItemConfig = require "resource.ItemConfig"
local LogicEntity = require "modules.LogicEntity"

local Tianshen = oo.class(LogicEntity)

function Tianshen:ctor(player)
	-- local femaleDevaBaseConfig = server.configCenter.FemaleDevaBaseConfig
	-- FightConfig.FightStatus.Running
	-- self.skilllist = {{[3] = {11001}}}
	-- self:AddSkill(11001, true, 1)
	-- self.hskillNo = femaleDevaBaseConfig.hskill
	self.player = player
	self.YuanbaoRecordType = server.baseConfig.YuanbaoRecordType.TianShenSpells
end

function Tianshen:onCreate()
	self:onLoad()
end

function Tianshen:onLoad()
	--登录相关数据加载
	self.cache = self.player.cache.tianshen
	if #self.cache.list == 0 then return end
	local baseConfig = server.configCenter.AirMarshalBaseConfig
	self:AddSkill(baseConfig.skill, true, 1)
	self.player.role:AddSkill(baseConfig.hskill, true, 1)
	local allattr = self:Allattr()
	self:UpdateBaseAttr({}, allattr, server.baseConfig.AttrRecord.Tianshen)
	self:AddAllSkill()
end

function Tianshen:packInfo()
	local msg = {
		use = self.cache.use,
		infoList = {},
		drugNum = self.cache.drugNum,
	}
	for k,v in pairs(self.cache.list) do
		table.insert(msg.infoList,{
			no = k,
			lv = v.lv,
			upNum = v.upNum,
			promotion = v.promotion,
		})
	end
	return msg
end

function Tianshen:AddAllSkill()
	if self.use == 0 then return end
	local data = self.cache.list[self.use]
	local breachConfig = self:GetAirMarshalBreachConfig(self.use)
	local newBreachConfig = breachConfig[data.promotion]
	local newSkillid = newBreachConfig.skillid
	if newSkillid then
		for _,v in pairs(newSkillid) do
			self:AddSkill(v, true, 1)
		end
	end
end

function Tianshen:AddAllSkill()
	if self.use == 0 then return end
	local data = self.cache.list[self.use]
	local breachConfig = self:GetAirMarshalBreachConfig(self.use)
	local newBreachConfig = breachConfig[data.promotion]
	local newSkillid = newBreachConfig.skillid
	if newSkillid then
		for _,v in pairs(newSkillid) do
			self:AddSkill(v, true, 1)
		end
	end
end

function Tianshen:Allattr()
	--登录初始化数据
	local attrsList = {}
	--算属性，
	for k,v in pairs(self.cache.list) do
		local data = slef:GetAirMarshalListConfig(k)
		for _,vv in pairs(data.attrs) do
			table.insert(attrsList, vv)
		end

		local lvproConfig = self:GetAirMarshalLvproConfig(data.quality)
		for i = 1, v.lv - 1 do
			if lvproConfig[i].attrs then
				for _,vv in pairs(lvproConfig[i].attrs) do
					local attr = table.wcopy(vv)
					attr.value = attr.value * attr.upnum
					table.insert(attrsList, attr)
				end
			end
		end
		local attr = table.wcopy(lvproConfig[v.lv].attrs) or {}
		for _,v in pairs(attr) do
			attr.value = attr.value * v.upNum
			table.insert(attrsList, v)
		end
		local attr = self:GetAirMarshalAttrsConfig(data.quality, v.lv)
		for _,v in pairs(attr.attrs) do
			table.insert(attrsList, v)
		end
	end
	return attrs
end

-- function Tianshen:onLevelUp(oldlevel,level)
-- 	--这里要读下表拿28
-- 	local lv = server.configCenter.FuncOpenConfig[28].conditionnum
-- 	if #self.cache.attrdatas.attrs > 0 then return end
-- 	if lv <= level then
-- 		self:Open(1)
-- 	end
-- end

-- function Tianshen:Open()

function Tianshen:onInitClient()
	local msg = self:packInfo()
	server.sendReq(self.player, "sc_tianshen_info", msg)
end

function Tianshen:onLogout()
end

local _airMarshalSynthesisConfig = false
function Tianshen:GetAirMarshalSynthesisConfig(no)
	if _airMarshalSynthesisConfig then return _airMarshalSynthesisConfig[no] end
	_airMarshalSynthesisConfig = server.configCenter.AirMarshalSynthesisConfig
	return _airMarshalSynthesisConfig[no]
end

local _airMarshalListConfig = false
function Tianshen:GetAirMarshalListConfig(no)
	if _airMarshalListConfig then return _airMarshalListConfig[no] end
	_airMarshalListConfig = server.configCenter.AirMarshalListConfig
	return _airMarshalListConfig[no]
end

local _airMarshalBaseConfig = false
function Tianshen:GetAirMarshalBaseConfig(no)
	if _airMarshalBaseConfig then return _airMarshalBaseConfig[no] end
	_airMarshalBaseConfig = server.configCenter.AirMarshalBaseConfig
	return _airMarshalBaseConfig[no]
end

local _airMarshalLvproConfig = false
function Tianshen:GetAirMarshalLvproConfig(no)
	if _airMarshalLvproConfig then return _airMarshalLvproConfig[no] end
	_airMarshalLvproConfig = server.configCenter.AirMarshalLvproConfig
	return _airMarshalLvproConfig[no]
end

local _airMarshalAttrsConfig = false
function Tianshen:GetAirMarshalAttrsConfig(no, key)
	if _airMarshalAttrsConfig then return _airMarshalAttrsConfig[no][key] end
	_airMarshalAttrsConfig = server.configCenter.AirMarshalAttrsConfig
	return _airMarshalAttrsConfig[no][key]
end

local _airMarshalBreachConfig = false
function Tianshen:GetAirMarshalBreachConfig(no)
	if _airMarshalBreachConfig then return _airMarshalBreachConfig[no] end
	_airMarshalBreachConfig = server.configCenter.AirMarshalBreachConfig
	return _airMarshalBreachConfig[no]
end

function Tianshen:Exchange(no)
	local synthesisConfig = self:GetAirMarshalSynthesisConfig(no)
	if not synthesisConfig then return end
	if not self.player:PayRewards({synthesisConfig.cost}, self.YuanbaoRecordType) then return end
	self.player:GiveReward(ItemConfig.AwardType.Item, no, 1, 1, self.YuanbaoRecordType)
end

function Tianshen:Activation(no)
	local open = self:GetAirMarshalBaseConfig("open")
	local lv = server.configCenter.FuncOpenConfig[open].conditionnum
	if lv > self.player.cache.level then return {ret = false} end
	if self.cache.list[no] then return {ret = false} end
	local listConfig = self:GetAirMarshalListConfig(no)
	if not listConfig then return {ret = false} end
	if not self.player:PayRewards({listConfig.material}, self.YuanbaoRecordType) then return {ret = false} end
	self.cache.list[no] = {
		lv = 1,
		upNum = 0,
		promotion = 0,
	}
	local attrData = table.wcopy(self:GetAirMarshalListConfig(no).attrs)
	local attrsConfig = self:GetAirMarshalAttrsConfig(listConfig.quality, 1)
	for _,v in pairs(listConfig.attrs) do
		table.insert(attrData, v)
	end
	self:UpdateBaseAttr({}, attrData, server.baseConfig.AttrRecord.Tianshen)
	local msg = {
		ret = true,
		no = no,
		lv = 1,
		upNum = 0,
		promotion = 0
	}
	--完成阵位↓
	self.player.position:AddClearNum(5, no)

	return msg
end

function Tianshen:UpLevel(no, autoBuy)
	local data = self.cache.list[no]
	if not data then return {ret = false} end
	local listConfig = self:GetAirMarshalListConfig(no)
	local lvproConfig = self:GetAirMarshalLvproConfig(listConfig.quality)
	if not lvproConfig[data.lv + 1] and data.upNum >= lvproConfig[data.lv].upnum then
		return {ret = false}
	end
	if not self.player:PayRewardsByShop(lvproConfig[data.lv].cost, self.YuanbaoRecordType, "tianshen", autoBuy) then return {ret = false} end
	data.upNum = data.upNum + 1
	-- if self.cache.upNum < proConfig.upnum then return end
	-- if self.cache.lv == maxLv then return end
	local newAttrs = table.wcopy(lvproConfig[data.lv].attrs or {})
	local oldAttrs = {}
	if data.upNum >= lvproConfig[data.lv].upnum then
		if lvproConfig[data.lv + 1] then
			local oldLv = data.lv
			data.upNum = data.upNum - lvproConfig[data.lv].upnum
			data.lv = data.lv + 1

			local oldAttrsConfig = self:GetAirMarshalAttrsConfig(listConfig.quality, oldLv)
			oldAttrs = oldAttrsConfig.attrs
			local newAttrsConfig = self:GetAirMarshalAttrsConfig(listConfig.quality, data.lv)
			for _,v in pairs(newAttrsConfig.attrs) do
				table.insert(newAttrs, v)
			end
		end
	end
	self:UpdateBaseAttr(oldAttrs, newAttrs, server.baseConfig.AttrRecord.Tianshen)
	local msg = {
		ret = true,
		no = no,
		lv = data.lv,
		upNum = data.upNum,
	}
	return msg
end

function Tianshen:Promotion(no)
	local data = self.cache.list[no]
	if not data then return {ret = false} end
	local breachConfig = self:GetAirMarshalBreachConfig(no)
	local nextLv = breachConfig[data.promotion + 1]
	if data.lv < nextLv.needlevel then return {ret = false} end
	if not self.player:PayRewards(nextLv.cost, self.YuanbaoRecordType) then return {ret = false} end
	local oldBreachConfig = breachConfig[data.promotion]
	data.promotion = data.promotion + 1
	local newBreachConfig = breachConfig[data.promotion]

	local oldAttrs = {}
	local oldSkilllist = {}
	if oldBreachConfig then 
		oldAttrs = oldBreachConfig.attrs or oldAttrs
		oldSkilllist = oldBreachConfig.skillid or oldSkilllist
	end
	self:UpdateBaseAttr(oldAttrs, newBreachConfig.attrs, server.baseConfig.AttrRecord.Tianshen)
	if self.cache.use == no then
		for k, v in pairs(oldBreachConfig.skillid or {}) do
			self:DelSkill(v, 1)
		end
		for _,v in pairs(newBreachConfig.skillid or {}) do
			self:AddSkill(v, true, 1)
		end
	end
	local msg = {
		ret = true,
		no = no,
		promotion = data.promotion,
	}
	return msg
end

function Tianshen:Fight(no, warType)
	if warType == 1 and self.cache.use == no then return {ret = false} end
	local newData = self.cache.list[no]
	if not newData then return {ret = false} end

	local oldNo = self.cache.use
	local oldData = self.cache.list[oldNo]
	local baseskill = self:GetAirMarshalBaseConfig("skill")
	local basehskill = self:GetAirMarshalBaseConfig("hskill")
	if self.cache.use ~= 0 then
		local oldSkill = self:GetAirMarshalBreachConfig(oldNo)[oldData.promotion].skillid or {}
		self.cache.use = 0
		for k,v in pairs(oldSkill) do
			self:DelSkill(v, 1)
		end
		self:DelSkill(baseskill, 1)
		self.player.role:DelSkill(basehskill, 1)
	end

	if warType == 1 then
		self.cache.use = no
		local newSkill = self:GetAirMarshalBreachConfig(no)[newData.promotion].skillid or {}
		for k,v in pairs(newSkill) do
			self:AddSkill(v, true, 1)
		end
		self:AddSkill(baseskill, true, 1)
		self.player.role:AddSkill(basehskill, true, 1)
	end
	local msg = {
		ret = true,
		use = self.cache.use,
		disuse = oldNo,
	}
	return msg
end

function Tianshen:UseDrug(useNum)
	local open = self:GetAirMarshalBaseConfig("open")
	local lv = server.configCenter.FuncOpenConfig[open].conditionnum
	if lv > self.player.cache.level then return {ret = false} end
	local propsNo = self:GetAirMarshalBaseConfig("attreitemid")
	local bag = self.player.bag
	if not self.player.bag:CheckItem(propsNo, useNum) then return {ret = false} end
	bag:DelItemByID(propsNo, useNum, self.YuanbaoRecordType)
	self.cache.drugNum = (self.cache.drugNum or 0) + useNum
	local attrs = table.wcopy(self:GetAirMarshalBaseConfig("attredata"))
	for _,v in pairs(attrs) do
		v.value = v.value * useNum
	end
	self:UpdateBaseAttr({}, attrs, server.baseConfig.AttrRecord.Tianshen)
	return {ret = true, drugNum = self.cache.drugNum}
end

server.playerCenter:SetEvent(Tianshen, "tianshen")
return Tianshen