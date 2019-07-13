<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|


Route::get('/', function () {
    return view('welcome');
});
*/

Route::get('/app/download',function(Request $request) {
	$android_url = 'https://vjshop.venjong.com/vjshop.apk';
	$iphone_url = 'https://itunes.apple.com/cn/app/bu-luo-chong-tu-huang-shi/id1053012308?mt=8';

	$agent = $request->Server('HTTP_USER_AGENT');

	$regex_iphone = '/.*i[pP]hone.*|.*i[pP]hone.*Safari.*/';
	$regex_android = '/.*[Aa]ndroid.*/';

	if(preg_match($regex_iphone, $agent)) {
		return redirect()->away($iphone_url);
	} else if(preg_match($regex_android, $agent)) {
		return redirect()->away($android_url);
	} else {
		return '<h2>提示：</h2>' . 
			   '<p>本APP只支持iPhone和Android手机，不支持其它设备。<p>' .
			   '<p>进一步信息请致电：0512-36620686；或发送邮件至：AM@venjong.com<p>' .
			   '<br>' .
			   '<p>昆山稳卓汽车配件有限公司<p>' .
               '<p>http://www.venjong.com<p>';
	}
});