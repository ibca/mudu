# mudu
目睹直播 API for Laravel

Step1: 安装, `composer require  "ibca/mudu:1.0.x@dev"`

Step2: 注册Provider `Ibca\Mudu\MuduServiceProvider` 到`config/app.php` 配置文件:

```
'providers' => [
    .....
    Ibca\Mudu\MuduServiceProvider::class,
],

```

也可以, 添加 `Mudu` Facades门面 到配置文件的 `aliases` 数组里:

```
    'Mudu' => Ibca\Mudu\Facades\Mudu::class,
```

Step3: 生成`config/mudu.php`

```
    php artisan vendor:publish
```

配置文件:

```

<?php

return [
    'access_token' => env('MUDU_ACCESS_TOKEN'),
    'api_url' => env('MUDU_API_URL')
];

```

在.env文件里面配置(目睹直播MUDU_ACCESS_TOKEN(专业版获取),目睹直播api域名地址:api_url):

`MUDU_ACCESS_TOKEN,MUDU_API_URL`

Step4: 使用


```
>>> use Mudu;
>>> Mudu::getActivities();
=> {
     "activities": [
       {
         "id": 1928,
         "name": "测试",
         "create_at": "2016-01-06 16:42:26",
         "live_status": 0,
         "watch_url": {
           "pc": "http://mudu.tv/watch/42776",
           "mobile": "http://mudu.tv/?c=activity&a=live&id=1928"
         },
         "embed_url": "http://mudu.tv/?a=index&c=show&id=1928&type=mobile",
         "page": {
           "start_time": "",
           "logo": "http://cdn13.mudu.tv/assets/upload/146467764819385.png",
           "pc_logo": "http://cdn13.mudu.tv/assets/upload/146467764819385.png",
           "mobile_logo": "http://cdn13.mudu.tv/assets/upload/146467764819385.png",
           "banner": "http://mudu.tv/assets/img/activity/pc/banner.jpg",
           "cover_image": "http://mudu.tv/assets/img/activity/pc/logo.png",
           "live_img": "http://mudu.tv/assets/console/images/livecoverimg.jpg",
           "footer": "技术支持：目睹直播技术开发团队"
         },
         "rtmp_publish_addr": "rtmp://video.mudu.tv/mudu_dev/dfads6",
         "hls_play_addr": "http://live.mudu.tv/watch/dfads6.m3u8",
         "rtmp_play_addr": "rtmp://live.mudu.tv/watch/dfads6",
         "manager": 1,
         "manager_username": "频道管理员A",
         "last_push_stream_at": "2016-11-11 14:48:54",
       }
     ],
     "meta": {
           "total": 1,
           "page": 1,
           "current": 1
     },
      "links": {
          "next_url": null,
          "end_url": "http://api.mudu.com/v1/activities?&p=1"
      }
   }
>>>

```

更多方法->`vendor\ibca\mudu\MuduAPI`,

