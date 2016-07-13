<?php

require_once "connect.php";

/*** Распечатка массива ***/
function print_arr($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

/*** Фильтрация входящих данных из админки ***/
function clear_admin($var, $type="str"){

	global $link;
	switch ($type) {
		case "str" :
			$var = mysqli_real_escape_string($link, trim($var));
			break;
		case "int" :
			$var = abs((int)$var);
			break;
		case "order" :
			$var = htmlspecialchars(strip_tags(trim($var)));
			$var = mysqli_real_escape_string($link, $var);
			
	}
    return $var;
}

/*** Результат запроса в массив ***/
function db2arr($data, $field_id = "id_content"){
	global $link;
	$arr = array();
	while($row = mysqli_fetch_assoc($data)){
		$arr[$row[$field_id]] = $row;
	}
	return $arr;
}

/* ===Ресайз картинок=== */
function resize($target, $dest, $wmax, $hmax, $ext){
    /*
    $target - путь к оригинальному файлу
    $dest - путь сохранения обработанного файла
    $wmax - максимальная ширина
    $hmax - максимальная высота
    $ext - расширение файла
    */
    list($w_orig, $h_orig) = getimagesize($target);
    $ratio = $w_orig / $h_orig; // =1 - квадрат, <1 - альбомная, >1 - книжная

    if(($wmax / $hmax) > $ratio){
        $wmax = $hmax * $ratio;
    }else{
        $hmax = $wmax / $ratio;
    }
    
    $img = "";
    // imagecreatefromjpeg | imagecreatefromgif | imagecreatefrompng
    switch($ext){
        case("gif"):
            $img = imagecreatefromgif($target);
            break;
        case("png"):
            $img = imagecreatefrompng($target);
            break;
        default:
            $img = imagecreatefromjpeg($target);    
    }
    $newImg = imagecreatetruecolor($wmax, $hmax); // создаем оболочку для новой картинки
    
    if($ext == "png"){
        imagesavealpha($newImg, true); // сохранение альфа канала
        $transPng = imagecolorallocatealpha($newImg,0,0,0,127); // добавляем прозрачность
        imagefill($newImg, 0, 0, $transPng); // заливка  
    }
    
    imagecopyresampled($newImg, $img, 0, 0, 0, 0, $wmax, $hmax, $w_orig, $h_orig); // копируем и ресайзим изображение
    switch($ext){
        case("gif"):
            imagegif($newImg, $dest);
            break;
        case("png"):
            imagepng($newImg, $dest);
            break;
        default:
            imagejpeg($newImg, $dest);    
    }
    imagedestroy($newImg);
}

function show_comments() {
	$comments = get_comments();
	return $comments;
}

/***
* Запрос всех комментариев для вывода
* при загрузке страницы
***/
function get_comments() {
	
	global $link;

	$sql = "SELECT * FROM tree_comments ORDER BY addtime DESC ";
	
	$res = mysqli_query($link, $sql);
	
	return db2arr($res, "id");
	
}

/********
 * Функция для формирования иерархического дерева
 ********/
function build_tree($data){
    
    $tree = array();
    
    foreach($data as $id => &$row){
    
        if(empty($row['parent_id'])){
            
            $tree[$id] = &$row;
        }
        else{
            $data[$row['parent_id']]['childs'][$id] = &$row;
        }
    }
    
    return $tree;
}

/**
* Загрузка шаблона для вывода комментариев
**/
function getCommentsTemplate($comments, $pid = 0){
    
    $html = '';
    foreach($comments as $comment){
        ob_start(); 
        include 'views/comments_template.php';          
        $html .= ob_get_clean();
    }
    
    return $html;
}

/*jquery.modal-window*/

/*** Генерация шаблона. ***/
function template($fileName, $vars = array()) 
{ 
    // Установка переменных для шаблона. 
	if (count($vars > 0)) {	
		foreach ($vars as $k => $v) { 
			$$k = $v; 
		}
	}
	
	//extract($vars);
	
    // Генерация HTML в строку. 
    ob_start(); 
    include $fileName; 
    return ob_get_clean();     
}

function loadCommentsForm() {
	sleep(1);
	//exit("cart_form");
	
	$error = false;
	
	//$quantity = $_SESSION['init_cart']['quantity'];
	//$sum = number_format($_SESSION['init_cart']['sum'], 0, '', ' ');
	
	/*ob_start();
	include_once("views/form_cart.php");
	$form = ob_get_contents();
	ob_clean();*/
	
	//Подключение шаблона формы заказа
	$form = template('views/form_comments.php');

	exit(json_encode(array("error"=>$error, "form"=>$form)));
	//exit($form);
	
}

function getCommentsProcess($comments_data) {
	
	//print_arr($comments_data);
	//$comments_data['cinput_sess'] = $_SESSION['captcha_keystring'];
	//exit(print_arr($comments_data));
	
	//exit($_POST['order_type']);
	//sleep(1);
	global $link;
	$error = false; //Нет ошибок
	$err_fields = array(); //Поля в которых ошибки
	$array_mess = array(); //Массив со всеми сообщениями
	$req_fields = array("cinput", "cart_customer", "cart_comment"/*, "cart_email"*/); //Обязательные поля для проверки
	
	if ($comments_data['post_id'] < 1) {
		exit(json_encode(array("error" => true, "errorComment" => true)));
	}
	
	if (!empty($comments_data['cinput']) && ($comments_data['cinput'] !== $_SESSION['captcha_keystring'])) {
		$error = true;
		$mess = "Неверный код";
		//exit(json_encode(array("error" => $error, "mess" => $mess, "fields" => array(1 => "email"))));
		$array_mess[] = array("mess" => $mess, "fields" => array(1 => "cinput"));
		//logWriter($mess." - ".$arrOrderData['cinput']);
	}
	
	//============ Проверка email ===========
	if (!empty($comments_data['cart_email'])) {
		if(!preg_match("#^([a-z0-9_\-\.])+@([a-z0-9_\-\.])+\.([a-z0-9])+(\.([a-z0-9])+)?$#i", $arrOrderData['cart_email']) OR (strlen($arrOrderData['cart_email']) > 50)) {
			$error = true;
			$mess = "Некорректный e-mail";
			//exit(json_encode(array("error" => $error, "mess" => $mess, "fields" => array(1 => "email"))));
			$array_mess[] = array("mess" => $mess, "fields" => array(1 => "cart_email"));
			//logWriter($mess." - ".$arrOrderData['cart_email']);
		}
	}
		
	//=========== Проверка на пустые поля ===========
	foreach ($comments_data as $key => $val) {
		if (empty($val) && in_array($key, $req_fields)) {
			$err_fields[] = $key;
			//logWriter("Пустое поле - ". $key);
		}
	}
	
	if (count($err_fields) > 0) { //Если есть пустые поля
		$error = true;
		$mess = "Заполните поле";
		//exit(json_encode(array("error" => $error, "mess" => $mess, "fields" => $err_fields)));
		$array_mess[] = array("mess" => $mess, "fields" => $err_fields);
	}
	
	if ($error) { //Если есть ошибки при валидации, выводим сообщения для инпутов
	
		exit(json_encode(array("error" => $error, "arrMess" => $array_mess )));
	
	} else {
		
		//Экранируем кавычки при добавлении в БД
		$comments_data['cart_customer'] = clear_admin($comments_data['cart_customer'], "order");
		$comments_data['cart_comment'] = clear_admin($comments_data['cart_comment'], "order");
		$comments_data['cart_email'] = clear_admin($comments_data['cart_email'], "order");
		
		//Определяем существование сообщений у предка
		$sql = "select count(*) from ".DB_PRE."comments WHERE parent_id=$comments_data[parent_id]";
		$result = mysqli_query($link, $sql);
		$row = mysqli_fetch_assoc($result); // Определяем общее число записей в базе данных 
		$comments_cnt = $row['count(*)'];
		//exit($comments_cnt);
		
		//Добавляем комментарий в БД и получаем ID нового сообщения
		$addCommentsId = addComments($comments_data);
		
		if ($addCommentsId) {//Если комментарий добавлен и существует ID
			
			//Получаем из БД новое сообщение в виде массива
			$comment_arr = getOneComment($addCommentsId);
			
			//Формируем массив переменных для вывода в шаблоне нового комментария
			foreach ($comment_arr as $comment)
				$comment_vars = array(
							'comment_id' => $comment['id'],
							'parent_id' => $comment['parent_id'],
							'post_id' => $comment['post_id'],
							'author' => $comment['author'],
							'comment' => $comment['comment'],
							'addtime' => $comment['addtime']
						);
			
			//exit(print_arr($comment_vars));
			//$filename = ($comments_cnt > 0) ? "append_li" : "append_ul";
			
			//Определяем какой шаблон подключать
			//Если нет родителя и дочерних сообщений
			if (($comment['parent_id'] == 0) && ($comments_cnt > 0)) {
				$filename = "append_li";
				$append = null;
			} elseif ($comments_cnt == 0) {
				$filename = "append_ul";
				$append = "ul";
			} elseif ($comments_cnt > 0) {
				$filename = "append_li";
				$append = "li";
			}
			
			//if ($comment['parent_id'] == 0) 
			
			$cid_link = "post-".$comment['post_id']."_cid-".$comment['id'];
			$comment_html = template('views/comments/'.$filename.'.php', $comment_vars);
			
			exit(json_encode(array(
									"error" => false, //Нет ошибок при добавлении комментарияя
									"html" => $comment_html, //HTML нового комментария
									"append" => $append, //Переключатеть для определения каким образом вставлять новый комментарий
															//null - новый комментарий без предка
															//ul - первый ком-рий предка добавляется в <ul>
															//li - добавляется в существующий <ul> если у предка есть уже другие ком-рии
									"link" => "comment-container-".$comment_vars['parent_id'], //html-контейнер куда вставлять новый комментарий
									"linkID" => $cid_link //id ссылки, чтобы повесить событие ответа для нового, добавленного комментария
									)
							)
				);
				
		} else {
			
			exit(json_encode(array(
									"error" => true, 
									"errorComment" => true, 
									"mess" => "Ошибка при добавлении комментария.\nПопробуйте позже!"
									)
							)
				);
		
		}
		
	}
}

function getOneComment($id) {
	//exit($id."***");
	global $link;
	$sql = "select * from ".DB_PRE."comments WHERE id=$id";
	$row = mysqli_query($link, $sql);
	return db2arr($row, "id");
	
}

function addComments($comments_data) {
	
	global $link;

	$sql = "INSERT INTO ".DB_PRE."comments(parent_id, post_id, `author`, `comment`, `addtime`) VALUES($comments_data[parent_id], $comments_data[post_id], '$comments_data[cart_customer]', '$comments_data[cart_comment]', NOW())";
			
	if (mysqli_query($link, $sql)) {
		//exit(mysqli_insert_id($link)."*****");
		return mysqli_insert_id($link);
	} else {
		return false;
	}

}

//Загрузка картинок ajax
function upload_gallery_img($id) {
	global $link;
	$uploaddir = '../'.PHOTOS;
	$uploadthumb = '../'.THUMB;
    $file = $_FILES['userfile']['name'];
    $ext = strtolower(preg_replace("#.+\.([a-z]+)$#i", "$1", $file)); // расширение картинки
    $types = array("image/gif", "image/png", "image/jpeg", "image/pjpeg", "image/x-png"); // массив допустимых расширений
    
    if($_FILES['userfile']['size'] > 1048576){
        $res = array("answer" => "Ошибка! Максимальный вес файла - 1 Мб!");
        exit(json_encode($res));
    }
    
    if($_FILES['userfile']['error']){
        $res = array("answer" => "Ошибка! Возможно, файл слишком большой.");
        exit(json_encode($res));
    }
    
    if(!in_array($_FILES['userfile']['type'], $types)){
        $res = array("answer" => "Допустимые расширения - .gif, .jpg, .png");
        exit(json_encode($res));
    }
    
    $query = "SELECT img_gallery FROM ".DB_PRE."content WHERE id_content=$id";
    $res = mysqli_query($link, $query) or die(mysqli_error($link)."<".__LINE__.">");
    $row = mysqli_fetch_assoc($res);
    if($row['img_gallery']){
        // если есть картинки в галерее
        $images = explode("|", $row['img_gallery']);
        $lastimg = end($images);
        // получаем номер последней картинки
        $lastnum = preg_replace("#\d+_(\d+)\.\w+#", "$1", $lastimg); // 1_1.ext
        $lastnum += 1;
        $newimg = "{$id}_{$lastnum}.{$ext}"; // имя новой картинки
        $images = "{$row['img_gallery']}|{$newimg}"; // строка для записи в БД
    }else{
        $newimg = "{$id}_0.{$ext}"; // имя новой картинки
        $images = $newimg; // строка для записи в БД
    }
    
    $uploadfile = $uploaddir.$newimg;
    if(@move_uploaded_file($_FILES['userfile']['tmp_name'], "../images/content/photos/".$newimg)){
        resize("../images/content/photos/".$newimg, "../images/content/thumb/".$newimg, 150, 150, $ext);
        mysqli_query($link, "UPDATE ".DB_PRE."content SET img_gallery='$images' WHERE id_content = $id") or die(mysqli_error($link)."<".__LINE__.">");
        $res = array("answer" => "OK", "file" => $newimg);
        exit(json_encode($res));
    }
	
	//$res = array("answer" => "uploaddir:".$uploaddir."| uploadthumb:".$uploadthumb."| $images");
    //exit(json_encode($res));
}

//Удадление картинок ajax
function del_img() {
	
	global $link;
	
	$content_id = (int)$_POST['content_id'];
    $img = clear_admin($_POST['img']);
    $rel = (int)$_POST['rel'];
	
	if(!$rel){
        // если удаляется базовая картинка
        $sql = "UPDATE ".DB_PRE."content SET img_content= '' WHERE id_content=$content_id";
        if(mysqli_query($link, $sql) or die(mysql_error())){
			@unlink("../".IMG_BASE.$img);
			@unlink("../".PHOTOS.$img);
            return true;
        }else{
            return false;
        }
	}else{
        // если удаляется картинка галереи
        $query = "SELECT img_gallery FROM ".DB_PRE."content WHERE id_content=$content_id";
        $res = mysqli_query($link, $query);
		//var_dump($res); exit();
        $row = mysqli_fetch_assoc($res);
        // получаем картинки в массив
        $images = explode("|", $row['img_gallery']);
        foreach($images as $item){
            // пропускаем удаляемую картинку
            if($item == $img) continue;
            // формируем строку с картинками
            if(!isset($galleryfiles)){
                $galleryfiles = $item;
            }else{
                $galleryfiles .= "|$item";
            }
        }
		
        //exit($galleryfiles);
		$sql = "UPDATE ".DB_PRE."content SET img_gallery='$galleryfiles' WHERE id_content=$content_id";
		if(mysqli_query($link, $sql) or die(mysqli_error($link)."<".__LINE__.">")){
			@unlink("../".THUMB.$img);
			@unlink("../".PHOTOS.$img);
            return true;
        }else{
            return false;
        }
    }
	
}