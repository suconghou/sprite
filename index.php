<?php
/*************************************系统配置区*************************************/
define('DEBUG',getenv('debug'));
define('ROOT',__DIR__.DIRECTORY_SEPARATOR);
define('APP_PATH',ROOT.'app'.DIRECTORY_SEPARATOR);
define('VAR_PATH',ROOT.'var'.DIRECTORY_SEPARATOR);
define('LIB_PATH','/data/git/mvc/app/system/');
define('VIEW_PATH',APP_PATH.'view'.DIRECTORY_SEPARATOR);
define('MODEL_PATH',APP_PATH.'model'.DIRECTORY_SEPARATOR);
define('CONTROLLER_PATH',APP_PATH.'controller'.DIRECTORY_SEPARATOR);
require LIB_PATH.'core.php';
if(DEBUG)
{
	define('DB_DSN','mysql:host=172.168.1.3;port=13306;dbname=test1;charset=utf8');
	define('DB_USER','work');
	define('DB_PASS','123456');
	base::url('//127.0.0.1:8088');
}
else
{
	define('DB_DSN','mysql:unix_socket=/var/run/mysql.sock;dbname=blog;charset=utf8');
	define('DB_USER','root');
	define('DB_PASS','root');
	base::url('//static-files.coding.io');
}


// 开启如下配置可使用sendMail
// define('MAIL_SERVER','smtp.126.com');
// define('MAIL_USERNAME','suconghou@126.com');
// define('MAIL_PASSWORD','123456');
// define('MAIL_NAME','发送者名称');


/*************************************应用程序配置区*************************************/

app::route('\/([\w\-]{2,20})\/([\w\-]{2,32})\.(png|css|json|less)',array('sprite','index'));
app::route('\/?',array('sprite','site'));

base::version(1234);
base::project('sprite');
/*************************************应用程序配置区*************************************/

//配置完,可以启动啦!
app::start();

