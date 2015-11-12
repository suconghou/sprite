<?php

/**
* icon 自动生成less/css 代码
*/
class sprite
{
	const route='\/([\w\-]{2,20})\/([\w\-]{2,32})\.(png|css|json|less)';
	const tStyle='style';
	private static $resource;

	function __construct($router)
	{

	}

	public function index($project=null,$name=null,$type=null)
	{
		if($project&&$name&&$type)
		{
			if(self::$resource=self::getData($project,$name))
			{
				if($type=='png')
				{
					return $this->outputPng();
				}
				else if($type=='css')
				{
					return $this->outputCss();
				}
				else if($type=='less')
				{
					return $this->outputLess();
				}
				else
				{
					return $this->outputMeta();
				}
			}
			return $this->notFound($project,$name,$type);
		}
		return $this->site();
	}

	function test()
	{
		$data=file_get_contents('/data/tmp/tmp.css');
		$data=self::makeLess($data);
		print_r($data);
		// if($data)
		// {
		// 	unset($data['class']);
		// 	$insertData=array_merge($data,array('project'=>$project,'name'=>$name,'comment'=>$comment,'createTime'=>time()));
		// 	$ret=self::setStyleData($insertData);
		// }
		// else
		// {

		// }
	}

	function site()
	{
		return template('sprite');
	}

	function project($project=null,$name=null)
	{
		if(!($project&&$name))
		{
			return $this->site();
		}
		if(self::$resource=self::getData($project,$name))
		{
			self::$resource['class']=json_decode(self::$resource['class'],true);
			return template('project',array('data'=>self::$resource));
		}
		else
		{
			return $this->notFound($project,$name);
		}
	}

	private static function notFound($project=null,$name=null,$type=null)
	{
		throw new Exception("{$project} {$name} {$type} not found",404);
	}

	public static function outputCss()
	{
		$css=&self::$resource['css'];
		header("Content-type: text/css");
		echo $css;
	}

	public static function outputPng()
	{
		$img=&self::$resource['img'];
		header("Content-type: image/png");
		echo base64_decode(substr($img,22));
	}
	
	public static function outputLess()
	{
		$less=&self::$resource['less'];
		header("Content-type: text/less");
		echo $less;
	}

	public static function outputMeta()
	{
		$data=&self::$resource;
		return json($data);
	}


	public static function makeLess($content)
	{
		$pattern="/(.[\w:\-]+)\s+{[\S\s]+?left:\s+(\d+)px;[\S\s]+?top:\s+(\d+)px;[\S\s]+?width:\s+(\d+)px;[\S\s]+?height:\s+(\d+)px;[\S\s]+?}/";
		if(preg_match_all($pattern,$content,$matches))
		{
			list($css,$classNames,$lefts,$tops,$widths,$heights)=$matches;
			$css=$less=$hovers=$actives=array();
			$allwidth=$allheight=0;
			foreach ($classNames as $index=>$item)
			{
				$child=false;
				if(stripos($item,':hover'))
				{
					$hoverClass=str_replace(':active','',str_replace(':hover','',$item));
					if(in_array($hoverClass,$classNames))
					{
						$child=true;
						$hovers[$hoverClass]=$index;
					}
				}
				if(stripos($item,':active'))
				{
					$activeClass=str_replace(':active','',str_replace(':hover','',$item));
					if(in_array($activeClass,$classNames))
					{
						$child=true;
						$actives[$activeClass]=$index;
					}
				}
				if($child)
				{
					unset($classNames[$index]);
				}
			}
			foreach ($classNames as $index=>$item)
			{
				$name=substr($item,1);
				$left=$lefts[$index]?"-{$lefts[$index]}px":0;
				$top=$tops[$index]?"-{$tops[$index]}px":0;
				$width=$widths[$index];
				$height=$heights[$index];
				if($index==0)
				{
					$allwidth=$width;
					$allheight=$height;
					$imgUrl="/static/img/icon.png";
					$tmp=array(
							".icon{",
							"\tdisplay: inline-block;",
							"\tvertical-align: middle;",
							"\tbackground-image: url({$imgUrl});",
							"\tbackground-repeat: no-repeat;"
						);
					$less[]=implode(PHP_EOL,$tmp);
					$css[]=".icon{display:inline-block;vertical-align:middle;background-image:url($imgUrl);background-repeat:no-repeat;}";
				}
				else
				{
					if(isset($actives[$item],$hovers[$item]))
					{
						$activeLeft=$lefts[$actives[$item]]?"-{$lefts[$actives[$item]]}px":0;
						$activeTop=$tops[$actives[$item]]?"-{$tops[$actives[$item]]}px":0;
						$hoverLeft=$lefts[$hovers[$item]]?"-{$lefts[$hovers[$item]]}px":0;
						$hoverTop=$tops[$hovers[$item]]?"-{$tops[$hovers[$item]]}px":0;
						if($activeLeft==$hoverLeft and $activeTop==$hoverTop)
						{
							$activeAndHover=array(
									"\t\t&.active,&:hover{",
									"\t\t\tbackground-position: {$activeLeft} {$activeTop};",
									"\t\t}",
									""
								);
							$activeAndHover=implode(PHP_EOL,$activeAndHover);
							$acthovCss=".icon.icon-{$name}.active,.icon.icon-{$name}:hover{background-position:{$activeLeft} {$activeTop};}";
						}
						else
						{
							$activeAndHover=array(
									"\t\t&.active{",
									"\t\t\tbackground-position: {$activeLeft} {$activeTop};",
									"\t\t}",
									"\t\t&:hover{",
									"\t\t\tbackground-position: {$hoverLeft} {$hoverTop};",
									"\t\t}",
									""
								);
							$activeAndHover=implode(PHP_EOL,$activeAndHover);
							$acthovCss=".icon.icon-{$name}.active{background-position: {$activeLeft} {$activeTop};} .icon.icon-{$name}:hover{background-position:{$hoverLeft} {$hoverTop};}";
						}
					}
					else if(isset($actives[$item]))
					{
						$activeLeft=$lefts[$actives[$item]]?"-{$lefts[$actives[$item]]}px":0;
						$activeTop=$tops[$actives[$item]]?"-{$tops[$actives[$item]]}px":0;
						$activeAndHover=array(
								"\t\t&.active{",
								"\t\t\tbackground-position: {$activeLeft} {$activeTop};",
								"\t\t}",
								""
							);
						$activeAndHover=implode(PHP_EOL,$activeAndHover);
						$acthovCss=".icon.icon-{$name}.active{background-position:{$activeLeft} {$activeTop};}";
					}
					else if(isset($hovers[$item]))
					{
						$hoverLeft=$lefts[$hovers[$item]]?"-{$lefts[$hovers[$item]]}px":0;
						$hoverTop=$tops[$hovers[$item]]?"-{$tops[$hovers[$item]]}px":0;
						$activeAndHover=array(
								"\t\t&:hover{",
								"\t\t\tbackground-position: {$hoverLeft} {$hoverTop};",
								"\t\t}",
								""
							);
						$activeAndHover=implode(PHP_EOL,$activeAndHover);
						$acthovCss=".icon.icon-{$name}:hover{background-position:{$hoverLeft} {$hoverTop};}";
					}
					else
					{
						$activeAndHover=null;
						$acthovCss=null;
					}
					
					$tmp=array(
							"\t&.icon-{$name}{",
							"\t\tbackground-position: {$left} {$top};",
							"\t\twidth: {$width}px;",
							"\t\theight: {$height}px;",
							"{$activeAndHover}\t}"
						);
					$less[]=implode(PHP_EOL,$tmp);
					$css[]=".icon.icon-{$name}{background-position:{$left} {$top};width:{$width}px;height:{$height}px;}".$acthovCss;
				}
			}
			$less[]="}";
			$less=implode(PHP_EOL,$less);
			$css=implode('',$css);
			array_shift($classNames);
			return array('less'=>$less,'css'=>$css,'class'=>$classNames,'width'=>$allwidth,'height'=>$allheight);
		}
		else
		{
			return false;
		}
	}


	public static function getData($project,$name)
	{
		$sql="SELECT * FROM ".self::tStyle." WHERE project='{$project}' AND name='{$name}' ";
		$resource=DB::getLine($sql);
		return $resource;
	}

	private static function setData($data)
	{
		$table=self::tStyle;
		$replace=array_merge($data,array('updateTime'=>time()));
		unset($replace['createTime']);
		$k=$v=array();
		foreach ($data as $key => $value)
		{
			$k[]='`'.$key.'`';
			$v[]=DB::quote($value);
		}
		$strv=implode(',',$v);
		$strk=implode(',',$k);
		$updateStr=array();
		foreach($replace as $key => $value)
		{
			$updateStr[]='`'.$key.'`='.DB::quote($value);
		}
		$updateStr=implode(',',$updateStr);
		$sql="INSERT INTO {$table} ({$strk}) VALUES ({$strv}) ON DUPLICATE KEY UPDATE {$updateStr}";
		return DB::runSql($sql)===false?false:true;
	}

	### API ###

	public static function prepare()
	{
		$project=Request::post('project');
		$name=Request::post('name');
		$img=Request::post('img');
		$css=Request::post('css');
		$comment=Request::post('comment',null,'xss');
		if($project&&$name&&$img&&$css)
		{
			if(preg_match('/^[\w\-]{2,20}$/',$project)&&preg_match('/^[\w\-]{2,32}$/',$name))
			{
				if(strlen($img)>102400)
				{
					return json(array('code'=>-4,'msg'=>'img is too large'));
				}
				$lessData=self::makeLess($css);
				if($lessData)
				{
					$data=array('project'=>$project,'name'=>$name,'img'=>$img,'comment'=>$comment,'lessData'=>$lessData);
					session('prepare-data',$data);
					return json(array('code'=>0,'msg'=>'success','class'=>&$lessData['class'],'width'=>&$lessData['width'],'height'=>&$lessData['height']));
				}
				return json(array('code'=>-3,'msg'=>'invalid css data'));
			}
			return json(array('code'=>-2,'msg'=>'invalid project or name'));
		}
		return json(array('code'=>-1,'msg'=>'error params'));

	}

	public static function store()
	{
		$data=json_decode(session('prepare-data'),true);
		if($data)
		{
			$time=time();
			$lessData=&$data['lessData'];
			$store=array('project'=>$data['project'],'name'=>$data['name'],'comment'=>$data['comment'],'createTime'=>$time);
			$store['img']=&$data['img'];
			$store['class']=json_encode($lessData['class'],JSON_UNESCAPED_UNICODE);
			$store['less']=str_replace('/static/img/icon.png',$data['img'],$lessData['less']);
			$store['css']=str_replace('/static/img/icon.png',$data['img'],$lessData['css']);
			if(self::setData($store))
			{
				session('prepare-data',null,true);
				return json(array('code'=>0,'msg'=>'success','data'=>array('project'=>$data['project'],'name'=>$data['name'])));
			}
			return json(array('code'=>-2,'msg'=>'store error'));
		}
		return json(array('code'=>-1,'msg'=>'error params'));
	}

	public static function check()
	{
		$project=Request::post('project');
		$name=Request::post('name');
		if(preg_match('/^[\w\-]{2,20}$/',$project)&&preg_match('/^[\w\-]{2,32}$/',$name))
		{
			$data=self::getData($project,$name);
			if($data)
			{
				return json(array('code'=>-2,'msg'=>'project or name already exists'));
			}
			return json(array('code'=>0,'msg'=>'ok'));
		}
		return json(array('code'=>-1,'msg'=>'invalid params'));
	}

}