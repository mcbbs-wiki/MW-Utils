# MW-Utils
MCBBS Wiki 对 MediaWiki 的增强和扩展。

支持 MediaWiki 版本：MediaWiki 1.37及以上
## 功能
* 自定义页脚文字
* UCenter 用户头像嵌入
* MCBBS 用户积分分析（依赖 [Highcharts](https://www.highcharts.com/) 和 [bbswiki-api](https://github.com/mcbbs-wiki/wiki-api)，在@Salt-lovely的积分分析 Widget 基础上修改）
* BiliBili 视频嵌入
## 安装
* 从 Release 下载压缩包，解压后将 `MCBBSWikiUtils` 文件夹放置到您的 `extensions/` 文件夹。
* 将下列代码放置在您的LocalSettings.php的底部：
```php
wfLoadExtension('MCBBSWikiUtils');
```
* 根据需要进行配置。
* 完成 – 在您的wiki上导航至Special:Version，以验证已成功安装扩展。
## 配置
```php
// 若希望使用 UCenter 头像嵌入功能，请指定 UCenter 的通信 URL。
//（注意 URL 末不带斜杠）
$wgUCenterURL='https://example.com/uc_server';

// 如：（以 MCBBS 为示例）
$wgUCenterURL='https://www.mcbbs.net/uc_server';
```
## 使用
### 自定义页脚文字
可以通过编辑 MediaWiki:Footerinfo 来自定义页面页脚。
### UCenter 用户头像嵌入
```html
<ucenter-avatar uid="UCenter 用户 UID" />
```
### MCBBS 用户积分分析
```html
<mcbbs-credit uid="MCBBS 用户 UID" />
```
### BiliBili 视频嵌入
```html
<bilibili bv="视频 BV 号（必填）" width="视频宽度（可选）" height="视频高度（可选）"/>
```