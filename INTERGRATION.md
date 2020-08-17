### 签名计算

步骤:

1. 组装请求数据字符串, 获得`DATA`
2. 按参数顺序,用中竖线“|”分割不同的参数(头尾无需中竖线), 获得`RAW_STR`
3. 执行 `HMAC_HASH('sha1', '{RAW_STR}', '{SECRET}')`
4. 输出摘要的小写16进制字符串形式作为签名

签名参数及顺序:

1. APPID: 应用 ID
2. SECRET: 密钥
3. TIME: 签名UNIX时间戳(秒)
4. METHOD: 请求方法, 如: get、post
5. PATH: 请求路径(不含主机名、端口号), 如: api/users
6. DATA: 数据(无需任何URL转义)
7. NONCE: 随机数

请求数据字符串:

* 按 KEY 的 ASCII 码正序排序
* 遍历排序的 KEY 数组, 拼接为 KEY:VALUE
* 如果 VALUE 是 Map, 递归生成 VALUE 的请求字符串, 拼接为: KEY:[VALUE]
* 如果 VALUE 是 Array, 视为 KEY 是整数的 Map, 执行 Map 的逻辑
* KEY:VALUE 组之间用分号“;”分割
* 例如
  * 输入: `b=1&c=2&a[]=3&a[]=4&d[a]=5&d[b]=6` 或 `{"b": 1, "c": 2, "a": [3,4], "d": {"a":4, "b":5 }}`
  * 输出: `a:[0:3;1:4];b:1;c:2;d:[a:5;d:6]`

PHP 示例代码:

```php
function sign($appId, $secret, $timestamp, $method, $path, $nonce, $data): string
{
    $signArr = [
        $appId,
        $secret,
        $timestamp,
        strtolower($method),
        strtolower($path),
        arr2str($data),
        $nonce,
    ];

    $raw = join('|', $signArr);
    $sign = hash_hmac('sha1', $raw, $secret);

    return $sign;
}

function arr2str(?array &$data)
{
    if (!$data) {
        return '';
    }

    $str = [];

    ksort($data);

    foreach ($data as $i => &$v) {
        $str[] = "{$i}:" . (is_array($v) ? '[' . arr2str($v) . ']' : $v);
    }

    return join(';', $str);
}

$s = sign('tFVzAUy07VIj2p8v', 'u4JsCDCwCUakBCVn', '1574661278', 'GET', 'api/users', '7o2jpms6l8ep', [
    "b" => 1,
    "c" => 2,
    "a" => [3,4],
    "d" => ["a" => 5, "b" => 6]
]);

assert("ddf8d0d008a12fc20a7c8713707886c2d814a7f7" === $s);
```

### 请求接口

请求参数 (Header):

* X-SIGN-APP-ID: APP ID
* X-SIGN-TIME: 签名UNIX时间戳(秒)
* X-SIGN-NONCE: 签名随机数
* X-SIGN: 签名

PHP 示例代码
```php
$client = new \GuzzleHttp\Client(['base_uri' => env('RPC_SERVER')]);

$appId = 'your app ID';
$secret = 'app secret';
$timestamp = time();
$query = ['page' => 1, 'page_size' => 20];
$method = 'GET';
$path = 'api/users';
$nonce = 'l9i7a4sh9';

$sign = sign($appId, $secret, $timestamp, $method, $path, $nonce, arr2str($query));

$res = $client->request($method, $path . '?' . http_build_query($query), [
    'headers' => [
        'Accept'        => "application/json",
        'X-SIGN-APP-ID' => $appId,
        'X-SIGN'        => $sign,
        'X-SIGN-TIME'   => $timestamp,
        'X-SIGN-NONCE'  => $nonce,
    ]
]);
```
### Java 示例代码
其他工具类涉及到 hutool工具类，详情 https://hutool.cn/docs/
```java
public JSONObject request(HttpServletRequest httpServletRequest) throws UNIException {
  String method = httpServletRequest.getMethod().toLowerCase();
  String requestURI = httpServletRequest.getRequestURI();
  Map<String, String[]> parameterMap = httpServletRequest.getParameterMap();
  //时间戳
  String timeStamp = DateUtil.currentSeconds() + "";
  //随机字符串
  String nonce = RandomUtil.randomString(10);
  //请求路径
  String path = uni + requestURI;
  ArrayList signArr = new ArrayList();
  signArr.add(appId);
  signArr.add(secret);
  signArr.add(timeStamp);
  signArr.add(method.toLowerCase());
  signArr.add(requestURI.toLowerCase());
  signArr.add(SignatureUtil.arr2str(parameterMap));
  signArr.add(nonce);
  String join = CollUtil.join(signArr, "|");
  HMac hmac = SecureUtil.hmac(HmacAlgorithm.HmacSHA1, secret);
  String digestHex = hmac.digestHex(join);
  Map pMap = SignatureUtil.getMap(parameterMap);
  HttpResponse execute = SignatureUtil.getMethod(path, method, pMap)
    .header("X-SIGN-APP-ID", appId)
    .header("X-SIGN", digestHex)
    .header("X-SIGN-TIME", timeStamp)
    .header("X-SIGN-NONCE", nonce)
    .execute();
  return getJSONObject(execute);
}

/**
 * 遍历参数，组装成字符串
 *
 * @param params
 * @return
 */
public static String arr2str(Map<String, String[]> params) {
  StringBuilder sb = new StringBuilder();
  if (!CollectionUtils.isEmpty(params)) {
    params.entrySet()
      .stream()
      .sorted(Map.Entry.comparingByKey())
      .forEach(paramEntry -> {
        String paramValue = String.join(";", Arrays.stream(paramEntry.getValue()).sorted().toArray(String[]::new));
        sb.append(paramEntry.getKey()).append(":").append(paramValue).append(';');
      });
  } else {
    return "";
  }
  String string = sb.toString();
  String trimEnd = StrUtil.removeSuffix(string, ";");
  return trimEnd;
}

/**
 * 组装参数成map
 *
 * @param parameterMap
 * @return
 */
public static Map getMap(Map<String, String[]> parameterMap) {
  Map pMap = new HashMap();
  Iterator entries = parameterMap.entrySet().iterator();
  Map.Entry entry;
  String name = "";
  String value = "";
  while (entries.hasNext()) {
    entry = (Map.Entry) entries.next();
    name = (String) entry.getKey();
    Object valueObj = entry.getValue();
    if (null == valueObj) {
      value = "";
    } else if (valueObj instanceof String[]) {
      String[] values = (String[]) valueObj;
      for (int i = 0; i < values.length; i++) {
        value = values[i] + ",";
      }
      value = value.substring(0, value.length() - 1);
    } else {
      value = valueObj.toString();
    }
    pMap.put(name, value.trim());
  }
  return pMap;
}
```
