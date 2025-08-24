![](https://files.mdnice.com/user/39773/dc2143d7-422a-4fe3-8bcb-692e8c6cbd9a.png)

<p align="center">
<img alt="GitHub" src="https://img.shields.io/github/license/outtimes/dujiaoka?style=for-the-badge">
<img alt="GitHub tag (latest by date)" src="https://img.shields.io/github/v/tag/outtimes/dujiaoka?label=version&style=for-the-badge">
<img alt="PHP Version" src="https://img.shields.io/static/v1?label=PHP&message=8.2%2B&style=for-the-badge">
<img alt="Laravel Version" src="https://img.shields.io/static/v1?label=Laravel&message=12.x&style=for-the-badge&color=red">
<img alt="Telegram" src="https://img.shields.io/static/v1?label=Telegram&logo=Telegram&message=@dujiaoka&style=for-the-badge&color=blue&&link=https://t.me/dujiaoka">
</p>

# :warning: 开发版本声明
**本版本为重构版本，正在积极开发中，不建议用于生产环境**  
**仅供技术研究和功能预览，目前需全新安装部署，后期将会推出迁移工具**

## :rocket: 架构升级
本项目基于[独角数卡](https://github.com/assimon/dujiaoka)进行深度重构和功能扩展：

- 升级框架至 **Laravel 12**
- 使用 **Filament 3** 作为后台管理系统
- 以及超多新增功能与优化

## :sparkles: 部分功能特性

### 用户系统
- 完整的用户注册/登录体系
- 基于消费实现的用户等级与折扣系统
- 用户下单历史

### 商品管理
- 多规格商品支持
- 下单库存模式可选
- 登录购买限制
- 自选卡密功能

### 订单系统
- 购物车批量下单
- IP并发订单限制

### 支付系统
- 支付通道费率配置
- 单商品支付方式限制  

## :open_book: 技术依赖

### 核心框架
- **后端框架**: [Laravel 12.x](https://github.com/laravel/laravel)
- **管理后台**: [Filament 3.x](https://filamentphp.com/)
- **支付集成**: [yansongda/Pay](https://github.com/yansongda/pay)
- **区块链支付**: [Tokenpay](https://github.com/LightCountry/TokenPay)

### 数据与服务
- **地理数据**: [GeoLite2](https://dev.maxmind.com/geoip/geolite2-free-geolocation-data)
- **缓存系统**: Redis
- **队列处理**: Laravel Queues
- **文件存储**: Laravel Storage

### 项目原版作者：
- [Assimon](https://github.com/assimon)

#### 核心贡献者：
- [iLay1678](https://github.com/iLay1678)

#### 模板贡献者：
- [Riniba](https://t.me/riniba) 默认模板作者

鸣谢以上开源项目及贡献者，排名不分先后。

## :gear: 部署要求

### 服务器环境
- **操作系统**: Linux (推荐Ubuntu 20.04+/CentOS 8+)
- **Web服务器**: Nginx 1.18+ 或 Apache 2.4+
- **数据库**: MySQL 8.0+ 或 MariaDB 10.6+
- **缓存**: Redis 6.0+
- **PHP版本**: 8.2+ (必需)

### PHP扩展要求
- **必需扩展**: `fileinfo`, `redis`, `gd`, `curl`, `zip`, `xml`, `mbstring`
- **系统函数**: `putenv`, `proc_open`, `pcntl_signal`, `pcntl_alarm`
- **推荐扩展**: `opcache`, `imagick`

### 技术要求
- 具备Linux服务器基础运维知识
- 理解Laravel框架部署流程
- 熟悉Composer依赖管理
- 了解Redis配置和使用

## :speech_balloon:使用交流
- 原作者的[Telegram群组](https://t.me/dujiaoka)
- 原作者的[Telegram官方频道](https://t.me/dujiaoshuka)

## :eye_speech_bubble:相关推荐
- [两米商店 2MStore](https://buy.2m.pub)
> 以下为原作者推荐
- （🇭🇰香港三网(电信/移动/联通)直连优化VPS，CN2优化网络大带宽低至35RMB/每月）[👉🏻点我直达](https://www.vkvm.info/cart?action=configureproduct&pid=146&aff=ECRPONNJ)
- （🇺🇸美国免备案vps，配置2核2G仅需`20.98$`≈`145RMB`一年/支持支付宝付款）[👉🏻点我直达](https://my.racknerd.com/aff.php?aff=2745&pid=681)

## :open_mouth:快速预览
![](https://files.mdnice.com/user/39773/0abbadfa-ef39-492b-bbc0-ac74b78e6a64.png)

![](https://files.mdnice.com/user/39773/8d72ecb8-c860-4d05-93c3-3691e786b05a.png)

![](https://files.mdnice.com/user/39773/c712dd5a-d987-4fd4-a84c-ed2244579c1c.png)

![](https://files.mdnice.com/user/39773/554c51e0-563f-4176-91ed-5ec4e0478c1c.png)

![](https://files.mdnice.com/user/39773/e43c9d40-1a03-4821-9e98-d285fa1ce6bd.png)

![](https://files.mdnice.com/user/39773/978342a2-15f7-477c-85c3-d6aac8a06e63.png)

![](https://files.mdnice.com/user/39773/0d9494c7-9cbe-4dea-b168-05f36d55273c.png)

## :book: 文档和教程

### 官方文档
- [系统安装指南](https://github.com/outtimes/dujiaoka/wiki/installation)
- [配置说明文档](https://github.com/outtimes/dujiaoka/wiki/configuration)
- [API接口文档](https://github.com/outtimes/dujiaoka/wiki/api)
- [主题开发指南](https://github.com/outtimes/dujiaoka/wiki/theme-development)

### 参考资料（原版）
- [Linux环境安装](https://github.com/assimon/dujiaoka/wiki/linux_install)
- [支付配置说明](https://github.com/assimon/dujiaoka/wiki/problems#各支付对应配置)
- [常见问题解答](https://github.com/assimon/dujiaoka/wiki/problems)

**注意**: 本版本架构已升级，请以本仓库Wiki文档为准

## :bank:支持的支付接口
- [x] 支付宝当面付、PC网站、手机网站
- [x] 微信Native、H5、小程序
- [x] Payjs
- [x] 码支付(QQ/支付宝/微信)
- [x] [Paypal支付(默认美元)](https://www.paypal.com)
- [x] V免签支付
- [x] 全网易支付支持(通用彩虹版)
- [x] [stripe](https://stripe.com/)

## :shield: 安全配置

### 默认管理员信息
**部署完成后请立即修改以下默认配置:**

- **后台访问路径**: `/admin`
- **默认管理员账号**: `admin`
- **默认管理员密码**: `admin`

### 安全建议
- 修改默认管理员密码为强密码
- 启用二步验证（如支持）
- 定期更新系统和依赖
- 配置防火墙限制管理后台访问
- 开启HTTPS并配置HSTS头

## :eyes:免责声明

独角数卡是一款用于学习PHP搭建自动化销售系统的程序案例，仅供学习交流使用。
严禁用于用于任何违反`中华人民共和国(含台湾省)`或`使用者所在地区`法律法规的用途。      
因为作者即本人仅完成代码的开发和开源活动`(开源即任何人都可以下载使用)`，从未参与用户的任何运营和盈利活动。    
且不知晓用户后续将`程序源代码`用于何种用途，故用户使用过程中所带来的任何法律责任即由用户自己承担。      

## :raised_hands:License

独角数卡 DJK Inc [MIT license](https://opensource.org/licenses/MIT).

This product includes GeoLite2 data created by MaxMind, available from
[https://www.maxmind.com](https://www.maxmind.com)

