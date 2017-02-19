<?php
/*
#2017-2-19
作者：超级行者
email:120266197@qq.com
website:http://www.imphper.cn
参考php-mysql封装php-oracle的方法
ORACLE操作封装
包含以下功能：
- 插入
- 删除
- 更新
- 查询
- 获取最新插入的ID
*/
//mysql_connect
//以下两个参数用户开发调试,发布后请置于FALSE;
define ('DEBUG_FLAG',0);//提示oraclebug开关
define ('SHOW_FULL_SQL',0);//查看该页面所有的SQL语句开关
/*
-Sample:
$link = oracle_connect('lms','lms','192.168.2.66/sid');
$sql = " SELECT * from tab";
$data = oracle_fetch_all($sql,$link);
print_r ($data);
*/
$user = DB_USER;
$psw =  DB_PSW;
$ip =   DB_HOST;
function oracle_connect($user,$psw,$ip,$charset='UTF-8'){
      if (!empty($user)&&!empty($psw)&&!empty($ip)&&!empty($charset)){
		  $link = oci_connect($user,$psw,$ip,$charset);
		  if (!$link){
			  $e = oci_error();
			  trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			  exit;
		  }
	  }
	  return $link;
}

############## select ##############
####################################
//mysql_fetch_all
function oracle_fetch_all($sql,$link){
      if (!empty($sql)&&!empty($link)){
		  if (SHOW_FULL_SQL){
		      echo $sql;
		  } 
		  $stmt = oci_parse($link,$sql);
		  $r = oci_execute($stmt);
		if (!$r ){
			oracle_error($stmt);
		}
		  while($row = oci_fetch_array($stmt,OCI_BOTH)){
			   $data[] = $row;
		  }
	  }
	  //oci_free_statement($stid);
	  return $data;
}
//mysql_fetch_row
function oracle_fetch_row($sql,$link){
		
		if (SHOW_FULL_SQL) echo $sql;
		if (!empty($sql)&&!empty($link)){
		   
					$stid = oci_parse($link, $sql); 
					$r = oci_execute($stid);
					if (!$r ){
						oracle_error($stid);
					}
					$data = oci_fetch_assoc($stid);
			
			oci_free_statement($stid);
		} else {
		  
		}
		return $data;
}
######### update/insert/delete #########
########################################
//mysql_query //commit
function oracle_query($sql,$link){
		if (SHOW_FULL_SQL){
		    echo $sql;
		} 
		if (!empty($sql)&&!empty($link)){
		
			$stmt = oci_parse($link,$sql);
			$r = oci_execute($stmt,OCI_DEFAULT);
			if (!$r ){
				$fp = fopen("logs",'ab');//打开文件开始激活
				$col = $sql."\r\n";
				fwrite($fp, $col, strlen($col));
				fclose($fp); //关闭文件
				oracle_error($stid);
			}
		}
		
		return $r;		
}

//得到最新的id
// SELECT SEQ_MT_USER_ID.currval FROM dual
//$a = SEQ_MT_USER_ID; 
function oracle_insert_id($a,$link){
	if (!empty($sql)&&!empty($link)){
		if (SHOW_FULL_SQL){
			echo $sql;
		}
	}
	$sql = "SELECT ".$a.".currval FROM dual";
	$idsource = oci_parse($link,$sql);
	$id = oci_execute($idsource,OCI_DEFAULT);
	if (!$id ){
			oracle_error($idsource);
	}
	$data = oci_fetch_assoc($idsource);
	return $data['CURRVAL'];
}

function oracle_commit($link){
	if (!empty($link)){
		$committed = oci_commit($link);
	}
	return $committed;
}
// mysql_error
function oracle_error($stid){
	if (!empty($stid)){
			$flag = DEBUG_FLAG;
			$e = oci_error($stid);
			$flag && trigger_error(htmlentities($e['message']), E_USER_ERROR);
	}
}
//roll_back
function oracle_rollback($link){
		if (!empty($link)){
				$r = oci_rollback($link);
		}
        return $r;
}

//oracle close
function oracle_close($link){
     return $link && oci_close($link);
}
// oracle 引号，双引号 转义
function oracle_escape_string($value){
	return  str_replace(array("\'",'\"'),array("''",'""'),$value);
}
?>
